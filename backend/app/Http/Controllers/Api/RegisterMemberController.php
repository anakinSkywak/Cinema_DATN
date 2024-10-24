<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegisterMember;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterMemberController extends Controller
{
    public function index()
    {
        $data = RegisterMember::with('member', 'user')->orderBy('id', 'DESC')->paginate(10);

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có đăng ký hội viên',
            ], 204);
        }

        return response()->json([
            'message' => 'Danh sách đăng ký hội viên',
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'total_items' => $data->total(),
                'per_page' => $data->perPage(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'tong_tien' => 'required|numeric|min:0',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $registerMember = RegisterMember::create($validator->validated());

        return response()->json([
            'message' => 'Đăng ký hội viên thành công!',
            'data' => $registerMember
        ], 201);
    }

    public function show($id)
    {
        $registerMember = RegisterMember::with('member', 'user')->find($id);

        if (!$registerMember) {
            return response()->json([
                'message' => 'Không tìm thấy đăng ký hội viên'
            ], 404);
        }

        return response()->json([
            'message' => 'Thông tin đăng ký hội viên',
            'data' => $registerMember
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $registerMember = RegisterMember::find($id);

        if (!$registerMember) {
            return response()->json([
                'message' => 'Không tìm thấy đăng ký hội viên'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'hoivien_id' => 'required|exists:members,id',
            'tong_tien' => 'required|numeric|min:0',
            'ngay_dang_ky' => 'required|date',
            'trang_thai' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $registerMember->update($validator->validated());

        return response()->json([
            'message' => 'Cập nhật đăng ký hội viên thành công!',
            'data' => $registerMember
        ], 200);
    }

    // Delete a register member
    public function destroy($id)
    {
        $registerMember = RegisterMember::find($id);

        if (!$registerMember) {
            return response()->json([
                'message' => 'Không tìm thấy đăng ký hội viên'
            ], 404);
        }

        $registerMember->delete();

        return response()->json([
            'message' => 'Xóa đăng ký hội viên thành công!'
        ]);
    }

}
// use App\Models\Member; // Import thêm Member để lấy giá từ bảng hội viên
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;

// class RegisterMemberController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      */
//     public function index()
//     {
//         // Lấy tất cả dữ liệu từ bảng RegisterMember
//         $data = RegisterMember::with('member', 'payments')->get();

//         if ($data->isEmpty()) {
//             return response()->json([
//                 'message' => 'Không có dữ liệu RegisterMember nào'
//             ], 200);
//         }

//         return response()->json([
//             'message' => 'Hiển thị dữ liệu thành công',
//             'data' => $data
//         ]);
//     }

//     /**
//      * Store a newly created resource in storage.
//      */
//     public function store(Request $request)
//     {
//         // Xác thực dữ liệu khi tạo RegisterMember mới
//         $validated = $request->validate([
//             'user_id' => 'required|integer|exists:users,id',
//             'hoivien_id' => 'required|integer|exists:members,id',
//             'trang_thai' => 'required|integer',
//             'phuong_thuc_thanh_toan' => 'required|in:credit_card,paypal,cash,bank_transfer', // Thêm phần phương thức thanh toán
//         ]);

//         // Lấy giá hội viên và thời gian
//         $member = Member::find($validated['hoivien_id']);
//         if (!$member) {
//             return response()->json(['message' => 'Hội viên không tồn tại!'], 404);
//         }

//         // Tính toán tổng tiền
//         $tong_tien = $member->gia * $member->thoi_gian;

//         // Tính toán ngày hết hạn (bằng cách thêm số tháng đăng ký vào ngày đăng ký)
//         $ngay_dang_ky = now(); // Ngày đăng ký hiện tại
//         $ngay_het_han = $ngay_dang_ky->copy()->addMonths($member->thoi_gian); // Thêm số tháng đăng ký để tính ngày hết hạn

//         // Tạo mới RegisterMember
//         $registerMember = RegisterMember::create([
//             'user_id' => $validated['user_id'],
//             'hoivien_id' => $validated['hoivien_id'],
//             'tong_tien' => $tong_tien,
//             'ngay_dang_ky' => $ngay_dang_ky,
//             'ngay_het_han' => $ngay_het_han, // Lưu ngày hết hạn
//             'trang_thai' => 0, // Đăng ký chưa được thanh toán
//         ]);

//         // Gọi phương thức thanh toán
//         $paymentResponse = app('App\Http\Controllers\Api\PaymentController')->processPaymentForRegister($request, $registerMember);

//         // Kiểm tra xem thanh toán có thành công hay không
//         if ($paymentResponse->getStatusCode() !== 200) {
//             // Nếu thanh toán không thành công, xóa bản ghi RegisterMember đã tạo
//             $registerMember->delete();
//             return $paymentResponse; // Trả về phản hồi từ PaymentController
//         }

//         return response()->json([
//             'message' => 'Thêm mới RegisterMember và thanh toán thành công',
//             'data' => $registerMember
//         ], 201); // Trả về mã trạng thái 201 cho việc tạo thành công
//     }





//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(Request $request, $id)
//     {
//         // Cập nhật RegisterMember theo ID
//         $dataID = RegisterMember::find($id);

//         if (!$dataID) {
//             return response()->json([
//                 'message' => 'Không tìm thấy RegisterMember theo ID'
//             ], 404);
//         }

//         // Validate dữ liệu khi cập nhật RegisterMember
//         $validated = $request->validate([
//             'user_id' => 'required|exists:users,id',
//             'hoivien_id' => 'required|exists:members,id',
//             'ngay_dang_ky' => 'required|date',
//             'trang_thai' => 'required|integer',
//         ]);

//         // Lấy thông tin hội viên để tính giá mới
//         $member = Member::find($request->hoivien_id);
//         if (!$member) {
//             return response()->json([
//                 'message' => 'Hội viên không tồn tại'
//             ], 404);
//         }

//         // Cập nhật giá dựa trên loại hội viên
//         $validated['tong_tien'] = $member->gia;

//         // Cập nhật RegisterMember
//         $dataID->update($validated);

//         // Gọi PaymentController để xử lý thanh toán nếu trạng thái chưa thanh toán
//         if ($validated['trang_thai'] == 0) {
//             app('App\Http\Controllers\Api\PaymentController')->processPaymentForRegister($request, $dataID->id);
//         }

//         return response()->json([
//             'message' => 'Cập nhật dữ liệu và thanh toán thành công',
//             'data' => $dataID,
//         ], 200);
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy($id)
//     {
//         // Xóa RegisterMember theo ID
//         $dataID = RegisterMember::find($id);

//         if (!$dataID) {
//             return response()->json([
//                 'message' => 'Không tìm thấy RegisterMember theo ID'
//             ], 404);
//         }

//         $dataID->delete();

//         return response()->json([
//             'message' => 'Xóa RegisterMember thành công'

//         ], 200);
//     }
// }
