<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Services\CourierRecommendationService;

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
        return response()->json([
            'courier' => $courier
        ]);
    }
}
