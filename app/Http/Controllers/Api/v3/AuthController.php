<?php

namespace App\Http\Controllers\Api\v3;

use App\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


/**
 * API Version 3 - AuthController
 */
class AuthController extends Controller
{
    /**
     * Register a User
     *
     * Provide registration capability to the client app
     *
     * Registration requires:
     * - name
     * - valid email address
     * - password (min 6 character)
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        //  check https://laravel.com/docs/12.x/validation#rule-email
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users',],
                'password' => ['required', 'string', 'min:6', 'confirmed',],
                'password_confirmation' => ['required', 'string', 'min:6',],
            ]
        );

        if ($validator->fails()) {
            return ApiResponse::error(
                ['error' => $validator->errors()],
                'Registration details error',
                401
            );
        }

        $user = User::create([
            'name' => $validator->validated()['name'],
            'email' => $validator->validated()['email'],
            'password' => Hash::make(
                $validator->validated()['password']
            ),
        ]);

        $user->assignRole('client'); // assign client role (new)

        $token = $user->createToken('MyAppToken')->plainTextToken;

        return ApiResponse::success(
            [
                'token' => $token,
                'user' => $user,
            ],
            'User successfully created',
            201
        );
    }

    public function verifyAccount(Request $request)
    {
        // email verification endpoint
    }

    /**
     * User Login
     *
     * Attempt to log the user in using email
     * and password based authentication.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Alternative using string based validation rules
        // $validator = Validator::make($request->all(), [
        //     'email' => 'required|string|email|max:255',
        //     'password' => 'required|string|min:6',
        // ]);
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255',],
            'password' => ['required', 'string',],
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                [
                    'error' => $validator->errors()
                ],
                'Invalid credentials',
                401
            );
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return ApiResponse::error(
                [],
                'Invalid credentials',
                401
            );
        }

        $user = Auth::user();

        $token = $user->createToken('MyAppToken')->plainTextToken;

        $tokens = $user->tokens();
        return ApiResponse::success(
            [
                'token' => $token,
                'user' => $user,
                'tokens' => $tokens
            ],
            'Login successful'
        );
    }

    /**
     * User Profile API
     *
     * Provide the user's profile information, including:
     * - name,
     * - email,
     * - email verified,
     * - created at, and
     * - updated at.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        return ApiResponse::success(
            [
                'user' => $request->user(),
            ],
            'User profile request successful'
        );
    }

    /**
     * User Logout
     *
     * Log user out of system, cleaning token and session details.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return ApiResponse::success(
            [],
            'Logout successful'
        );
    }

    public function edit(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'], // must include password_confirmation field if sent
            'current_password' => ['required_with:password', 'string'], // only required if password change attempted
        ]);

        if ($validator->fails()) {
            return ApiResponse::error(
                ['error' => $validator->errors()],
                'Invalid input',
                422
            );
        }

        // If password change requested, verify the old one first
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return ApiResponse::error([], 'Current password is incorrect', 403);
            }
            $user->password = Hash::make($request->password);
        }

        // Update fields (if present)
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return ApiResponse::success([
            'user' => $user->only(['id', 'name', 'email']),
        ], 'Profile updated successfully');

        /*
         * POTENTIAL PAYLOADS
         *      Password
            {
                "current_password": "OldPassword1",
                "password": "NewPassword1",
                "password_confirmation": "NewPassword1"
            }
         *
         *      Email
            {
                "email": "new@example.com"
            }
         *
         *      Name
            {
                "name": "New Name"
            }
         *
         */
    }

    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255',],
            'password' => ['required', 'string',],
        ]);

        //get current user for later
        $user = $request->user();

        if ($validator->fails()) {
            return ApiResponse::error(
                ['error' => $validator->errors()],
                'Invalid credentials',
                401
            );
        }

        // check password and email input against records for extra security
        if ($user->email !== $request->email) {
            return ApiResponse::error([], 'Email does not match the signed-in account', 403);
        }
        if (!Hash::check($request->password, $user->password)) {
            return ApiResponse::error([], 'Invalid password', 403);
        }

        // logout + delete
        $user->tokens()->delete();
        $user->delete();

        return ApiResponse::success([],'Account deleted successfully');
    }
}
