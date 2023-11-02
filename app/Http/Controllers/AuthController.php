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
        $user->assignRole('user');
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
        $roles = $user->getRoleNames();
        if (!$user->is_verified) {
            Auth::logout();
            return response()->json([
                'id' => $user->id,
                'message' => 'Tài khoản chưa được xác minh, vui lòng vào Email để xác minh',
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'success' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $token,
            'role' => $roles,
        ], 200);
    }
    public function verify(VerifyRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Tài khoản đã được xác minh trước đó.'], 400);
        }

        $verificationCode = $request->input('verification_code');

        if ($user->verification_code !== $verificationCode) {
            return response()->json(['message' => 'Mã xác minh không hợp lệ.'], 400);
        }

        if (!empty($user->code_expired_in) && now() > $user->code_expired_in) {
            return response()->json(['message' => 'Mã xác minh đã hết hạn.'], 400);
        }

        try {
            $user->update([
                'is_verified' => true,
                'email_verified_at' => now(),
                'verification_code' => null,
                'code_expired_in' => null,
            ]);

            Auth::login($user);
            $token = $user->createToken('token')->plainTextToken;

            return response()->json(['message' => 'Xác minh thành công.', 'token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Đã xảy ra lỗi khi xác minh tài khoản.'], 500);
        }
    }

    public function resendVerificationEmail($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Tài khoản đã được xác minh trước đó.'], 400);
        }

        $verificationCode = mt_rand(100000, 999999);

        $user->update([
            'verification_code' => $verificationCode,
            'code_expired_in' => now()->addSeconds(300),
        ]);

        dispatch(new SendVerificationEmail($user, $verificationCode));

        return response()->json(['message' => 'Đã gửi lại email xác minh với mã xác minh mới.'], 200);
    }
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json([
            'message' => 'Successfully logged out.',
        ], 200);
    }
}
