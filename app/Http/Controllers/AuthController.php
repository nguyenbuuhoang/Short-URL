<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthServices;
use App\Jobs\SendVerificationEmail;
use App\Http\Requests\VerifyRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServices $authService)
    {
        $this->authService = $authService;
    }
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
        dispatch(new SendVerificationEmail($user, $verificationCode));

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid username or password',
            ], 401);
        }

        $user = $request->user();
        if (!$user->is_verified) {
            Auth::logout();
            return response()->json([
                'id' => $user->id,
                'message' => 'Account not verified, please check your email for verification',
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'success' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'role' => $user->getRoleNames(),
        ], 200);
    }
    public function verify(VerifyRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User does not exist.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'The account has been previously verified.'], 400);
        }

        $verificationCode = $request->input('verification_code');

        if ($user->verification_code !== $verificationCode) {
            return response()->json(['message' => 'The verification code is invalid.'], 400);
        }

        if (!empty($user->code_expired_in) && now() > $user->code_expired_in) {
            return response()->json(['message' => 'The verification code has expired.'], 400);
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

            return response()->json(['message' => 'Verification successful.', 'token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'There was an error while verifying the account.'], 500);
        }
    }

    public function resendVerificationEmail($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'The account has been previously verified.'], 400);
        }

        $verificationCode = mt_rand(100000, 999999);

        $user->update([
            'verification_code' => $verificationCode,
            'code_expired_in' => now()->addSeconds(300),
        ]);

        dispatch(new SendVerificationEmail($user, $verificationCode));

        return response()->json(['message' => 'Resent verification email with a new verification code.'], 200);
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
