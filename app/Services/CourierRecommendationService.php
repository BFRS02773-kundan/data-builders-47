<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\Courier;
use App\Models\CourierPerformance;
use App\Models\CourierRate;

class CourierRecommendationService
{
    /**
     * Recommend the best courier for a shipment.
     *
     * @param Shipment $shipment
     * @return Courier|null
     */
    public function recommendBestCourier(Shipment $shipment)
    {
        // Get all couriers
        $couriers = Courier::all();
        $bestScore = -INF;
        $bestCourier = null;

        foreach ($couriers as $courier) {
            // Get rate for this shipment's pincode
            $rate = CourierRate::where('courier_id', $courier->id)
                ->where('pincode', $shipment->destination_pincode)
                ->orderByDesc('last_updated')
                ->first();
            $rateValue = $rate ? $rate->rate : 9999;

            // Get performance for this pincode
            $performance = CourierPerformance::where('courier_id', $courier->id)
                ->where('pincode', $shipment->destination_pincode)
                ->first();
            $speed = $performance ? $performance->avg_delivery_speed : 9999;
            $rto = $performance ? $performance->rto_rate : 1.0;
            $success = $performance ? $performance->success_rate : 0.0;

            // Weighted scoring (lower rate/speed/rto is better, higher success is better)
            $score =
                (-1.0 * $rateValue) +
                (-0.5 * $speed) +
                (-2.0 * $rto) +
                (3.0 * $success);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestCourier = $courier;
            }
        }

        return $bestCourier;
    }
}
