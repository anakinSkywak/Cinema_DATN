<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Movie_genreRequest;
use App\Models\Movie_genre;
use Illuminate\Http\Request;

class Movie_genreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Movie_genre::query()->orderBy('id', 'DESC')->paginate(10);

        if($data->isEmpty()){
            return response()->json([
                'message'=> 'Không có phim',
            ], 204);
        }

        return response()->json([
            'data' => $data->items(), // Chỉ lấy dữ liệu các bản ghi
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Movie_genreRequest $request)
    {
        // Tạo mới Movie_genre với dữ liệu đã được xác thực
        $data = Movie_genre::create($request->validated());

        // Trả về phản hồi với dữ liệu đã tạo và thông báo thành công
        return response()->json([
            'data' => $data,
            'message' => 'Bạn đã thêm thành công!'
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = Movie_genre::query()->findOrFail($id);
        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Movie_genreRequest $request, string $id)
    {
        // Tìm movie genre theo id, nếu không có thì trả về lỗi 404
        $data = Movie_genre::query()->findOrFail($id);

        // Cập nhật dữ liệu đã được xác thực
        $data->update($request->validated());

        dd($data);
        // Trả về phản hồi với dữ liệu đã cập nhật và thông báo thành công
        return response()->json([
            'data' => $data,
            'message' => 'Bạn đã cập nhật thành công!'
        ], 200); // Sử dụng mã 200 OK khi cập nhật thành công
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Tìm movie genre theo id, nếu không có thì trả về lỗi 404
        $data = Movie_genre::query()->findOrFail($id);
        
        // Xóa bản ghi
        $data->delete();

        // Trả về phản hồi thông báo xóa thành công
        return response()->json([
            'message' => 'Bạn đã xóa thành công!'
        ], 200);
    }

}
