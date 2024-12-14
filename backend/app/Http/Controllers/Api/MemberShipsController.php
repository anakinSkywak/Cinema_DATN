<?php

namespace App\Http\Controllers\Api;

use App\Models\Membership;
use App\Models\MemberShips;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\MembershipNotification;
use Illuminate\Support\Facades\Mail;

class MemberShipsController extends Controller
{
    /**
     * Hiển thị danh sách tất cả thẻ hội viên.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        // Lấy tất cả dữ liệu từ bảng Membership
        $data = Membership::with('registerMember')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Membership nào'
            ], 200);
        }

        // Dữ liệu đã có status tự động qua Accessor
        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }

    public function show()
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để xem thông tin thẻ hội viên!'
            ], 401);
        }

        // Lấy user_id của người dùng đã đăng nhập
        $user_id = auth()->user()->id;

        // Lấy thông tin thẻ hội viên của người dùng
        $membership = Membership::with('registerMember')
            ->whereHas('registerMember', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->first(); // Không cần phải so sánh với `$id` nữa

        // Kiểm tra nếu thẻ hội viên tồn tại
        if (!$membership) {
            return response()->json(['message' => 'Thẻ hội viên không tồn tại hoặc không phải của bạn!'], 404);
        }

        // Kiểm tra trạng thái thẻ hội viên
        $currentDate = Carbon::now();  // Ngày hiện tại
        $expirationDate = Carbon::parse($membership->ngay_het_han);  // Ngày hết hạn

        // Kiểm tra nếu thẻ đã hết hạn
        if ($expirationDate->isBefore($currentDate)) {
            $membership->trang_thai = 1; // Thẻ đã hết hạn
            $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";
        } else {
            // Kiểm tra thẻ sắp hết hạn (trong vòng 2 ngày)
            if ($expirationDate->diffInDays($currentDate) <= 2) {
                $membership->renewal_message = "Thẻ hội viên sắp hết hạn!!!. Vui lòng gia hạn thẻ!";
            } else {
                // Thẻ còn thời gian sử dụng
                $membership->renewal_message = "Thẻ còn thời gian sử dụng.";
            }
            $membership->trang_thai = 0;  // Thẻ còn hiệu lực
        }


        // Cập nhật trạng thái thẻ hội viên
        $membership->save();

        return response()->json(['message' => 'Hiển thị thông tin thẻ hội viên thành công', 'data' => $membership], 200);
    }


    public function getUserMembership()
    {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để xem thông tin thẻ hội viên!'
            ], 401);  // 401 Unauthorized nếu chưa đăng nhập
        }

        // Lấy user_id của người dùng đã đăng nhập
        $user_id = auth()->user()->id;

        // Truy vấn để lấy thẻ hội viên của người dùng
        $membership = Membership::with('registerMember')  // Quan hệ với RegisterMember
            ->whereHas('registerMember', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);  // Lọc theo user_id trong bảng register_members
            })
            ->first();  // Lấy thẻ hội viên đầu tiên (nếu có)

        // Kiểm tra xem thẻ hội viên có tồn tại không
        if (!$membership) {
            return response()->json([
                'message' => 'Bạn chưa đăng ký thẻ hội viên!'
            ], 404);
        }
        $currentDate = now();
        $expirationDate = Carbon::parse($membership->ngay_het_han);

        if ($expirationDate->isBefore($currentDate)) {
            $membership->trang_thai = 1;
            $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";
        } else {
            if ($expirationDate->diffInDays($currentDate) <= 2) {
                $membership->renewal_message = "Thẻ hội viên sắp hết hạn!!!. Vui lòng gia hạn thẻ!";
            }
            $membership->trang_thai = 0;
            $membership->renewal_message = $membership->renewal_message ?? "Thẻ còn thời gian sử dụng.";
        }

        // Cập nhật trạng thái thẻ hội viên
        $membership->save();
        // Nếu thẻ hội viên tồn tại, trả về thông tin thẻ hội viên
        return response()->json([
            'message' => 'Thông tin thẻ hội viên',
            'data' => $membership
        ], 200);
    }



    /**
     * Xóa thẻ hội viên theo ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $membership = Membership::find($id);

        if (!$membership) {
            return response()->json([
                'message' => 'Thẻ hội viên không tồn tại!'
            ], 404); // Trả về 404 nếu không tìm thấy
        }

        $membership->delete();

        return response()->json([
            'message' => 'Xóa thẻ hội viên thành công!'
        ], 200); // Trả về 200 khi xóa thành công
    }
    private function checkAndNotifyMembership($membership)
    {
        $currentDate = Carbon::now();
        $expirationDate = Carbon::parse($membership->ngay_het_han);
        $user = $membership->registerMember;  // Người dùng liên kết với thẻ hội viên

        // Kiểm tra nếu thẻ đã hết hạn
        if ($expirationDate->isBefore($currentDate)) {
            $membership->trang_thai = 1;  // Thẻ đã hết hạn
            $membership->renewal_message = "Thẻ hội viên đã hết hạn. Vui lòng đăng ký lại thẻ hội viên mới!";
            $this->sendNotification($user->email, "Thông báo hết hạn thẻ hội viên", $membership->renewal_message);
        } else {
            // Kiểm tra thẻ sắp hết hạn (trong vòng 2 ngày)
            if ($expirationDate->diffInDays($currentDate) <= 2) {
                $membership->renewal_message = "Thẻ hội viên sắp hết hạn. Vui lòng gia hạn thẻ!";
                $this->sendNotification($user->email, "Thông báo gia hạn thẻ hội viên", $membership->renewal_message);
            } else {
                $membership->renewal_message = "Thẻ còn thời gian sử dụng.";
            }
            $membership->trang_thai = 0;  // Thẻ còn hiệu lực
        }

        // Cập nhật trạng thái và thông báo
        $membership->save();
    }

    /**
     * Gửi thông báo email cho người dùng.
     */
    // private function sendNotification($email, $subject, $message)
    // {
    //     Mail::to($email)->send(new MembershipNotification($subject, $message));
    // }
}
