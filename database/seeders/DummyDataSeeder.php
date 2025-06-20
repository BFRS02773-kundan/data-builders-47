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
        $couriers = Courier::all();
        $pincodes = ['110001', '560001', '400001'];
        $days = 30;

        // Create sample orders
        foreach (range(1, 10) as $i) {
            Order::factory()->create();
        }

        // Create performance and rate data for each courier and pincode
        foreach ($couriers as $courier) {
            foreach ($pincodes as $pincode) {
                CourierPerformance::create([
                    'courier_id' => $courier->id,
                    'pincode' => $pincode,
                    'avg_delivery_speed' => rand(1, 5),
                    'rto_rate' => rand(1, 20) / 100,
                    'success_rate' => rand(80, 99) / 100,
                ]);
                CourierRate::create([
                    'courier_id' => $courier->id,
                    'pincode' => $pincode,
                    'rate' => rand(30, 100),
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
