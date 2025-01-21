<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'code' => 'FR1',
                'name' => 'Fruit tea',
                'price' => 3.11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SR1',
                'name' => 'Strawberries',
                'price' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CF1',
                'name' => 'Coffee',
                'price' => 11.23,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
