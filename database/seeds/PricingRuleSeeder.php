<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingRuleSeeder extends Seeder
{
    public function run()
    {
        DB::table('pricing_rules')->insert([
            [
                'product_code' => 'FR1',
                'rule_name' => 'buy_one_get_one',
                'rule_details' => json_encode([]),
                'active' => true,
                'start_date' => null,
                'end_date' => null,
                'days' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_code' => 'SR1',
                'rule_name' => 'bulk_discount',
                'rule_details' => json_encode(['min_quantity' => 3, 'discount_price' => 4.50]),
                'active' => true,
                'start_date' => null,
                'end_date' => null,
                'days' => json_encode(["Monday", "Wednesday"]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
