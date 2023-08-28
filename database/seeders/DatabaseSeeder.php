<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(5)->create();

        \App\Models\Product::factory(5)->create();

        \App\Models\ProductGroup::create([
            'user_id' => 1,
            'discount' => 10
        ]);

        DB::table('product_group_items')->insert([
            [
                'group_id' => 1,
                'product_id' => 2
            ],
            [
                'group_id' => 1,
                'product_id' => 5
            ]
        ]);
    }
}
