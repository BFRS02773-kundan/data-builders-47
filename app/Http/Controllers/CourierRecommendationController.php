<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Services\CourierRecommendationService;
use Illuminate\Support\Facades\Log;

class CourierRecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(CourierRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function recommend($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $courier = $this->recommendationService->recommendBestCourier($shipment);

        // Example: Extract or assign values for logging (update as per your Courier object structure)
        $score = $courier->score ?? null;
        $rateValue = $courier->rate ?? null;
        $speed = $courier->speed ?? null;
        $rto = $courier->rto ?? null;
        $success = $courier->success ?? null;

        Log::info("Courier: {$courier->name}, Score: $score, Rate: $rateValue, Speed: $speed, RTO: $rto, Success: $success");
        return response()->json([
            'courier' => $courier
        ]);
    }
}
