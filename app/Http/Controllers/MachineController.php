<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MachineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $washers = Machine::washers()->with('currentOrder')->get();
        $dryers = Machine::dryers()->with('currentOrder')->get();
        
        return view('admin.machines.dashboard', compact('washers', 'dryers'));
    }

    public function assignWasher(Request $request, Order $order)
    {
        $request->validate([
            'washer_id' => 'required|exists:machines,id'
        ]);

        $washer = Machine::findOrFail($request->washer_id);
        
        if (!$washer->isAvailable()) {
            return back()->with('error', 'Selected washer is not available.');
        }

        // Assign washer to order
        $order->update([
            'assigned_washer_id' => $washer->id,
            'washing_start' => now(),
            'washing_end' => now()->addMinutes(38), // 38 minutes washing time
            'status' => 'washing'
        ]);

        // Update machine status
        $washer->update([
            'status' => 'in_use',
            'current_order_id' => $order->id,
            'washing_start' => now(),
            'washing_end' => now()->addMinutes(38)
        ]);

        return back()->with('success', "Washer assigned successfully. Washing will complete at {$order->washing_end->format('H:i')}");
    }

    public function assignDryer(Request $request, Order $order)
    {
        $request->validate([
            'dryer_id' => 'required|exists:machines,id'
        ]);

        $dryer = Machine::findOrFail($request->dryer_id);
        
        if (!$dryer->isAvailable()) {
            return back()->with('error', 'Selected dryer is not available.');
        }

        // Free up washer first
        if ($order->assigned_washer_id) {
            $washer = Machine::find($order->assigned_washer_id);
            if ($washer) {
                $washer->update([
                    'status' => 'idle',
                    'current_order_id' => null,
                    'washing_start' => null,
                    'washing_end' => null
                ]);
            }
        }

        // Assign dryer to order
        $order->update([
            'assigned_dryer_id' => $dryer->id,
            'drying_start' => now(),
            'drying_end' => now()->addMinutes(30), // 30 minutes drying time
            'status' => 'drying'
        ]);

        // Update machine status
        $dryer->update([
            'status' => 'in_use',
            'current_order_id' => $order->id,
            'drying_start' => now(),
            'drying_end' => now()->addMinutes(30)
        ]);

        return back()->with('success', "Dryer assigned successfully. Drying will complete at {$order->drying_end->format('H:i')}");
    }

    public function checkCompletedMachines()
    {
        $completedWashers = Machine::washers()
            ->where('status', 'in_use')
            ->where('washing_end', '<=', now())
            ->with('currentOrder')
            ->get();

        $completedDryers = Machine::dryers()
            ->where('status', 'in_use')
            ->where('drying_end', '<=', now())
            ->with('currentOrder')
            ->get();

        foreach ($completedWashers as $washer) {
            $order = $washer->currentOrder;
            if ($order && $order->status === 'washing') {
                $order->update(['status' => 'folding']);
            }
            
            $washer->update([
                'status' => 'idle',
                'current_order_id' => null,
                'washing_start' => null,
                'washing_end' => null
            ]);
        }

        foreach ($completedDryers as $dryer) {
            $order = $dryer->currentOrder;
            if ($order && $order->status === 'drying') {
                // Auto-advance through remaining stages
                $order->update(['status' => 'folding']);
            }
            
            $dryer->update([
                'status' => 'idle',
                'current_order_id' => null,
                'drying_start' => null,
                'drying_end' => null
            ]);
        }

        return response()->json([
            'completed_washers' => $completedWashers->count(),
            'completed_dryers' => $completedDryers->count()
        ]);
    }
}
