<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AuthLoginRequest;
use App\Http\Requests\Api\V1\AuthRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/login",
     * operationId="Login",
     * tags={"Auth"},
     * summary="User Login",
     * description="Login User Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password")
     *            )
     *        )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorised."),
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
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            Auth::user()->token = Auth::user()->createToken('authToken')->plainTextToken;
            return new UserResource(Auth::user());
        } else {
            return abort(401, 'Unauthorised.');
        }
    }

    /**
     * @OA\Post(
     * path="/register",
     * operationId="Register",
     * tags={"Auth"},
     * summary="User Register",
     * description="User Register here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name","email", "password", "password_confirmation"},
     *                 @OA\Property(property="name", type="text"),
     *                 @OA\Property(property="email", type="text"),
     *                 @OA\Property(property="password", type="password"),
     *                 @OA\Property(property="password_confirmation", type="password")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Register Successfully",
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
        $data = $request->only('name', 'email', 'password');
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->token = $user->createToken('authToken')->plainTextToken;
        return new UserResource($user);
    }
}
