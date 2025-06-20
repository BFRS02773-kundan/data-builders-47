<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\DeliveryResult;

class ShipmentController extends Controller
{
    // Create a new shipment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'destination_pincode' => 'required|string',
            'package_details' => 'required|array',
        ]);
        $shipment = Shipment::create([
            'order_id' => $validated['order_id'],
            'destination_pincode' => $validated['destination_pincode'],
            'package_details' => $validated['package_details'],
            'status' => 'pending',
        ]);
        return response()->json(['shipment' => $shipment], 201);
    }

    // Update delivery result for a shipment
    public function updateDeliveryResult(Request $request, $shipmentId)
    {
        $validated = $request->validate([
            'courier_id' => 'required|exists:couriers,id',
            'delivered_at' => 'nullable|date',
            'success' => 'required|boolean',
            'rto' => 'required|boolean',
        ]);
        $shipment = Shipment::findOrFail($shipmentId);
        $result = DeliveryResult::create([
            'shipment_id' => $shipment->id,
            'courier_id' => $validated['courier_id'],
            'delivered_at' => $validated['delivered_at'] ?? now(),
            'success' => $validated['success'],
            'rto' => $validated['rto'],
        ]);
        $shipment->status = $validated['success'] ? 'delivered' : 'failed';
        $shipment->courier_id = $validated['courier_id'];
        $shipment->save();
        return response()->json(['delivery_result' => $result]);
    }

    // List all shipments with pagination
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $query = \App\Models\Shipment::orderByDesc('id');
        if ($request->filled('pincode')) {
            $query->where('destination_pincode', $request->query('pincode'));
        }
        $shipments = $query->paginate($perPage);
        return response()->json([
            'shipments' => $shipments->items(),
            'meta' => [
                'current_page' => $shipments->currentPage(),
                'last_page' => $shipments->lastPage(),
                'per_page' => $shipments->perPage(),
                'total' => $shipments->total(),
            ]
        ]);
    }
}
