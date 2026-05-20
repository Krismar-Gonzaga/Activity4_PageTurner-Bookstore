<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable query log to save memory
        DB::disableQueryLog();

        // Create categories only if they don't exist
        if (Category::count() === 0) {
            Category::factory(20)->create();
        }
    }
}
