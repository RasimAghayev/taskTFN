<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()
            ->count(5)
            ->create();
        Category::factory()
            ->count(5)
            ->hasCategoryParentIds(10)
            ->create();
//        Category::factory()
//            ->count(5)
//            ->hasCategoryParentIds(10)
//            ->create();
    }
}
