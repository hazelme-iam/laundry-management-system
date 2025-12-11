<?php

namespace App\Livewire;

use App\Models\Machine;
use Livewire\Component;

class MachineDashboard extends Component
{
    public $washers;
    public $dryers;
    public $stats;

    protected $listeners = ['machineUpdated' => '$refresh'];

    public function mount()
    {
        $this->loadMachines();
    }

    public function loadMachines()
    {
        $this->washers = Machine::washers()->with('currentOrder.customer')->get();
        $this->dryers = Machine::dryers()->with('currentOrder.customer')->get();
        
        $this->stats = [
            'washers_in_use' => $this->washers->where('status', 'in_use')->count(),
            'washers_available' => $this->washers->where('status', 'idle')->count(),
            'dryers_in_use' => $this->dryers->where('status', 'in_use')->count(),
            'dryers_available' => $this->dryers->where('status', 'idle')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.machine-dashboard');
    }
}
