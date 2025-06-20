<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatasetBuildLog extends Model
{
    protected $fillable = [
        'dataset_name',
        'status',
        'file_path',
        'record_count',
        'error_message',
    ];
}
