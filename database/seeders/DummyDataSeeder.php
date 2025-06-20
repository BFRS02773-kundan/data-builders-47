<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Courier;
use App\Models\CourierPerformance;
use App\Models\CourierRate;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\DeliveryResult;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 distinct couriers
        $courierNames = [
            'Delhivery', 'Bluedart', 'Ecom Express', 'XpressBees', 'Shadowfax'
        ];
        $couriers = collect();
        foreach ($courierNames as $idx => $name) {
            $couriers->push(Courier::create([
                'name' => $name,
                'api_credentials' => json_encode(['api_key' => strtolower(str_replace(' ', '', $name)) . '123']),
            ]));
        }

        $pincodes = ['110001', '560001', '400001'];
        $days = 30;

        // Create sample orders
        foreach (range(1, 10) as $i) {
            Order::factory()->create();
        }

        // Assign distinct performance and rate data for each courier and pincode
        foreach ($couriers as $idx => $courier) {
            foreach ($pincodes as $pincode) {
                CourierPerformance::create([
                    'courier_id' => $courier->id,
                    'pincode' => $pincode,
                    'avg_delivery_speed' => 2 + $idx, // 2, 3, 4, 5, 6
                    'rto_rate' => 0.05 + ($idx * 0.02), // 0.05, 0.07, 0.09, 0.11, 0.13
                    'success_rate' => 0.95 - ($idx * 0.02), // 0.95, 0.93, 0.91, 0.89, 0.87
                ]);
                CourierRate::create([
                    'courier_id' => $courier->id,
                    'pincode' => $pincode,
                    'rate' => 50 + ($idx * 10), // 50, 60, 70, 80, 90
                    'last_updated' => now(),
                ]);
            }
        }

        // Create shipments and delivery results for the last 30 days
        $orders = Order::all();
        foreach ($orders as $order) {
            foreach ($pincodes as $pincode) {
                foreach ($couriers as $courier) {
                    for ($i = $days; $i >= 1; $i--) {
                        $date = Carbon::now()->subDays($i);
                        $shipment = Shipment::create([
                            'order_id' => $order->id,
                            'courier_id' => $courier->id,
                            'destination_pincode' => $pincode,
                            'package_details' => [
                                'weight' => rand(1, 5),
                                'dimensions' => rand(10, 20) . 'x' . rand(10, 20) . 'x' . rand(10, 20),
                            ],
                            'status' => 'delivered',
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);
                        DeliveryResult::create([
                            'shipment_id' => $shipment->id,
                            'courier_id' => $courier->id,
                            'delivered_at' => $date->copy()->addDays(rand(1, 3)),
                            'success' => (rand(1, 100) > 10), // 90% success
                            'rto' => (rand(1, 100) <= 10), // 10% RTO
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);
                    }
                }
            }
        }
    }
}
