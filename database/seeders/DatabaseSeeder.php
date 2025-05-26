<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Mark Romel F. Feguro',
            'email' => 'markromelfeguro1@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $categories = [
            ['category_name' => 'Snacks'],
            ['category_name' => 'Beverages'],
            ['category_name' => 'Drinks'],
        ];

        DB::table('categories')->insert($categories);

    }
}
