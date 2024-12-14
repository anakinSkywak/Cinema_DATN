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
        $data = RegisterMember::with('memberships', 'member', 'payments')->get();

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
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Bạn cần đăng nhập để tiếp tục'], 401);
        }

        // Kiểm tra xem hội viên có tồn tại không
        $member = Member::find($hoivien_id);
        if (!$member) {
            return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
        }
        $existingRegistration = RegisterMember::where('user_id', $user->id)
            ->first();

        if ($existingRegistration) {
            // Nếu người dùng đã đăng ký, trả về thông báo lỗi
            return response()->json(['message' => 'Bạn đã đăng ký thẻ hội viên rồi!'], 400);
        }

        // Lấy thời gian từ request, nếu không có thì sử dụng thời gian mặc định của loại hội viên
        $thoi_gian = $request->input('thoi_gian', $member->thoi_gian);

        // Kiểm tra thời gian có hợp lệ không
        if (!is_numeric($thoi_gian) || $thoi_gian <= 0) {
            return response()->json(['message' => 'Thời gian không hợp lệ'], 400);
        }

        // Tính tổng tiền dựa trên loại hội viên và thời gian được chọn
        $tong_tien = $member->gia * $thoi_gian;

        // Lấy ngày đăng ký và tính ngày hết hạn
        $ngay_dang_ky = Carbon::now();
        $ngay_het_han = $ngay_dang_ky->copy()->addMonths($thoi_gian);

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();
        try {
            // Tạo mới bản ghi RegisterMember
            $registerMember = RegisterMember::create([
                'user_id' => $user->id,
                'hoivien_id' => $hoivien_id,
                'tong_tien' => $tong_tien,
                'ngay_dang_ky' => $ngay_dang_ky,
                'ngay_het_han' => $ngay_het_han,
                'trang_thai' => 0,
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


    public function update(Request $request, $hoivien_id)
    {
        // Lấy người dùng hiện tại
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Người dùng chưa đăng nhập'], 401);
        }

        // Tìm đăng ký hội viên của người dùng
        $registerMember = RegisterMember::where('user_id', $user->id)->first();

        if (!$registerMember) {
            return response()->json(['message' => 'Không tìm thấy đăng ký hội viên của người dùng'], 404);
        }

        // Tìm loại hội viên mới
        $newMember = Member::find($hoivien_id);
        $currentMember = $registerMember->member;

        if (!$newMember) {
            return response()->json(['message' => 'Loại hội viên không tồn tại'], 404);
        }

        // Lấy thời gian từ request (mặc định là thời gian của loại hội viên mới nếu không cung cấp)
        $requestedTime = $request->input('thoi_gian', $newMember->thoi_gian);

        // Kiểm tra thời gian hợp lệ
        if (!is_numeric($requestedTime) || $requestedTime <= 0) {
            return response()->json(['message' => 'Thời gian không hợp lệ'], 400);
        }

        // Tính giá và áp dụng giảm giá nếu nâng cấp
        $currentPrice = $currentMember->gia ?? 0;
        $newPrice = $newMember->gia ?? 0;

        $tong_tien_moi = $newPrice * $requestedTime;

        if ($newPrice > $currentPrice) {
            $tong_tien_moi *= 0.8; // Giảm 20% khi nâng cấp
        }

        // Xử lý ngày hết hạn
        $ngayDangKy = Carbon::now();
        $ngayHetHan = Carbon::parse($registerMember->ngay_het_han);

        if ($ngayHetHan->greaterThan($ngayDangKy)) {
            $ngayHetHan = ($currentMember->id === $newMember->id)
                ? $ngayHetHan->addMonths($requestedTime)
                : $ngayDangKy->copy()->addMonths($requestedTime);
        } else {
            $ngayHetHan = $ngayDangKy->copy()->addMonths($requestedTime);
        }

        // Cập nhật thông tin đăng ký
        DB::beginTransaction();
        try {
            $registerMember->update([
                'hoivien_id' => $hoivien_id,
                'tong_tien' => $tong_tien_moi,
                'trang_thai' => 0,
                'ngay_dang_ky' => $ngayDangKy,
                'ngay_het_han' => $ngayHetHan,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật đăng ký thành công, vui lòng thanh toán',
                'data' => $registerMember,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật đăng ký hội viên', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau'], 500);
        }
    }

    public function listRegisterMembersForUser()
    {
        // Lấy thông tin người dùng đã đăng nhập
        $user = auth()->user(); // Giả sử bạn đang sử dụng Laravel Auth

        // Tìm tất cả các đăng ký hội viên của người dùng
        $registerMembers = RegisterMember::with('member')
            ->where('user_id', $user->id)
            ->get();


        if ($registerMembers->isEmpty()) {
            return response()->json(['message' => 'Bạn chưa đăng ký hội viên nào'], 404);
        }

        return response()->json([
            'message' => 'Danh sách đăng ký hội viên của bạn',
            'data' => $registerMembers,
        ], 200);
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

    public function revenueByMembershipType()
    {
        // Lấy tất cả các loại hội viên từ bảng Member
        $membershipTypes = Member::all()->keyBy('id');

        // Lấy tổng doanh thu từ bảng RegisterMember theo từng loại hội viên
        $revenueData = RegisterMember::select('hoivien_id', DB::raw('SUM(tong_tien) as total_revenue'))
            ->groupBy('hoivien_id')
            ->get()
            ->keyBy('hoivien_id');

        // Kết hợp thông tin doanh thu với toàn bộ loại hội viên
        $result = $membershipTypes->map(function ($membership) use ($revenueData) {
            $revenue = $revenueData->get($membership->id);
            return [
                'Loại hội viên' => $membership->loai_hoi_vien,
                'Doanh thu' => $revenue ? $revenue->total_revenue : 0
            ];
        });

        // Trả về kết quả
        return response()->json([
            'message' => 'Thống kê doanh thu thành công',
            'data' => $result->values()
        ], 200);
    }
    public function countUsersByMembershipType()
    {
        // Lấy tất cả các loại hội viên từ bảng Member
        $membershipTypes = Member::all()->keyBy('id');

        // Đếm số lượng người đăng ký cho từng loại hội viên từ bảng RegisterMember
        $registrationData = RegisterMember::select('hoivien_id', DB::raw('COUNT(user_id) as total_users'))
            ->groupBy('hoivien_id')
            ->get()
            ->keyBy('hoivien_id');

        // Kết hợp thông tin số lượng người đăng ký với tất cả loại hội viên
        $result = $membershipTypes->map(function ($membership) use ($registrationData) {
            $registrations = $registrationData->get($membership->id);
            return [
                'Loại hội viên' => $membership->loai_hoi_vien,
                'Doanh thu' => $registrations ? $registrations->total_users : 0
            ];
        });

        // Trả về kết quả
        return response()->json([
            'message' => 'Thống kê số lượng người đăng ký thành công',
            'data' => $result->values()
        ], 200);
    }
}
