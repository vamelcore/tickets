<?php

namespace App\Http\Middleware;

use App\Helpers\PrintHelper;
use App\Helpers\StorageHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    public function terminate($request, $response)
    {
        Log::build([
            'driver' => 'single',
            'path' => StorageHelper::getLogPath('api'),
        ])->info(PrintHelper::printMessage([
            'url' => $request->fullUrl(),
            'method' => $request->getMethod(),
            'request' => $request->all(),
            'code' => $response->getStatusCode(),
            'response' => json_decode($response->getContent(), true)
        ]));
    }
}
