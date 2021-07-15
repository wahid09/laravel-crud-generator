<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken($user->email . '-' . now())->plainTextToken;
        return response()->json([
                    'status' => true, 
                    'message' => 'Register Successfully',
                    'user' => $user,
                    'token' => $token
                ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ],
        [
            'email.exists' => 'Email Doesn\'t Exists'
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $token = $user->createToken($user->email . '-' . now())->plainTextToken;

            return response()->json([
                'status' => true, 
                'message' => 'Login Successfully',
                'user' => $user,
                'token' => $token
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => ['message' => ['Incorrect Password']]
            ], 401);
        }
    }

    public function logout(Request $request){
        $user = Auth::user();
        // Revoke all tokens...
        $user->tokens()->delete();
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true, 
            'message' => 'Logout Successfully',
            'user' => $user
        ]);
        // Revoke a specific token...
        // $user->tokens()->where('id', $tokenId)->delete();
    }
}
