<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class ApiVersionObserver
{
    public function created()
    {
        Cache::forget('api_version');
    }
}
