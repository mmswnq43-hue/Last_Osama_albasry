<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function store(Request $request, GasYemenSubscriptionService $service)
    {
        // ── تحقق من وجود بيانات الجلسة ──────────────────────
        $data = session('reg_data');

        if (! $data || empty($data['phone']) || empty($data['password']) || empty($data['plan'])) {
            return redirect()->route('customer.register')
                ->with('error', 'انتهت صلاحية الجلسة. يرجى البدء من جديد.');
        }

        // ── التحقق من الملف ──────────────────────────────────
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'receipt.required' => 'يرجى إرفاق سند التحويل البنكي',
            'receipt.mimes'    => 'الملف يجب أن يكون صورة (JPG/PNG/WEBP) أو PDF',
            'receipt.max'      => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',
        ]);

        // ── التحقق من عدم تكرار البيانات ────────────────────
        if (User::where('phone', $data['phone'])->exists()) {
            return back()->withErrors([
                'receipt' => 'رقم الجوال مسجّل مسبقاً. يرجى العودة وإدخال رقم آخر.',
            ]);
        }

        if ($data['email'] && User::where('email', $data['email'])->exists()) {
            return back()->withErrors([
                'receipt' => 'البريد الإلكتروني مسجّل مسبقاً. يرجى العودة وتغييره.',
            ]);
        }

        // ── رفع الملف بمسار صريح (يحل مشكلة double-prefix مع AWS_ROOT) ──
        // نحدد المسار يدوياً بدلاً من الاعتماد على store() لتجنب
        // تكرار AWS_ROOT في المسار المُعاد
        $file      = $request->file('receipt');
        $ext       = strtolower($file->getClientOriginalExtension());
        $filename  = 'receipts/' . Str::random(40) . '.' . $ext;
        $disk      = config('filesystems.default', 'public');

        Storage::disk($disk)->put($filename, $file->get());
        $receiptPath = $filename; // receipts/xxxxxxxxxx.pdf

        // ── إنشاء الحساب والاشتراك ───────────────────────────
        DB::transaction(function () use ($data, $receiptPath, $service) {
            $user = User::create([
                'full_name'       => $data['full_name'],
                'phone'           => $data['phone'],
                'email'           => $data['email'],
                'password_hash'   => Hash::make($data['password']),
                'vehicle_type'    => $data['vehicle_type'],
                'engine_number'   => $data['engine_number'],
                'user_role'       => 'customer',
                'is_active'       => false,
                'approval_status' => 'pending',
            ]);

            $service->createSubscription($user, $data['plan'], 'pending', $receiptPath);
        });

        // ── مسح الجلسة والتوجيه لصفحة الدخول ───────────────
        session()->forget('reg_data');

        return redirect()->route('login')
            ->with('registered', 'تم إنشاء حسابك بنجاح! حسابك قيد المراجعة بانتظار موافقة الإدارة.');
    }
}
