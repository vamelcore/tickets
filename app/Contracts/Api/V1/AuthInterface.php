<?php

namespace App\Contracts\Api\V1;

use App\Http\Requests\Api\V1\AuthLoginRequest;
use App\Http\Requests\Api\V1\AuthPasswordRequest;
use App\Http\Requests\Api\V1\AuthRegisterRequest;
use App\Http\Requests\Api\V1\AuthRestoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface AuthInterface
{
    public function login(AuthLoginRequest $request): JsonResponse;
    public function logout(): JsonResponse;
    public function register(AuthRegisterRequest $request): JsonResponse;
    public function passwordRestore(AuthRestoreRequest $request): JsonResponse;
    public function passwordUpdate(AuthPasswordRequest $request): JsonResponse;
    public function emailVerification(Request $request): JsonResponse;
    public function emailVerified(Request $request): JsonResponse;
}
