<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\PasswordHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AuthRestoreRequest;
use App\Http\Requests\Api\V1\AuthLoginRequest;
use App\Http\Requests\Api\V1\AuthRegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Resources\Api\ResponseResource;
use App\Mail\RestoreMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/login",
     * operationId="Login",
     * tags={"Auth"},
     * summary="User Login",
     * description="User login",
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
     * path="/logout",
     * operationId="Logout",
     * tags={"Auth"},
     * summary="User Logout",
     * description="User logout",
     * security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Logout Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(type="integer", example="100", description="User identifier", property="id"),
     *                 @OA\Property(type="boolean", example="true", description="Logout status", property="logout"),
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
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return new ResponseResource([
            'id' => Auth::id(),
            'logout' => true
        ]);
    }

    /**
     * @OA\Post(
     * path="/register",
     * operationId="Register",
     * tags={"Auth"},
     * summary="User Register",
     * description="User register",
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

    /**
     * @OA\Post(
     * path="/restore",
     * operationId="Restore",
     * tags={"Auth"},
     * summary="Restore password",
     * description="Restore password",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email"},
     *               @OA\Property(property="email", type="email")
     *            )
     *        )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(type="text", example="admin@email.com", description="User email", property="email"),
     *                 @OA\Property(type="boolean", example="true", description="Restore status", property="restore"),
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
    public function restore(AuthRestoreRequest $request)
    {
        $email = $request->only('email');
        $user = User::where(['email' => $email])->first();
        $password = PasswordHelper::randString();
        $user->password = Hash::make($password);
        $user->save();

        Mail::to($user)->send(new RestoreMail($password));

        return new ResponseResource([
            'email' => $email,
            'success' => true
        ]);
    }
}
