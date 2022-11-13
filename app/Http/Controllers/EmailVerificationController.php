<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuestEmailVerificationRequest;

class EmailVerificationController extends Controller
{
    public function index(GuestEmailVerificationRequest $request)
    {
        $user = $request->verify();

        return view('email-verify', compact('user'));
    }
}
