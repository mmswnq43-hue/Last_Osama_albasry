<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Subscription;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /** عرض صفحة الدفع */
    public function showPay()
    {
        $req = session('sub_request');

        if (! $req || empty($req['plan'])) {
            return redirect()->route('customer.status')
                ->with('error', 'انتهت الجلسة. يرجى اختيار الباقة مجدداً.');
        }

        $plans        = app(GasYemenSubscriptionService::class)->plans();
        $plan         = $plans[$req['plan']] ?? null;
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('customer.subscription-pay', compact('req', 'plan', 'bankAccounts'));
    }

    /** استقبال طلب الاشتراك / التجديد */
    public function store(Request $request)
    {
        $req = session('sub_request');

        if (! $req || empty($req['plan'])) {
            return redirect()->route('customer.status')
                ->with('error', 'انتهت الجلسة. يرجى اختيار الباقة مجدداً.');
        }

        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'receipt.required' => 'يرجى إرفاق سند التحويل البنكي',
            'receipt.mimes'    => 'الملف يجب أن يكون صورة (JPG/PNG/WEBP) أو PDF',
            'receipt.max'      => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',
        ]);

        $user  = Auth::user();
        $plans = app(GasYemenSubscriptionService::class)->plans();
        $plan  = $plans[$req['plan']] ?? null;

        abort_unless($plan, 400, 'باقة غير صالحة');

        // رفع السند
        $file     = $request->file('receipt');
        $ext      = strtolower($file->getClientOriginalExtension());
        $filename = 'receipts/' . Str::random(40) . '.' . $ext;
        $disk     = config('filesystems.default', 'public');
        Storage::disk($disk)->put($filename, $file->get());

        // تحديد start_date بحسب نوع الاشتراك
        $type      = $req['type'] ?? 'immediate';
        $startDate = now();

        if ($type === 'after' && ! empty($req['active_end'])) {
            $startDate = \Carbon\Carbon::parse($req['active_end']);
        }

        // إنشاء الاشتراك المعلّق للمراجعة
        Subscription::create([
            'user_id'               => $user->id,
            'plan_type'             => $req['plan'],
            'price'                 => $plan['price'],
            'discount_percent'      => $plan['discount_percent'],
            'start_date'            => $startDate,
            'end_date'              => (clone $startDate)->addMonths($plan['duration_months']),
            'status'                => 'pending',
            'payment_receipt_image' => $filename,
            'remaining_cylinders'   => 0,
            'remaining_car_washes'  => $plan['car_washes'],
            'remaining_maintenance' => $plan['maintenance'],
            'notes'                 => 'renewal:' . $type,   // يستخدمه الأدمن عند الموافقة
            'monthly_liters'        => 0,
            'last_reset_date'       => now(),
        ]);

        session()->forget('sub_request');

        return redirect()->route('customer.status')
            ->with('success', 'تم إرسال طلب الاشتراك بنجاح! بانتظار موافقة الإدارة.');
    }
}
