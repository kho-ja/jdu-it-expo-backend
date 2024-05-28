<?php

namespace Database\Seeders;

use App\Models\BookCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BookCategory::create([
            'name' => 'Axborot Texnologiyalari',
            'code' => 'IT'
        ]);
    }
}
