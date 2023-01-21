<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ZoneResource;

class ZoneController extends Controller
{
    public function index()
    {
        return ZoneResource::collection(Zone::all());
    }

    public function show(Zone $zone)
    {
        return ZoneResource::make($zone);
    }
}
