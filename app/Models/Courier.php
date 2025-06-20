<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourierPerformance;
use App\Models\CourierRate;
use App\Models\Shipment;
use App\Models\DeliveryResult;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_credentials',
    ];

    protected $casts = [
        'api_credentials' => 'array',
    ];

    public function performances()
    {
        return $this->hasMany(CourierPerformance::class);
    }

    public function rates()
    {
        return $this->hasMany(CourierRate::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function deliveryResults()
    {
        return $this->hasMany(DeliveryResult::class);
    }
}
