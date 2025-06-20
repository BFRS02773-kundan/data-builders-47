<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shipment;
use App\Models\CourierPerformance;
use App\Models\CourierRate;
use App\Models\DeliveryResult;
use Illuminate\Support\Facades\Storage;

class ExportDeliveryData extends Command
{
    protected $signature = 'export:delivery-data';
    protected $description = 'Export shipment and delivery result data for ML training';

    public function handle()
    {
        $filename = storage_path('app/delivery_data.csv');
        $handle = fopen($filename, 'w');
        // CSV header
        fputcsv($handle, [
            'shipment_id', 'courier_id', 'destination_pincode', 'rate', 'avg_delivery_speed', 'rto_rate', 'success_rate', 'delivered_at', 'success', 'rto'
        ]);

        $results = DeliveryResult::with(['shipment', 'courier'])
            ->get();

        foreach ($results as $result) {
            $shipment = $result->shipment;
            $courierId = $result->courier_id;
            $pincode = $shipment->destination_pincode;
            // Get rate and performance for this courier and pincode
            $rate = \App\Models\CourierRate::where('courier_id', $courierId)
                ->where('pincode', $pincode)
                ->orderByDesc('last_updated')
                ->first();
            $performance = \App\Models\CourierPerformance::where('courier_id', $courierId)
                ->where('pincode', $pincode)
                ->first();
            fputcsv($handle, [
                $shipment->id,
                $courierId,
                $pincode,
                $rate ? $rate->rate : null,
                $performance ? $performance->avg_delivery_speed : null,
                $performance ? $performance->rto_rate : null,
                $performance ? $performance->success_rate : null,
                $result->delivered_at,
                $result->success,
                $result->rto,
            ]);
        }
        fclose($handle);
        $this->info('Exported delivery data to ' . $filename);
    }
}
