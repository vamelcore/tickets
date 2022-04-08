<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiVersionCollection;
use App\Models\ApiVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiVersionController extends Controller
{
    public function index()
    {
        return new ApiVersionCollection(Cache::remember('api_version', config('cache.default_cache_time'), function (){
            return ApiVersion::all();
        }));
    }
}
