<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:customers',
                'password' => 'required|confirmed'
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create customer
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if ($customer) {
            return response()->json([
                'success' => true,
                'user' => $customer,
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );
        // error response validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // get email and password
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('api_customer')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email and password is incorrect'
            ], 401);
        }
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api_customer')->user(),
            'token' => $token,
        ], 200);
    }
    public function getUser()
    {
        return response()->json([
            'status' => true,
            'user' => auth()->guard('api_customer')->user()
        ], 200);
    }
    public function refreshToken(Request $request)
    {
        // refreshToken
        $refreshToken = JWTAuth::refresh(JWTAuth::getToken());
        // set user dengan token baru
        $user = JWTAuth::setToken($refreshToken)->toUser();
        //set header "Authorization" dengan type Bearer + "token" baru  
        $request->headers->set('Authorization', 'Bearer' . $refreshToken);

        // response data user dangn token baru
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $refreshToken,
        ], 200);
    }

    public function logout()
    {
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());
        if ($removeToken) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Logout'
            ]);
        }
    }
}