<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'courier_id',
        'delivered_at',
        'success',
        'rto',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'success' => 'boolean',
        'rto' => 'boolean',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
