<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\CourierRecommendationController;

Route::get('/shipments', [ShipmentController::class, 'index']);
Route::post('/shipments', [ShipmentController::class, 'store']);
Route::post('/shipments/{shipment}/delivery-result', [ShipmentController::class, 'updateDeliveryResult']);
Route::get('/shipments/{shipment}/recommend-courier', [CourierRecommendationController::class, 'recommend']);
Route::get('/test', function () { return 'ok'; });