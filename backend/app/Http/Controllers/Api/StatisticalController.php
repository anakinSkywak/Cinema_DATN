<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

// chức năng thống kê
class StatisticalController extends Controller
{
    // thống kê số lượng phim
    public function soLuongPhim()
    {
        $data = Movie::query()->count(); // Truy vấn trực tiếp số lượng phim

        if ($data === 0) {
            return response()->json([
                'message' => "Không có phim nào trong cơ sở dữ liệu",
                'data' => $data
            ], 404);
        }

        return response()->json([
            'message' => "Lấy tổng số lượng phim thành công",
            'data' => $data
        ], 200);
    }

    // thông kê doanh thu bán vé
    // public function doanhThu(){
    //     $data = 
    // }
}
