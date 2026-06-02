<?php

namespace App\Livewire\Admin\BankAccounts;

use App\Models\BankAccount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('الحسابات البنكية - غازي')]
class BankAccountsPage extends Component
{
    public bool   $showModal = false;
    public ?int   $editingId = null;

    public string $bank_name      = '';
    public string $account_name   = '';
    public string $account_number = '';
    public string $iban           = '';
    public string $currency       = 'YER';
    public string $notes          = '';
    public bool   $is_active      = true;

    public string $successMessage = '';
    public ?int   $confirmDeleteId = null;

    protected function rules(): array
    {
        return [
            'bank_name'      => 'required|string|max:100',
            'account_name'   => 'required|string|max:150',
            'account_number' => 'required|string|max:60',
            'iban'           => 'nullable|string|max:60',
            'currency'       => 'required|string|max:10',
            'notes'          => 'nullable|string|max:255',
            'is_active'      => 'boolean',
        ];
    }

    protected $messages = [
        'bank_name.required'      => 'اسم البنك مطلوب',
        'account_name.required'   => 'اسم صاحب الحساب مطلوب',
        'account_number.required' => 'رقم الحساب مطلوب',
    ];

    public function openCreate(): void
    {
        $this->reset(['editingId', 'bank_name', 'account_name', 'account_number', 'iban', 'notes']);
        $this->currency  = 'YER';
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $acc = BankAccount::findOrFail($id);
        $this->editingId      = $acc->id;
        $this->bank_name      = $acc->bank_name;
        $this->account_name   = $acc->account_name;
        $this->account_number = $acc->account_number;
        $this->iban           = $acc->iban ?? '';
        $this->currency       = $acc->currency;
        $this->notes          = $acc->notes ?? '';
        $this->is_active      = $acc->is_active;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            BankAccount::findOrFail($this->editingId)->update($data);
            $this->successMessage = 'تم تحديث الحساب البنكي بنجاح';
        } else {
            BankAccount::create($data);
            $this->successMessage = 'تمت إضافة الحساب البنكي بنجاح';
        }

        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        $acc = BankAccount::findOrFail($id);
        $acc->update(['is_active' => ! $acc->is_active]);
        $this->successMessage = $acc->is_active ? 'تم تفعيل الحساب' : 'تم تعطيل الحساب';
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function deleteAccount(): void
    {
        if ($this->confirmDeleteId) {
            BankAccount::findOrFail($this->confirmDeleteId)->delete();
            $this->successMessage = 'تم حذف الحساب البنكي';
            $this->confirmDeleteId = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.bank-accounts.bank-accounts-page', [
            'accounts' => BankAccount::latest('created_at')->get(),
        ]);
    }
}
