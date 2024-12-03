<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovieGenre;
use Illuminate\Http\Request;

class MoviegenreController extends Controller
{


    // xuất all thể loại phim
    public function index()
    {

        $moviegenreall = MovieGenre::all();

        if ($moviegenreall->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre nào !'
            ], 404);
        }

        return response()->json([
            'message' => 'Xuất dữ liệu MovieGenre thành công',
            'data' => $moviegenreall,
        ], 200);
    }


    // thêm mới thể loại phim
    public function store(Request $request)
    {

        $validated = $request->validate([
            'ten_loai_phim' => 'required|string|max:255',
        ]);

        // check ten_loai_phim đã có nếu thêm mới 1 thể loại giống 
        $checkGenre = MovieGenre::where('ten_loai_phim', $validated['ten_loai_phim'])->exists();

        if ($checkGenre) {
            return response()->json([
                'message' => 'Tên thể loại phim này đã tồn tại !',
                'data' => $checkGenre
            ], 409); // 409 là xung đột 
        }

        $moviegenre = MovieGenre::create($validated);

        return response()->json([
            'message' => 'Thêm mới loai phim thành công',
            'data' => $moviegenre
        ], 201);
    }


    // show MovieGenre theo id
    public function show(string $id)
    {
        // show MovieGenre theo id
        $moviegenreID = MovieGenre::find($id);

        if (!$moviegenreID) {
            return response()->json([
                'error' => 'Không có dữ liệu MovieGenre theo id : ' . $id,
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin MovieGenre theo ID thành công',
            'data' => $moviegenreID,
        ], 200);
    }

    
    // đưa đến trang edit đổ thông tin theo id
    public function edit(string $id)
    {
        // show MovieGenre theo id
        $moviegenreID = MovieGenre::find($id);

        if (!$moviegenreID) {
            return response()->json([
                'error' => 'Không có dữ liệu MovieGenre theo id : ' . $id,
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin MovieGenre theo ID để edit thành công',
            'data' => $moviegenreID,
        ], 200);
    }


    // cập nhật thông tin mới với id đó
    public function update(Request $request, string $id)
    {

        $moviegenreID = MovieGenre::find($id);

        //check có id khi sửa de cap nhat 
        if (!$moviegenreID) {
            return response()->json([
                'error' => 'Không tìm thấy bản ghi với ID : ' . $id
            ], 404);
        }
        // check cac truong 
        $validated = $request->validate([
            'ten_loai_phim' => 'required|string|max:255',
        ]);

        // check nếu có thể loại này trong db chua 
        $checkGenre = MovieGenre::where('ten_loai_phim', $validated['ten_loai_phim'])->exists();

        if ($checkGenre) {
            return response()->json([
                'message' => 'Thể loại phim này đã tồn tại rồi !',
            ], 409); // 409 xung đột
        }

        // cap nhat
        $moviegenreID->update($validated);

        return response()->json([
            'message' => 'Cập nhật dữ liệu MovieGenre theo id thành công',
            'data' => $moviegenreID
        ], 200);
    }

    
    // xóa theo id
    public function delete(string $id)
    {
        // xoa theo id có softdelete
        $moviegenreID = MovieGenre::find($id);

        // check xem co du lieu hay ko
        if (!$moviegenreID) {
            return response()->json([
                'message' => 'Không có dữ liệu MovieGenre theo id :' . $id,
            ], 404);
        }

        $moviegenreID->delete();

        return response()->json([
            'message' => 'Xóa MovieGenre theo id thành công'
        ], 200);
    }
}
