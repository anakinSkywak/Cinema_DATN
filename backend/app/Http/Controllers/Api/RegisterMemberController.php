<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\RegisterMember;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MemberShips;
use Illuminate\Support\Facades\Log;

class RegisterMemberController extends Controller
{
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng RegisterMember
        $data = RegisterMember::with('membership', 'member', 'payments')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu RegisterMember nào'
            ], 200);
        }

        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }
    public function store(Request $request, $hoivien_id)
    {
          // Kiểm tra xem người dùng đã đăng nhập chưa
        // $user = auth()->user();
        // if (!$user) {
        //     return response()->json(['message' => 'Bạn cần đăng nhập để tiếp tục'], 401);
        // }

        // Kiểm tra xem hội viên có tồn tại không
        $member = Member::find($hoivien_id);
        if (!$member) {
            return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
        }
    
        // Tính tổng tiền dựa trên loại hội viên và thời gian
        $tong_tien = $member->gia * $member->thoi_gian;
        $ngay_dang_ky = Carbon::now();
        $ngay_het_han = $ngay_dang_ky->copy()->addMonths($member->thoi_gian);
    
        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();
        try {
            // Tạo mới bản ghi RegisterMember
            $registerMember = RegisterMember::create([
                // 'user_id' => $user->id,  // Sử dụng user đang đăng nhập
                'user_id' => 14,  // Bạn có thể thay đổi số này thành user_id thực tế nếu có
                'hoivien_id' => $hoivien_id,
                'tong_tien' => $tong_tien,
                'ngay_dang_ky' => $ngay_dang_ky,
                'ngay_het_han' => $ngay_het_han,
                'trang_thai' => 0,  // Trạng thái đăng ký ban đầu là chưa thanh toán
            ]);
    
            // Commit transaction nếu không có lỗi
            DB::commit();
    
            // Trả về kết quả thành công
            return response()->json([
                'message' => 'Đăng ký thành công, vui lòng chọn phương thức thanh toán.',
                'data' => $registerMember
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();
            Log::error('Lỗi khi tạo RegisterMember', ['error' => $e->getMessage()]);
    
            // Trả về thông báo lỗi
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo RegisterMember',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    




    public function update(Request $request, $id)
    {
        // Tìm đăng ký hội viên
        $registerMember = RegisterMember::find($id);

        if (!$registerMember) {
            return response()->json(['message' => 'Không tìm thấy đăng ký hội viên'], 404);
        }

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
        ]);

        // Lấy thông tin hội viên mới và cũ
        $newMember = Member::find($validated['hoivien_id']);
        $currentMember = $registerMember->member;

        if (!$newMember) {
            return response()->json(['message' => 'Loại hội viên không tồn tại'], 404);
        }

        // Tính tổng tiền mới
        $tong_tien_moi = $newMember->gia * $newMember->thoi_gian;

        // Áp dụng giảm giá nếu nâng cấp từ thường lên VIP
        if (
            strtolower(trim($currentMember->loai_hoi_vien)) === 'hội viên thường' &&
            strtolower(trim($newMember->loai_hoi_vien)) === 'vip'
        ) {
            $tong_tien_moi *= 0.8; // Giảm giá 20%
        }

        // Xử lý ngày hết hạn
        $ngayDangKy = Carbon::now();
        $ngayHetHan = Carbon::parse($registerMember->ngay_het_han);
        if ($ngayHetHan->greaterThan($ngayDangKy)) {
            $ngayHetHan = ($currentMember->id == $newMember->id)
                ? $ngayHetHan->addMonths($newMember->thoi_gian)
                : $ngayDangKy->copy()->addMonths($newMember->thoi_gian);
        } else {
            $ngayHetHan = $ngayDangKy->copy()->addMonths($newMember->thoi_gian);
        }

        // Cập nhật thông tin đăng ký
        DB::beginTransaction();
        try {
            $registerMember->update([
                'user_id' => $validated['user_id'],
                'hoivien_id' => $validated['hoivien_id'],
                'tong_tien' => $tong_tien_moi,
                'trang_thai' => 0, // Đặt lại trạng thái để thanh toán lại
                'ngay_dang_ky' => $ngayDangKy,
                'ngay_het_han' => $ngayHetHan,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thành công, sẵn sàng thanh toán lại.',
                'data' => $registerMember,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật thẻ hội viên', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
        }
    }





    public function destroy($id)
    {
        $dataID = RegisterMember::find($id);

        if (!$dataID) {
            return response()->json(['message' => 'Không tìm thấy RegisterMember theo ID'], 404);
        }
        $dataID->delete();
        return response()->json(['message' => 'Xóa RegisterMember thành công'], 200);
    }
}
