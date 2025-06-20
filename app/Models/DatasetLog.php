<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatasetLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dataset_name',
        'status',
        'message',
        'metadata',
    ];
}
