<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\VersionResource;
use App\Models\ApiVersion;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    public function index()
    {
        $versions = VersionResource::collection(Cache::remember(
            'api_version',
            config('cache.default_cache_time'),
            function (){
                return ApiVersion::latest('id')->limit(20)->get();
            }
        ));

        return view('index', compact('versions'));
    }
}
