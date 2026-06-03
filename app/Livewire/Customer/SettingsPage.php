<?php
namespace App\Livewire\Customer;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.customer')]
#[Title('الإعدادات - غازي')]
class SettingsPage extends Component
{
    public string $full_name = '';
    public string $email = '';
    public string $vehicle_type = '';
    public string $engine_number = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->full_name     = $user->full_name ?? '';
        $this->email         = $user->email ?? '';
        $this->vehicle_type  = $user->vehicle_type ?? '';
        $this->engine_number = $user->engine_number ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate([
            'full_name'     => 'required|string|max:100',
            'email'         => 'nullable|email|max:150',
            'vehicle_type'  => 'nullable|string|max:100',
            'engine_number' => 'nullable|string|max:100',
        ]);

        Auth::user()->update([
            'full_name'     => $this->full_name,
            'email'         => $this->email,
            'vehicle_type'  => $this->vehicle_type,
            'engine_number' => $this->engine_number,
        ]);

        $this->successMessage = 'تم حفظ البيانات بنجاح';
        $this->errorMessage   = '';
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($this->current_password, Auth::user()->password_hash)) {
            $this->errorMessage   = 'كلمة المرور الحالية غير صحيحة';
            $this->successMessage = '';
            return;
        }

        Auth::user()->update(['password_hash' => Hash::make($this->new_password)]);

        $this->current_password          = '';
        $this->new_password              = '';
        $this->new_password_confirmation = '';
        $this->successMessage            = 'تم تغيير كلمة المرور بنجاح';
        $this->errorMessage              = '';
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.customer.settings-page', ['user' => Auth::user()])
            ->layoutData(['showNav' => true]);
    }
}
