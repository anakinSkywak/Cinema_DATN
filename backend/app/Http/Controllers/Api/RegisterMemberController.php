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
        $registerMember = RegisterMember::find($id);

        if (!$registerMember) {
            return response()->json(['message' => 'Không tìm thấy đăng ký hội viên'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'trang_thai' => 'required|integer', // 0: chưa thanh toán, 1: đã thanh toán
        ]);

        $newMember = Member::find($validated['hoivien_id']);
        $newMember = $newMember->refresh();

        $currentMember = $registerMember->member;

        if (!$newMember) {
            return response()->json(['message' => 'Loại hội viên không tồn tại'], 404);
        }

        DB::beginTransaction();
        try {
            // Tính tổng tiền mới
            $tong_tien_moi = $newMember->gia * $newMember->thoi_gian;

            // Áp dụng giảm giá nếu nâng cấp từ thường lên VIP
            if (
                strtolower($currentMember->loai_thanh_vien) === 'Hội viên thường' &&
                strtolower($newMember->loai_thanh_vien) === 'vip'
            ) {
                $tong_tien_moi *= 0.8;
            }

            // Ngày đăng ký mới
            $ngayDangKy = Carbon::now();

            // Kiểm tra nếu thẻ chưa hết hạn
            $thoi_gian_con_lai = Carbon::parse($registerMember->ngay_het_han)->diffInDays($ngayDangKy);

            if ($thoi_gian_con_lai > 0) {
                // Thẻ chưa đến ngày hết hạn, không cập nhật
                return response()->json([
                    'message' => 'Thẻ chưa đến ngày hết hạn, không cần cập nhật.',
                    'data' => $registerMember
                ], 200);
            }

            // Xử lý gia hạn hoặc nâng cấp
            if ($currentMember->id == $newMember->id) {
                // Gia hạn: Thêm thời gian vào ngày hết hạn hiện tại
                $ngayHetHan = Carbon::parse($registerMember->ngay_het_han)
                    ->addMonths($newMember->thoi_gian);
            } else {
                // Nâng cấp: Thiết lập ngày đăng ký và hết hạn mới
                $ngayHetHan = $ngayDangKy->copy()->addMonths($newMember->thoi_gian);
            }

            // Cập nhật thông tin đăng ký
            $registerMember->update([
                'user_id' => $validated['user_id'],
                'hoivien_id' => $validated['hoivien_id'],
                'tong_tien' => $tong_tien_moi,
                'trang_thai' => 0, // Trạng thái 0: chưa thanh toán
                'ngay_dang_ky' => $ngayDangKy,
                'ngay_het_han' => $ngayHetHan,
            ]);

            // Nếu trạng thái là chưa thanh toán, gọi hàm xử lý thanh toán
            if ($validated['trang_thai'] == 0) {
                app('App\Http\Controllers\Api\PaymentController')->processPaymentForRegister($request, $registerMember->id);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thẻ thành công, trạng thái thẻ là chưa thanh toán!',
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
