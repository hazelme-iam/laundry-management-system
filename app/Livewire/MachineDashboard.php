<?php

namespace App\Livewire;

use App\Models\Machine;
use App\Models\Load;
use App\Services\NotificationService;
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
        $this->checkForCompletedCycles();
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
        
        $this->checkForCompletedCycles();
    }

    /**
     * Check if any washing or drying cycles have completed and trigger notifications
     */
    public function checkForCompletedCycles()
    {
        // Check washers
        foreach ($this->washers as $washer) {
            if ($washer->status === 'in_use' && $washer->washing_end && now()->greaterThanOrEqualTo($washer->washing_end)) {
                // Find the load associated with this washer
                $load = Load::where('washer_machine_id', $washer->id)
                    ->where('status', 'washing')
                    ->first();
                
                if ($load) {
                    // Update load status
                    $load->update([
                        'wash_end' => now(),
                        'updated_by' => auth()->id(),
                    ]);
                    
                    // Send notifications
                    NotificationService::washingCompleted($load->order);
                    NotificationService::machineAvailable($washer, $load->order);
                    
                    // Update machine status
                    $washer->update(['status' => 'idle']);
                }
            }
        }
        
        // Check dryers
        foreach ($this->dryers as $dryer) {
            if ($dryer->status === 'in_use' && $dryer->drying_end && now()->greaterThanOrEqualTo($dryer->drying_end)) {
                // Find the load associated with this dryer
                $load = Load::where('dryer_machine_id', $dryer->id)
                    ->where('status', 'drying')
                    ->first();
                
                if ($load) {
                    // Update load status
                    $load->update([
                        'dry_end' => now(),
                        'updated_by' => auth()->id(),
                    ]);
                    
                    // Send notifications
                    NotificationService::dryingCompleted($load->order);
                    NotificationService::machineAvailable($dryer, $load->order);
                    
                    // Update machine status
                    $dryer->update(['status' => 'idle']);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.machine-dashboard');
    }
}
