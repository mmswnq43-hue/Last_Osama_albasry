<?php

namespace App\Livewire\Owner;

use App\Models\Employee;
use App\Models\GasStation;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.owner')]
#[Title('الموظفين - مالك المحطة')]
class EmployeesPage extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;

    // Form fields
    public string $user_phone = '';
    public ?int $foundUserId = null;
    public string $foundUserName = '';
    public string $position = '';
    public ?float $salary = null;
    public string $hire_date = '';
    public ?int $station_id = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['user_phone', 'foundUserId', 'foundUserName', 'position', 'salary', 'hire_date', 'station_id']);
        $this->hire_date = now()->toDateString();
        $this->showModal = true;
    }

    public function searchUser(): void
    {
        $this->foundUserId = null;
        $this->foundUserName = '';

        if (empty($this->user_phone)) {
            $this->addError('user_phone', 'أدخل رقم الجوال.');
            return;
        }

        $user = User::where('phone', $this->user_phone)->first();
        if (!$user) {
            $this->addError('user_phone', 'لم يتم العثور على مستخدم بهذا الرقم.');
            return;
        }

        $this->foundUserId = $user->id;
        $this->foundUserName = $user->full_name;
    }

    public function save(): void
    {
        $this->validate([
            'foundUserId' => 'required|integer',
            'station_id'  => 'required|integer',
            'position'    => 'required|string|max:50',
            'salary'      => 'nullable|numeric',
            'hire_date'   => 'required|date',
        ]);

        // verify station belongs to owner
        GasStation::where('id', $this->station_id)->where('owner_id', auth()->id())->firstOrFail();

        $employee = Employee::create([
            'user_id'       => $this->foundUserId,
            'station_id'    => $this->station_id,
            'employee_code' => 'EMP-' . strtoupper(Str::random(8)),
            'position'      => $this->position,
            'salary'        => $this->salary,
            'hire_date'     => $this->hire_date,
            'is_active'     => true,
        ]);

        User::where('id', $this->foundUserId)->update(['user_role' => 'station_worker']);

        $this->showModal = false;
        session()->flash('success', 'تم إضافة الموظف بنجاح.');
    }

    public function toggleActive(int $id): void
    {
        $stationIds = GasStation::where('owner_id', auth()->id())->pluck('id');
        $employee = Employee::whereIn('station_id', $stationIds)->findOrFail($id);
        $employee->update(['is_active' => !$employee->is_active]);
        session()->flash('success', 'تم تحديث حالة الموظف.');
    }

    public function fire(int $id): void
    {
        $stationIds = GasStation::where('owner_id', auth()->id())->pluck('id');
        $employee = Employee::whereIn('station_id', $stationIds)->findOrFail($id);
        $employee->update(['is_active' => false]);
        session()->flash('success', 'تم إيقاف الموظف.');
    }

    public function render()
    {
        $stationIds = GasStation::where('owner_id', auth()->id())->pluck('id');

        $employees = Employee::with(['user:id,full_name,phone', 'station:id,station_name'])
            ->whereIn('station_id', $stationIds)
            ->when($this->search, fn($q) => $q->whereHas('user', fn($u) =>
                $u->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
            ))
            ->latest()
            ->paginate(10);

        $myStations = GasStation::where('owner_id', auth()->id())->get(['id', 'station_name']);

        return view('livewire.owner.employees-page', compact('employees', 'myStations'));
    }
}
