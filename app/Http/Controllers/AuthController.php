<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendVerificationEmail;
use App\Http\Requests\VerifyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function user()
    {
        return Auth::user();
    }
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode = mt_rand(100000, 999999),
            'code_expired_in' => now()->addSeconds(300),

        ]);

        //dispatch(new SendVerificationEmail($user, $verificationCode));

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Tài khoản hoặc mật khẩu không chính xác',
            ], 401);
        }

        $user = $request->user();

        /*     if (!$user->is_verified) {
            Auth::logout();
            return response()->json([
                'id' => $user->id,
                'message' => 'Tài khoản chưa được xác minh, vui lòng vào Email để xác minh',
            ], 401);
        } */

        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json([
            'message' => 'Successfully logged out.',
        ], 200);
    }
}
