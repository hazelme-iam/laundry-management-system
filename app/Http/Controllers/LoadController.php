<?php

namespace App\Http\Controllers;

use App\Models\Load;
use App\Models\Order;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $loads = Load::with(['order.customer', 'washerMachine', 'dryerMachine', 'creator'])
            ->latest()
            ->paginate(20);

        $statistics = [
            'total' => Load::count(),
            'pending' => Load::where('status', 'pending')->count(),
            'washing' => Load::where('status', 'washing')->count(),
            'drying' => Load::where('status', 'drying')->count(),
            'completed' => Load::where('status', 'completed')->count(),
            'consolidated' => Load::where('is_consolidated', true)->count(),
        ];

        return view('admin.loads.index', compact('loads', 'statistics'));
    }

    public function create()
    {
        $orders = Order::where('status', 'approved')->get();
        $machines = Machine::available()->get();
        
        return view('admin.loads.create', compact('orders', 'machines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'washer_machine_id' => 'nullable|exists:machines,id',
            'dryer_machine_id' => 'nullable|exists:machines,id',
            'weight' => 'required|numeric|min:0.1',
            'status' => 'required|in:pending,washing,drying,folding,completed',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        // Calculate capacity utilization
        if (isset($data['washer_machine_id'])) {
            $machine = Machine::find($data['washer_machine_id']);
            $data['capacity_utilization'] = ($data['weight'] / $machine->capacity_kg) * 100;
        }

        Load::create($data);

        return redirect()->route('admin.loads.index')
            ->with('success', 'Load created successfully.');
    }

    public function show(Load $load)
    {
        $load->load(['order.customer', 'washerMachine', 'dryerMachine', 'creator', 'updater']);
        
        return view('admin.loads.show', compact('load'));
    }

    public function edit(Load $load)
    {
        $orders = Order::all();
        $machines = Machine::all();
        
        return view('admin.loads.edit', compact('load', 'orders', 'machines'));
    }

    public function update(Request $request, Load $load)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'washer_machine_id' => 'nullable|exists:machines,id',
            'dryer_machine_id' => 'nullable|exists:machines,id',
            'weight' => 'required|numeric|min:0.1',
            'status' => 'required|in:pending,washing,drying,folding,completed',
            'wash_start' => 'nullable|date',
            'wash_end' => 'nullable|date',
            'dry_start' => 'nullable|date',
            'dry_end' => 'nullable|date',
            'folding_start' => 'nullable|date',
            'folding_end' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['updated_by'] = Auth::id();

        // Update capacity utilization
        if (isset($data['washer_machine_id'])) {
            $machine = Machine::find($data['washer_machine_id']);
            $data['capacity_utilization'] = ($data['weight'] / $machine->capacity_kg) * 100;
        }

        $load->update($data);

        return redirect()->route('admin.loads.index')
            ->with('success', 'Load updated successfully.');
    }

    public function destroy(Load $load)
    {
        $load->delete();

        return redirect()->route('admin.loads.index')
            ->with('success', 'Load deleted successfully.');
    }

    public function startWashing(Load $load)
    {
        if ($load->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending loads can start washing.');
        }

        if (!$load->washer_machine_id) {
            return redirect()->back()
                ->with('error', 'Please assign a washer machine first.');
        }

        $load->update([
            'status' => 'washing',
            'wash_start' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Update machine status
        $load->washerMachine->update(['status' => 'in_use']);

        return redirect()->back()
            ->with('success', 'Load started washing.');
    }

    public function completeWashing(Load $load)
    {
        if ($load->status !== 'washing') {
            return redirect()->back()
                ->with('error', 'Only washing loads can be completed.');
        }

        $load->update([
            'wash_end' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Release washer machine
        if ($load->washerMachine) {
            $load->washerMachine->update(['status' => 'available']);
            // Notify admins that washing is finished and machine is now available
            NotificationService::washingCompleted($load->order);
            NotificationService::machineAvailable($load->washerMachine);
        }

        return redirect()->back()
            ->with('success', 'Washing completed.');
    }

    public function startDrying(Load $load)
    {
        if ($load->status !== 'washing' || !$load->wash_end) {
            return redirect()->back()
                ->with('error', 'Load must complete washing first.');
        }

        if (!$load->dryer_machine_id) {
            return redirect()->back()
                ->with('error', 'Please assign a dryer machine first.');
        }

        $load->update([
            'status' => 'drying',
            'dry_start' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Update dryer machine status
        $load->dryerMachine->update(['status' => 'in_use']);

        return redirect()->back()
            ->with('success', 'Load started drying.');
    }

    public function completeDrying(Load $load)
    {
        if ($load->status !== 'drying') {
            return redirect()->back()
                ->with('error', 'Only drying loads can be completed.');
        }

        $load->update([
            'dry_end' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Release dryer machine
        if ($load->dryerMachine) {
            $load->dryerMachine->update(['status' => 'available']);
            // Notify admins that drying is finished and machine is now available
            NotificationService::dryingCompleted($load->order);
            NotificationService::machineAvailable($load->dryerMachine);
        }

        return redirect()->back()
            ->with('success', 'Drying completed.');
    }

    public function consolidate()
    {
        // Find pending orders that can be consolidated
        $consolidatableOrders = Order::where('status', 'pending')
            ->where('weight', '<=', 4.0)
            ->with(['customer'])
            ->get();

        $availableMachines = Machine::available()->washers()->get();

        return view('admin.loads.consolidate', compact('consolidatableOrders', 'availableMachines'));
    }

    public function processConsolidation(Request $request)
    {
        $request->validate([
            'orders' => 'required|array|min:2',
            'orders.*' => 'exists:orders,id',
            'washer_machine_id' => 'required|exists:machines,id',
        ]);

        $machine = Machine::find($request->washer_machine_id);
        $orders = Order::whereIn('id', $request->orders)->get();
        
        $totalWeight = $orders->sum('weight');
        
        if ($totalWeight > $machine->capacity_kg) {
            return redirect()->back()
                ->with('error', 'Total weight exceeds machine capacity.');
        }

        // Create consolidated load
        $load = Load::create([
            'order_id' => $orders->first()->id, // Primary order
            'washer_machine_id' => $machine->id,
            'weight' => $totalWeight,
            'capacity_utilization' => ($totalWeight / $machine->capacity_kg) * 100,
            'is_consolidated' => true,
            'status' => 'pending',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'notes' => 'Consolidated load for orders: ' . $orders->pluck('id')->implode(', '),
        ]);

        // Update all orders to reference this load
        foreach ($orders as $order) {
            $order->update(['primary_washer_id' => $machine->id]);
        }

        return redirect()->route('admin.loads.show', $load)
            ->with('success', 'Load consolidated successfully!');
    }
}
