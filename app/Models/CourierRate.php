<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'pincode',
        'rate',
        'last_updated',
    ];

    protected $casts = [
        'last_updated' => 'datetime',
    ];

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
