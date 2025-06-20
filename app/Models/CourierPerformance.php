<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'pincode',
        'avg_delivery_speed',
        'rto_rate',
        'success_rate',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
