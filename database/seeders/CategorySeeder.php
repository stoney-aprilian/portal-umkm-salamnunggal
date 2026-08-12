<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['type' => 'umkm', 'name' => 'Kuliner', 'slug' => 'kuliner'],
            ['type' => 'product', 'name' => 'Makanan', 'slug' => 'makanan'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['type' => $category['type'], 'name' => $category['name']],
                ['slug' => $category['slug']],
            );
        }
    }
}
