<?php

namespace App\Services\Api\V1;

use App\Contracts\Api\V1\AuthInterface;
use App\Helpers\PasswordHelper;
use App\Http\Requests\Api\V1\AuthLoginRequest;
use App\Http\Requests\Api\V1\AuthPasswordRequest;
use App\Http\Requests\Api\V1\AuthRegisterRequest;
use App\Http\Requests\Api\V1\AuthRestoreRequest;
use App\Http\Resources\Api\ErrorResource;
use App\Http\Resources\Api\ResponseResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Mail\RestoreMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService implements AuthInterface
{
    /**
     * @param AuthLoginRequest $request
     * @return JsonResponse
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $data = $request->only('email', 'password');
        $remember = $request->remember ?? false;
        if (Auth::attempt($data, $remember)) {
            Auth::user()->token = Auth::user()->createToken('authToken')->plainTextToken;

            return (new UserResource(Auth::user()))->response();
        }

        return (new ErrorResource([
            'message' => 'User not found.'
        ]))->response()->setStatusCode(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return (new ResponseResource([
            'id' => Auth::id(),
            'logout' => true,
        ]))->response();
    }

    /**
     * @param AuthRegisterRequest $request
     * @return JsonResponse
     */
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        $data = $request->only('name', 'email', 'password');
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->token = $user->createToken('authToken')->plainTextToken;

        event(new Registered($user));

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param AuthRestoreRequest $request
     * @return JsonResponse
     */
    public function passwordRestore(AuthRestoreRequest $request): JsonResponse
    {
        $data = $request->only('email');
        $user = User::where(['email' => $data['email']])->first();
        $password = PasswordHelper::randString();
        $user->password = Hash::make($password);
        $user->save();

        event(new PasswordReset($user));

        Mail::to($user)->send(new RestoreMail($user->name,$password));

        return (new ResponseResource([
            'email' => $data['email'],
            'send' => true,
        ]))->response();
    }

    /**
     * @param AuthPasswordRequest $request
     * @return JsonResponse
     */
    public function passwordUpdate(AuthPasswordRequest $request): JsonResponse
    {
        $data = $request->only('password');
        $user = User::where(['id' => Auth::id()])->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new ResponseResource([
            'id' => Auth::id(),
            'update' => true,
        ]))->response();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function emailVerification(Request $request): JsonResponse
    {
        $send = false;
        if (!$request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
            $send = true;
        }

        return (new ResponseResource([
            'email' => $request->user()->getEmailForVerification(),
            'send' => $send,
        ]))->response();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function emailVerified(Request $request): JsonResponse
    {
        return (new ResponseResource([
            'email' => $request->user()->getEmailForVerification(),
            'verified' => $request->user()->hasVerifiedEmail(),
        ]))->response();
    }
}
