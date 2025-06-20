<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         \App\Models\User::factory(100)->create()->each(function ($user) {
         \App\Models\Order::factory(rand(1, 10))->create([
            'user_id' => $user->id,
            'total_amount' => rand(100, 1000),
        ]);
    });
    }
}
