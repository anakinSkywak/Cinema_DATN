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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'hoivien_id' => 'required|integer|exists:members,id',
            'trang_thai' => 'required|integer',
        ]);

        $member = Member::find($validated['hoivien_id']);
        if (!$member) {
            return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
        }

        $tong_tien = $member->gia * $member->thoi_gian;
        $ngay_dang_ky = Carbon::now();
        $ngay_het_han = $ngay_dang_ky->copy()->addMonths($member->thoi_gian);

        DB::beginTransaction();
        try {
            // Tạo mới RegisterMember
            $registerMember = RegisterMember::create([
                'user_id' => $validated['user_id'],
                'hoivien_id' => $validated['hoivien_id'],
                'tong_tien' => $tong_tien,
                'ngay_dang_ky' => $ngay_dang_ky,
                'ngay_het_han' => $ngay_het_han,
                'trang_thai' => 0,
            ]);


            DB::commit();

            return response()->json([
                'message' => 'Đăng ký thành công, vui lòng chọn phương thức thanh toán.',
                'data' => $registerMember
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo RegisterMember và Membership', ['error' => $e->getMessage()]);

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
            'trang_thai' => 'required|integer',
        ]);

        // Lấy thông tin loại hội viên mới
        $newMember = Member::find($validated['hoivien_id']);
        $currentMember = $registerMember->member; // Lấy thông tin loại hội viên hiện tại

        if (!$newMember) {
            return response()->json(['message' => 'Loại hội viên không tồn tại'], 404);
        }

        // Tính tổng tiền mới (chưa tính giảm giá)
        $tong_tien_moi = $newMember->gia * $newMember->thoi_gian;

        // Áp dụng giảm giá nếu nâng cấp từ "thường" lên "VIP"
        if (
            strtolower(trim($currentMember->loai_hoi_vien)) === 'hội viên thường' &&
            strtolower(trim($newMember->loai_hoi_vien)) === 'vip'
        ) {
            $tong_tien_moi *= 0.8; // Giảm giá 20%
        }

        // Ngày đăng ký và hết hạn
        $ngayDangKy = Carbon::now();
        $ngayHetHan = Carbon::parse($registerMember->ngay_het_han);

        // Kiểm tra ngày hết hạn của thẻ hiện tại
        if ($ngayHetHan->greaterThan($ngayDangKy)) {
            if ($currentMember->id == $newMember->id) {
                $ngayHetHan = $ngayHetHan->addMonths($newMember->thoi_gian);
                $message = "Thẻ được gia hạn thêm.";
            } else {
                $ngayHetHan = $ngayDangKy->copy()->addMonths($newMember->thoi_gian);
                $message = "Thẻ đã được nâng cấp.";
            }
        } else {
            $ngayHetHan = $ngayDangKy->copy()->addMonths($newMember->thoi_gian);
            $message = "Thẻ đã hết hạn và được gia hạn.";
        }

        // Cập nhật thông tin đăng ký
        DB::beginTransaction();
        try {
            $registerMember->update([
                'user_id' => $validated['user_id'],
                'hoivien_id' => $validated['hoivien_id'],
                'tong_tien' => $tong_tien_moi,
                'trang_thai' => $validated['trang_thai'],
                'ngay_dang_ky' => $ngayDangKy,
                'ngay_het_han' => $ngayHetHan,
            ]);

            // Load lại quan hệ để trả về chính xác dữ liệu
            $registerMember->load('member');

            // Thực hiện thanh toán
            // try {
            //     $this->processPaymentForRegister($request, $registerMember); // Gọi hàm xử lý thanh toán
            // } catch (\Exception $e) {
            //     DB::rollBack(); // Rollback nếu thanh toán bị lỗi
            //     Log::error('Lỗi khi xử lý thanh toán: ' . $e->getMessage());
            //     return response()->json(['message' => 'Thanh toán không thành công!'], 500);
            // }

            DB::commit();

            return response()->json([
                'message' => $message,
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
