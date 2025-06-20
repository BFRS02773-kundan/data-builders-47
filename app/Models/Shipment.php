<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeliveryResult;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier_id',
        'destination_pincode',
        'package_details',
        'status',
    ];

    protected $casts = [
        'package_details' => 'array',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function deliveryResults()
    {
        return $this->hasMany(DeliveryResult::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
