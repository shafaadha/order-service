<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('customers')->insert([
            'name' => Str::random(10),
            'email' => uniqid().'@example.com',
            'phone_number' => fake()->phoneNumber(),
        ]);
    }
}
