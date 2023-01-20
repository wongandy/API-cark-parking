<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'user_id',
        'vehicle_id',
        'start_time',
        'stop_time',
        'total_price',
    ];
}
