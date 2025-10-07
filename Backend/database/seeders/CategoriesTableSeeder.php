<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Renting',
            'status' => 1,
        ]);

        Category::create([
            'name' => 'Storage',
            'status' => 1,
        ]);

    }
}
