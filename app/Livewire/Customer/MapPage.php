<?php

namespace App\Livewire\Customer;

use App\Models\GasStation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.customer')]
#[Title('الخريطة - غازي')]
class MapPage extends Component
{
    public function render()
    {
        $stations = GasStation::where('is_active', true)
            ->get(['id', 'station_name', 'latitude', 'longitude', 'location', 'is_open', 'rating']);

        return view('livewire.customer.map-page', compact('stations'))
            ->layoutData(['showNav' => true, 'fullscreen' => true]);
    }
}
