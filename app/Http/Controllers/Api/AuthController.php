<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'engine_number' => ['nullable', 'string', 'max:50'],
            'user_role' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'password_hash' => Hash::make($data['password']),
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'engine_number' => $data['engine_number'] ?? null,
            'user_role' => $data['user_role'] ?? 'customer',
            'qr_code' => 'GHAZI:'.Str::upper(Str::random(32)),
            'is_active' => false,
            'approval_status' => 'pending',
        ]);

        SecurityLog::create([
            'user_id' => $user->id,
            'log_type' => 'qr_session',
            'is_successful' => true,
        ]);

        return response()->json($user, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $phone = $request->input('phone', $request->input('username'));
        $password = $request->input('password');

        $request->merge(['phone' => $phone, 'password' => $password]);
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
            'device_info' => ['nullable', 'string'],
            'ip_address' => ['nullable', 'string', 'max:45'],
        ]);

        $user = User::query()->where('phone', $data['phone'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password_hash)) {
            SecurityLog::create([
                'user_id' => $user?->id,
                'log_type' => 'failed_login',
                'ip_address' => $data['ip_address'] ?? $request->ip(),
                'is_successful' => false,
                'error_message' => 'Invalid credentials',
            ]);

            return response()->json(['detail' => 'بيانات الدخول غير صحيحة'], 401);
        }

        if ($user->approval_status === 'pending') {
            return response()->json(['detail' => 'حسابك قيد المراجعة، يرجى الانتظار حتى يتم قبولك من قِبَل الإدارة'], 403);
        }
        if ($user->approval_status === 'rejected') {
            return response()->json(['detail' => 'تم رفض حسابك. السبب: '.($user->rejection_reason ?? 'لم يُحدد سبب')], 403);
        }
        abort_if(! $user->is_active, 400, 'الحساب غير نشط');

        $token = $user->createToken($data['device_info'] ?? 'mobile')->plainTextToken;

        SecurityLog::create([
            'user_id' => $user->id,
            'log_type' => 'qr_session',
            'ip_address' => $data['ip_address'] ?? $request->ip(),
            'is_successful' => true,
        ]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'otp' => ['required', 'string'],
            'device_fingerprint' => ['nullable', 'string'],
        ]);

        $user = User::query()->where('phone', $data['phone'])->firstOrFail();

        abort_if($data['otp'] !== '123456', 400, 'رمز التحقق غير صحيح');

        $user->forceFill(['two_factor_enabled' => true])->save();

        return response()->json(['message' => 'تم تفعيل التحقق الثنائي بنجاح']);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'access_token' => $user->createToken('refresh')->plainTextToken,
            'token_type' => 'bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        SecurityLog::create([
            'user_id' => $request->user()->id,
            'log_type' => 'qr_session',
            'is_successful' => true,
            'error_message' => 'User logged out',
        ]);

        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $user = $request->user();
        abort_if(! Hash::check($data['current_password'], $user->password_hash), 400, 'كلمة المرور الحالية غير صحيحة');

        $user->forceFill([
            'password_hash' => Hash::make($data['new_password']),
        ])->save();

        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح']);
    }

    public function enableTwoFactor(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
            'verification_code' => ['required', 'string'],
        ]);

        abort_if($data['verification_code'] !== '123456', 400, 'رمز التحقق غير صحيح');

        $user = $request->user();
        abort_if(isset($data['phone']) && $data['phone'] !== $user->phone, 400, 'رقم الهاتف غير مطابق للحساب');

        $user->forceFill([
            'two_factor_enabled' => true,
            'phone_verified' => true,
        ])->save();

        SecurityLog::create([
            'user_id' => $user->id,
            'log_type' => 'two_factor_enabled',
            'ip_address' => $request->ip(),
            'is_successful' => true,
        ]);

        return response()->json(['message' => 'تم تفعيل التحقق الثنائي بنجاح']);
    }

    public function disableTwoFactor(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->forceFill(['two_factor_enabled' => false])->save();

        SecurityLog::create([
            'user_id' => $user->id,
            'log_type' => 'two_factor_disabled',
            'ip_address' => $request->ip(),
            'is_successful' => true,
        ]);

        return response()->json(['message' => 'تم تعطيل التحقق الثنائي بنجاح']);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'user_role' => $user->user_role,
            'is_active' => $user->is_active,
            'phone_verified' => $user->phone_verified,
            'two_factor_enabled' => $user->two_factor_enabled,
            'account_locked' => $user->account_locked,
        ]);
    }
}
