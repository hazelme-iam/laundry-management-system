<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Machine;
use Livewire\Component;

class OrderShow extends Component
{
    public $order;
    public $orderStatus;
    public $availableWashers;
    public $availableDryers;
    public $washingTimeRemaining;
    public $dryingTimeRemaining;

    protected $listeners = ['orderUpdated' => '$refresh'];

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->loadOrderData();
    }

    public function loadOrderData()
    {
        $this->order->load(['customer', 'creator', 'updater', 'loads.washerMachine', 'loads.dryerMachine', 'assignedWasher', 'assignedDryer']);
        $this->orderStatus = $this->order->status;
        $this->availableWashers = Machine::washers()->idle()->get();
        $this->availableDryers = Machine::dryers()->idle()->get();
        
        // Calculate time remaining
        if ($this->order->status === 'washing' && $this->order->washing_end) {
            $this->washingTimeRemaining = $this->order->washing_end->diffInSeconds(now(), false);
        } else {
            $this->washingTimeRemaining = null;
        }
        
        if ($this->order->status === 'drying' && $this->order->drying_end) {
            $this->dryingTimeRemaining = $this->order->drying_end->diffInSeconds(now(), false);
        } else {
            $this->dryingTimeRemaining = null;
        }
    }

    public function render()
    {
        return view('livewire.order-show');
    }
}
