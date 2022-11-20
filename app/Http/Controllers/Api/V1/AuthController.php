<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Api\V1\AuthInterface;
use App\Helpers\PasswordHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AuthPasswordRequest;
use App\Http\Requests\Api\V1\AuthRestoreRequest;
use App\Http\Requests\Api\V1\AuthLoginRequest;
use App\Http\Requests\Api\V1\AuthRegisterRequest;
use App\Http\Resources\Api\ErrorResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Resources\Api\ResponseResource;
use App\Mail\RestoreMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public $service;

    public function __construct(AuthInterface $authService)
    {
        $this->service = $authService;
    }

    /**
     * @OA\Post(
     * path="/login",
     * operationId="Login",
     * tags={"Auth"},
     * summary="User login",
     * description="User login",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password"),
     *            ),
     *        ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="text", description="User email"),
     *                 @OA\Property(property="password", type="password", description="Password"),
     *                 example={"email":"", "password":""},
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The email field is required.","The email must be a valid email address."}
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The password field is required."}
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(AuthLoginRequest $request)
    {
        return $this->service->login($request);
    }

    /**
     * @OA\Post(
     * path="/logout",
     * operationId="Logout",
     * tags={"Auth"},
     * summary="User logout",
     * description="User logout",
     * security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(type="integer", example="100", description="User identifier", property="id"),
     *                 @OA\Property(type="boolean", example="true", description="Logout status", property="logout"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     )
     * )
     */
    public function logout()
    {
        return $this->service->logout();
    }

    /**
     * @OA\Post(
     * path="/register",
     * operationId="Register",
     * tags={"Auth"},
     * summary="User register",
     * description="User register",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name","email", "password", "password_confirmation"},
     *                 @OA\Property(property="name", type="text"),
     *                 @OA\Property(property="email", type="text"),
     *                 @OA\Property(property="password", type="password"),
     *                 @OA\Property(property="password_confirmation", type="password"),
     *             ),
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="text", description="User name"),
     *                 @OA\Property(property="email", type="text", description="User email"),
     *                 @OA\Property(property="password", type="password", description="Password"),
     *                 @OA\Property(property="password_confirmation", type="password", description="Password confirmation"),
     *                 example={"name": "", "email":"", "password":"", "password_confirmation":""},
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Register successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The email field is required.","The email must be a valid email address."}
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The password field is required."}
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function register(AuthRegisterRequest $request)
    {
        return $this->service->register($request);
    }

    /**
     * @OA\Post(
     * path="/password-restore",
     * operationId="PasswordRestore",
     * tags={"Auth"},
     * summary="Restore password",
     * description="Restore password",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email"},
     *               @OA\Property(property="email", type="email"),
     *            ),
     *        ),
     *        @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="email", description="User email"),
     *                 example={"email": ""},
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(type="text", example="admin@email.com", description="User email", property="email"),
     *                 @OA\Property(type="boolean", example="true", description="Email send status", property="send"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The email field is required.","The email must be a valid email address."}
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function passwordRestore(AuthRestoreRequest $request)
    {
        return $this->service->passwordRestore($request);
    }

    /**
     * @OA\Put(
     * path="/password-update",
     * operationId="PasswordUpdate",
     * tags={"Auth"},
     * summary="Update password",
     * description="Update password",
     * security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="password", type="password", description="Password"),
     *                 @OA\Property(property="password_confirmation", type="password", description="Password confirmation"),
     *                 example={"password": "", "password_confirmation": ""},
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(type="integer", example="100", description="User identifier", property="id"),
     *                 @OA\Property(type="boolean", example="true", description="Update password status", property="update"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Your email address is not verified."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The password field is required."}
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function passwordUpdate(AuthPasswordRequest $request)
    {
        return $this->service->passwordUpdate($request);
    }

    /**
     * @OA\Post(
     * path="/email-verification",
     * operationId="EmailVerification",
     * tags={"Auth"},
     * summary="Send email verification",
     * description="Send new email verification message",
     * security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Email sended successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(type="text", example="admin@email.com", description="User email", property="email"),
     *                 @OA\Property(type="boolean", example="true", description="Email send status", property="send"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     )
     * )
     */
    public function emailVerification(Request $request)
    {
        return $this->service->emailVerification($request);
    }

    /**
     * @OA\Get(
     * path="/email-verified",
     * operationId="EmailVerificationStatus",
     * tags={"Auth"},
     * summary="Check email verification status",
     * description="Check email verification status",
     * security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(type="text", example="admin@email.com", description="User email", property="email"),
     *                 @OA\Property(type="boolean", example="true", description="Email verification status", property="verified"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated."),
     *         )
     *     )
     * )
     */
    public function emailVerified(Request $request)
    {
        return $this->service->emailVerified($request);
    }
}
