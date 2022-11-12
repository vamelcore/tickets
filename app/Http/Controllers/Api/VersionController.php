<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\VersionResource;
use App\Models\ApiVersion;
use Illuminate\Support\Facades\Cache;

class VersionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/version",
     *     operationId="ApiVersion",
     *     tags={"Info"},
     *     summary="Get list of api versions",
     *     description="Returns list of api versions",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/VersionResource"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return VersionResource::collection(Cache::remember(
            'api_version',
            config('cache.default_cache_time'),
            function (){
                return ApiVersion::latest('id')->limit(20)->get();
            }
        ));
    }
}
