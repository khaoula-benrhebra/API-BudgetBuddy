<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AuthController extends Controller
{
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

    public function login(Request $request){

        if (!Auth::attempt($request->only('email','password'), true)) {
            
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message'=>'hi '. $user->name . ' welcome to home', 'auth_token' => $token, 'token_type' =>'Bearer',]);
    }

    public function logout (){
     Auth::user()->tokens()->delete();
     return[
        'message'=>'You have successfully logged out and the token was successfully delete'
     ] ;  
    } 
}