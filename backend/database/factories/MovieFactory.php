<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Movie_genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ten_phim' => $this->faker->sentence(3), // Tên phim
            'anh_phim' => $this->faker->imageUrl(), // URL ảnh phim
            'dao_dien' => $this->faker->name, // Đạo diễn
            'dien_vien' => implode(', ', $this->faker->words(3)), // Diễn viên
            'noi_dung' => $this->faker->paragraph, // Nội dung phim
            'trailer' => $this->faker->url(), // URL trailer
            'gia_ve' => $this->faker->randomFloat(2, 50, 200), // Giá vé
            'danh_gia' => $this->faker->numberBetween(1, 10), // Đánh giá từ 1 đến 10
            'loaiphim_id' => Movie_genre::inRandomOrder()->first()->id, // Lấy id ngẫu nhiên từ bảng Movie_genre
        ];
    }
}
