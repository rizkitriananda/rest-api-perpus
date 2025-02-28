<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // register
    public function register(Request $request){
        $credential = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users',
            'password' => 'required|regex:/[!@#$%^&*]/|min:8',
            'role_id' => ['nullable', Rule::exists('roles' ,'id')]
        ]);

        $credential['password'] = Hash::make($credential['password']);

        $user = User::create($credential);

        return response()->json([
            'status' => 'success',
            'message' => 'User registration successful',
            'data' => $user->makeHidden('email')
        ], 201);
    }

    // login
    public function login(Request $request){
    $credential = $request->validate([
            'email' => ['required','email'],
            'password' => 'required|regex:/[!@#$%^&*]/|min:8'
        ]);


        if(!Auth::once($credential)){
            return response()->json([
                'status' => 'error' , 
                'message' => 'Email or password is doesn’t match'], 401);
        }

        $user =  Auth::user();

        /** @var \App\Models\User $user */
        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'status' => 'success',
            'message' => 'User login has successfully',
            'data' => [
                'user' => new UserResource($user),
                'tokens' => $token
            ]
        ], 200);
    }

    // logout
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' =>  'User logout has successfully'
        ], 201);
    }

}
