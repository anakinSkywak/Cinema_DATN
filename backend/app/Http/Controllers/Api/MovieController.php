<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\MovieGenre;
use App\Models\Showtime;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index()
    {
        $data = Movie::with('movie_genres')->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Không có dữ liệu Movie nào'
            ], 200);
        }

        return response()->json([
            'message' => 'Hiển thị dữ liệu thành công',
            'data' => $data
        ]);
    }

    public function getMovieGenre()
    {
        $getMovieGenre = MovieGenre::all();

        if ($getMovieGenre->isEmpty()) {
            return response()->json([
                'message' => 'Không có thể loại phim nào'
            ], 200);
        }

        return response()->json([
            'message' => 'Thể loại phim',
            'data' => $getMovieGenre
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'trailer' => 'required|string|url|max:255',
            'gia_ve' => 'required|numeric',
            'hinh_thuc_phim' => 'required|string|max:255',
            'loaiphim_ids' => 'required|array',
            'loaiphim_ids.*' => 'exists:moviegenres,id',
        ]);

        if ($request->hasFile('anh_phim')) {
            $file = $request->file('anh_phim');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
            $validated['anh_phim'] = '/storage/' . $filePath;
        }

        $movie = Movie::create($validated);
        $movie->movie_genres()->sync($request->loaiphim_ids);

        return response()->json([
            'message' => 'Thêm mới phim thành công',
            'data' => $movie->load('movie_genres'),
            'image_url' => asset($validated['anh_phim']),
        ], 201);
    }

    public function show(string $id)
    {
        $movie = Movie::with('movie_genres')->find($id);

        if (!$movie) {
            return response()->json([
                'message' => 'Không tìm thấy phim'
            ], 404);
        }

        return response()->json([
            'message' => 'Hiển thị thành công',
            'data' => $movie,
        ]);
    }

    public function showEditID(string $id)
    {
        $movie = Movie::findOrFail($id);
        $allGenres = MovieGenre::all();

        return response()->json([
            'message' => 'Hiển thị phim và thể loại',
            'movie' => $movie->load('movie_genres'),
            'all_genre' => $allGenres,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $movie = Movie::findOrFail($id);

        $validated = $request->validate([
            'ten_phim' => 'required|string|max:255',
            'anh_phim' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'dao_dien' => 'required|string|max:255',
            'dien_vien' => 'required|string|max:255',
            'noi_dung' => 'required|string|max:255',
            'trailer' => 'required|string|max:255',
            'gia_ve' => 'required|numeric',
            'hinh_thuc_phim' => 'required|string|max:255',
            'loaiphim_ids' => 'required|array',
            'loaiphim_ids.*' => 'exists:moviegenres,id',
        ]);

        if ($request->hasFile('anh_phim')) {
            if ($movie->anh_phim) {
                unlink(public_path($movie->anh_phim));
            }

            $file = $request->file('anh_phim');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/anh_phim', $filename, 'public');
            $validated['anh_phim'] = '/storage/' . $filePath;
        } else {
            $validated['anh_phim'] = $movie->anh_phim;
        }

        $movie->update($validated);
        $movie->movie_genres()->sync($request->loaiphim_ids);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $movie->load('movie_genres'),
            'image_url' => asset($movie->anh_phim),
        ]);
    }

    public function delete(string $id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json([
                'message' => 'Không tìm thấy phim'
            ], 404);
        }

        $movie->delete();

        return response()->json([
            'message' => 'Xóa thành công'
        ]);
    }

    public function movieFilter(string $id)
    {
        $movies = Movie::with('movie_genres')->whereHas('movie_genres', function ($query) use ($id) {
            $query->where('moviegenres.id', $id);
        })->get();

        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không có phim nào'
            ], 404);
        }

        return response()->json([
            'data' => $movies,
        ]);
    }

    public function movieFilterKeyword(Request $request)
    {
        $keyword = $request->input('keyword');
        $movies = Movie::with('movie_genres')->where('ten_phim', 'like', '%' . $keyword . '%')->get();

        if ($movies->isEmpty()) {
            return response()->json([
                'message' => 'Không tìm thấy phim'
            ], 404);
        }

        return response()->json([
            'data' => $movies,
        ]);
    }

    public function movieDetail($movieID)
    {
        $movie = Movie::with('showtimes')->findOrFail($movieID);

        $hasShowtimes = Showtime::where('phim_id', $movieID)->exists();

        return response()->json([
            'message' => $hasShowtimes ? 'Có showtime' : 'Chưa có showtime',
            'data' => $movie
        ]);
    }
}
