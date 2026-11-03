<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Hydrococo 150ml',
                'description' => 'Minuman air kelapa alami kemasan 150ml.',
                'price' => 5000,
                'stock' => 50,
            ],
            [
                'name' => 'Indomie Goreng',
                'description' => 'Mie instan rasa ayam penyet.',
                'price' => 3500,
                'stock' => 100,
            ],
            [
                'name' => 'Aqua 600ml',
                'description' => 'Air mineral dalam kemasan 600ml.',
                'price' => 4000,
                'stock' => 80,
            ],
            [
                'name' => 'Teh Pucuk Harum 350ml',
                'description' => 'Minuman teh melati dalam kemasan.',
                'price' => 5000,
                'stock' => 60,
            ],
            [
                'name' => 'Chitato Original 68gr',
                'description' => 'Keripik kentang rasa original.',
                'price' => 12000,
                'stock' => 35,
            ],
            [
                'name' => 'SilverQueen Almond 62gr',
                'description' => 'Cokelat dengan campuran kacang almond.',
                'price' => 15000,
                'stock' => 25,
            ],
            [
                'name' => 'Sari Roti Tawar',
                'description' => 'Roti tawar putih lembut.',
                'price' => 16000,
                'stock' => 20,
            ],
            [
                'name' => 'Kopi Kapal Api 165gr',
                'description' => 'Kopi bubuk hitam.',
                'price' => 18000,
                'stock' => 40,
            ],
            [
                'name' => 'Oreo Original 133gr',
                'description' => 'Biskuit cokelat dengan krim vanila.',
                'price' => 10000,
                'stock' => 45,
            ],
            [
                'name' => 'Roma Kelapa 300gr',
                'description' => 'Biskuit kelapa renyah.',
                'price' => 13000,
                'stock' => 30,
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                ...$product,
                'product_code' => 'PRD-' . Str::upper(Str::random(6)),
                'slug' => Str::slug($product['name']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
