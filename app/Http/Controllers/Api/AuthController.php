<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->company_email,
                'phone' => $request->company_phone,
                'address' => $request->company_address,
                'city' => $request->company_city,
                'country' => $request->company_country,
                'tax_id' => $request->company_tax_id,
                'registration_number' => $request->company_registration_number,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'is_active' => true,
            ]);

            $user->assignRole('admin');

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'company' => $company,
                'token' => $token,
                'token_type' => 'Bearer',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Account is deactivated'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
