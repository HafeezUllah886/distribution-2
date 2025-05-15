<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(request $request)
    {
        $validate = validator::make($request->all(), [
            'user_name' => 'required',
            'password' => 'required' 
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validate->errors()
            ], 422);
        }

        $user = User::where('name', $request->user_name)->first();

        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'message' => 'Invalid username or password'
            ], 401);
        }
        
        if($user->status == "Inactive")
        {
            return response()->json([
                'message' => 'Account is inactive'
            ], 403);
        }

        $token = $user->createToken($request->user_name);
 
        return [
            'user' => $user,
            'token' => $token->plainTextToken,
            'message' => 'Logged in successfully',
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function logout(request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out successfully',
        ];
    }

}
