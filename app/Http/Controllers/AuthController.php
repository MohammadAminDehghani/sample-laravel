<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    // Register user
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => [
                    'errors' => [
                        'status' => true,
                        'messages' => $validator->errors()
                    ]
                ]
            ], 400);
        }

        // Create user
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password
            ]);

            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    // Login user
//    public function login(Request $request)
//    {
//        // Validation
//        $validator = Validator::make($request->all(), [
//            'email' => 'required|string|email|max:255',
//            'password' => 'required|string|min:8',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json(['message' => 'Invalid email or password'], 400);
//        }
//        // Find user by email
//        $user = User::where('email', $request->email)->first();
//
//        // If user not found or password doesn't match
//        if (!$user || !$user->comparePassword($request->password)) {
//            return response()->json(['message' => 'Invalid email or password'], 401);
//        }
//        return response()->json(['message' => (!$user instanceof \App\Models\User)], 200);
//        // Generate JWT token
//        $token = JWTAuth::fromUser($user);
//
//
//        return response()->json(['user' => $user, 'token' => $token], 200);
//        try {
//
//
//        } catch (\Exception $e) {
//            return response()->json(['message' => 'Internal server error'], 500);
//        }
//    }

//    public function login(Request $request)
//    {
//        $credentials = $request->only('email', 'password');
//
//        if (!$token = JWTAuth::attempt($credentials)) {
//            return response()->json(['error' => 'Invalid credentials'], 401);
//        }
//
//        // Get the authenticated user
//        $user = auth()->user();
//
//        // Check if the user implements JWTSubject (debugging)
//        if (!$user instanceof \App\Models\User || !method_exists($user, 'getJWTIdentifier')) {
//            return response()->json(['error' => 'User does not implement JWTSubject'], 500);
//        }
//
//        // Generate JWT token
//        return response()->json([
//            'token' => $token,
//            'user' => $user
//        ]);
//    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        //return response()->json(['error' => $credentials], 401);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::user();
        return $this->respondWithToken($token);

    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid name'], 400);
        }

        try {
            $user = User::where('name', $request->name)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Generate new password
            $newPassword = str_random(8);
            $user->password = Hash::make($newPassword);
            $user->save();

            // Here you would send the new password via email (e.g., using Laravel Mail)
            // Mail::to($user->email)->send(new PasswordResetMail($newPassword));

            return response()->json(['message' => 'Password reset instructions sent'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function verify_token(Request $request){
        try {
            $token = $request->header('Authorization');
            $user = JWTAuth::parseToken()->authenticate();

            if ($user) {
                return response()->json(['valid' => true]);
            }
        } catch (\Exception $e) {
            return response()->json(['valid' => false], 401);
        }
    }


    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }

}

