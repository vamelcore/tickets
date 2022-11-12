<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\ResponseResource;
use App\Http\Requests\GuestEmailVerificationRequest;

class EmailVerificationController extends Controller
{
    public function index(GuestEmailVerificationRequest $request)
    {
        $user = $request->verify();

        return new ResponseResource([
            'email' => $user->email,
            'verified' => true,
        ]);
    }
}
