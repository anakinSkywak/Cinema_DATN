<?php

namespace Database\Seeders;

use App\Models\Movie_genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Movie_genreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Movie_genre::factory(10)->create();
    }
}
