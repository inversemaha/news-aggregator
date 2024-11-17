<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     summary="Register a new user",
     *     description="Create a new user account.",
     *     operationId="registerNewUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", description="User's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", description="User's password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", description="Password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */

    public function register(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed'
        ]);

        // If validation passes, create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password'])
        ]);

        // Return success response
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201); // HTTP 201 Created
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User login",
     *     description="Authenticate a user and return an access token.",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", description="User's email address"),
     *             @OA\Property(property="password", type="string", format="password", description="User's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string'
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401); // HTTP 401 Unauthorized
        }

        $user = auth()->user();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200); // HTTP 200 OK
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="User logout",
     *     description="Log out the authenticated user and revoke the access token.",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully'
        ], 200); // HTTP 200 OK
    }
}
