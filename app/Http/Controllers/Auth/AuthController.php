<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{

    public function Register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
        ]);

        
        
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            "role_id" => Role::where('name', 'admin')->first()->id,
            'password' => bcrypt($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $userData=DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.*', 'roles.name as role_name')
        ->where('users.id', $user->id)
        ->first();
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $userData,
            'token' => $token,
        ], 201);
    }
    public function Login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $userData=DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.*', 'roles.name as role_name')
        ->where('users.id', $user->id)
        ->first();
        return response()->json([
            'token' => $token,
            'user' => $userData,
        ]);

    }

    public function getUserData(Request $request)
    {
        $user = DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.*', 'roles.name as role_name')
        ->where('users.id', $request->user()->id)
        ->first();
        return response()->json($user);
    }

    public function Logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
