<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Courier;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        Courier::create([
            'name' => 'Delhivery',
            'api_credentials' => json_encode(['api_key' => 'delhivery123']),
        ]);
        Courier::create([
            'name' => 'Bluedart',
            'api_credentials' => json_encode(['api_key' => 'bluedart456']),
        ]);
        Courier::create([
            'name' => 'Ecom Express',
            'api_credentials' => json_encode(['api_key' => 'ecom789']),
        ]);
    }
}
