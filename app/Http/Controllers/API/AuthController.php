<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Authentication",
 *      description="API pour l'authentification avec Laravel et Swagger",
 *      @OA\Contact(
 *          email="support@example.com"
 *      ),
 * )
 */

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         ),
     *     ),
     *     @OA\Response(response=200, description="User registered successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     * )
     */

    public function register (Request $request){

        $validator = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255|unique:users',
            'password'=>'required|string|min:6|max:12',
        ]);

       if (!$validator) {
       return response()->json($validator->errors());
       } 

       $user= User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password),
       ]);

       $token = $user->createToken('auth_token')->plainTextToken;
       return response()->json([
        'message' => 'user added successful',
        'data' => $user,
        'auth_token' => $token,
        'token_type' =>'Bearer',
         ]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         ),
     *     ),
     *     @OA\Response(response=200, description="User logged in successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     * )
     */

    public function login(Request $request){

        if (!Auth::attempt($request->only('email','password'), true)) {
            
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message'=>'hi '. $user->name . ' welcome to home', 'auth_token' => $token, 'token_type' =>'Bearer',]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     @OA\Response(response=200, description="User logged out successfully"),
     * )
     */

    public function logout (){
     Auth::user()->tokens()->delete();
     return[
        'message'=>'You have successfully logged out and the token was successfully delete'
     ] ;  
    } 
}