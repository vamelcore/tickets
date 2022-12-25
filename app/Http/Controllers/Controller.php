<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *    title="SandBOX API Documentnation",
 *    version=L5_SWAGGER_CONST_API_VERSION,
 *    description="Swagger OpenApi Documentnation"
 * )
 * @OA\Server(
 *    url=L5_SWAGGER_CONST_HOST,
 *    description="API Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
