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

        // Create loads for the order based on 8kg capacity
        $loads = $order->createOptimizedLoads();
        
        // Assign washer to first pending load
        $pendingLoad = $loads->where('status', 'pending')->first();
        if (!$pendingLoad) {
            return back()->with('error', 'No pending loads available for assignment.');
        }

        // Assign washer to load
        $pendingLoad->update([
            'washer_machine_id' => $washer->id,
            'washing_start' => now(),
            'washing_end' => now()->addMinutes(38),
            'status' => 'washing'
        ]);

        // Update machine status
        $washer->update([
            'status' => 'in_use',
            'current_order_id' => $order->id,
            'washing_start' => now(),
            'washing_end' => now()->addMinutes(38)
        ]);

        // Update order status if this is the first load
        if ($order->status === 'picked_up') {
            $order->update(['status' => 'washing']);
        }

        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'duration' => 38, // 38 minutes washing time
                'message' => "Washer assigned to load ({$pendingLoad->weight}kg). Washing will complete at " . now()->addMinutes(38)->format('H:i')
            ]);
        }

        return back()->with('success', "Washer assigned to load ({$pendingLoad->weight}kg). Washing will complete at " . now()->addMinutes(38)->format('H:i'));
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

        // Find completed washing loads for this order
        $completedWashingLoads = $order->loads()
            ->where('status', 'washing')
            ->where('washing_end', '<=', now())
            ->get();

        if ($completedWashingLoads->isEmpty()) {
            return back()->with('error', 'No completed washing loads available for drying.');
        }

        // Assign dryer to first completed washing load
        $load = $completedWashingLoads->first();
        
        // Free up washer first
        if ($load->washerMachine) {
            $washer = $load->washerMachine;
            $washer->update([
                'status' => 'idle',
                'current_order_id' => null,
                'washing_start' => null,
                'washing_end' => null
            ]);
        }

        // Assign dryer to load
        $load->update([
            'dryer_machine_id' => $dryer->id,
            'drying_start' => now(),
            'drying_end' => now()->addMinutes(30),
            'status' => 'drying'
        ]);

        // Update machine status
        $dryer->update([
            'status' => 'in_use',
            'current_order_id' => $order->id,
            'drying_start' => now(),
            'drying_end' => now()->addMinutes(30)
        ]);

        // Update order status
        $order->update(['status' => 'drying']);

        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'duration' => 30, // 30 minutes drying time
                'message' => "Dryer assigned to load ({$load->weight}kg). Drying will complete at " . now()->addMinutes(30)->format('H:i')
            ]);
        }

        return back()->with('success', "Dryer assigned to load ({$load->weight}kg). Drying will complete at " . now()->addMinutes(30)->format('H:i'));
    }

    public function checkCompletedMachines()
    {
        // Check completed washing loads
        $completedWashingLoads = \App\Models\Load::where('status', 'washing')
            ->where('washing_end', '<=', now())
            ->with('order', 'washerMachine')
            ->get();

        foreach ($completedWashingLoads as $load) {
            // Update load status
            $load->update(['status' => 'drying']);
            
            // Free up washer
            if ($load->washerMachine) {
                $load->washerMachine->update([
                    'status' => 'idle',
                    'current_order_id' => null,
                    'washing_start' => null,
                    'washing_end' => null
                ]);
            }
        }

        // Check completed drying loads
        $completedDryingLoads = \App\Models\Load::where('status', 'drying')
            ->where('drying_end', '<=', now())
            ->with('order', 'dryerMachine')
            ->get();

        foreach ($completedDryingLoads as $load) {
            // Update load status
            $load->update(['status' => 'completed']);
            
            // Free up dryer
            if ($load->dryerMachine) {
                $load->dryerMachine->update([
                    'status' => 'idle',
                    'current_order_id' => null,
                    'drying_start' => null,
                    'drying_end' => null
                ]);
            }
            
            // Update order status if all loads are completed
            $order = $load->order;
            if ($order && $order->loads()->where('status', '!=', 'completed')->count() === 0) {
                $order->update(['status' => 'folding']);
            }
        }

        return response()->json([
            'completed_washing_loads' => $completedWashingLoads->count(),
            'completed_drying_loads' => $completedDryingLoads->count()
        ]);
    }
}
