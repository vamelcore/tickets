<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\VersionCollection;
use App\Models\ApiVersion;
use Illuminate\Support\Facades\Cache;

class VersionController extends Controller
{
    /**
     * @OA\Get(
     *      path="/version",
     *      operationId="ApiVersion",
     *      tags={"Auth"},
     *      summary="Get list of api versions",
     *      description="Returns list of api versions",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/VersionCollection")
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=500, description="Internal server error"),
     * )
     */
    public function index()
    {
        return new VersionCollection(Cache::remember('api_version', config('cache.default_cache_time'), function (){
            return ApiVersion::latest('id')->get();
        }));
    }
}
