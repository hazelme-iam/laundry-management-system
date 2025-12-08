<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Load;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $machines = Machine::with(['washerLoads', 'dryerLoads'])
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(15);

        $statistics = [
            'total' => Machine::count(),
            'available' => Machine::where('status', 'available')->count(),
            'in_use' => Machine::where('status', 'in_use')->count(),
            'maintenance' => Machine::where('status', 'maintenance')->count(),
            'washers' => Machine::where('type', 'washer')->count(),
            'dryers' => Machine::where('type', 'dryer')->count(),
        ];

        return view('admin.machines.index', compact('machines', 'statistics'));
    }

    public function create()
    {
        return view('admin.machines.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:washer,dryer',
            'capacity_kg' => 'required|numeric|min:1|max:50',
            'status' => 'required|in:available,in_use,maintenance,out_of_order',
            'notes' => 'nullable|string|max:500',
        ]);

        Machine::create($data);

        return redirect()->route('admin.machines.index')
            ->with('success', 'Machine created successfully.');
    }

    public function show(Machine $machine)
    {
        $machine->load(['washerLoads.order.customer', 'dryerLoads.order.customer']);
        
        $utilization = $this->calculateUtilization($machine);
        
        return view('admin.machines.show', compact('machine', 'utilization'));
    }

    public function edit(Machine $machine)
    {
        return view('admin.machines.edit', compact('machine'));
    }

    public function update(Request $request, Machine $machine)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:washer,dryer',
            'capacity_kg' => 'required|numeric|min:1|max:50',
            'status' => 'required|in:available,in_use,maintenance,out_of_order',
            'notes' => 'nullable|string|max:500',
        ]);

        $machine->update($data);

        return redirect()->route('admin.machines.index')
            ->with('success', 'Machine updated successfully.');
    }

    public function destroy(Machine $machine)
    {
        // Check if machine has active loads
        $hasActiveLoads = $machine->washerLoads()->whereIn('status', ['pending', 'washing', 'drying'])->exists() ||
                         $machine->dryerLoads()->whereIn('status', ['pending', 'washing', 'drying'])->exists();

        if ($hasActiveLoads) {
            return redirect()->route('admin.machines.index')
                ->with('error', 'Cannot delete machine with active loads.');
        }

        $machine->delete();

        return redirect()->route('admin.machines.index')
            ->with('success', 'Machine deleted successfully.');
    }

    public function toggleStatus(Machine $machine)
    {
        $newStatus = $machine->status === 'available' ? 'maintenance' : 'available';
        
        $machine->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', "Machine status changed to {$newStatus}.");
    }

    private function calculateUtilization(Machine $machine)
    {
        $loads = $machine->type === 'washer' ? $machine->washerLoads : $machine->dryerLoads;
        
        $totalLoads = $loads->count();
        $activeLoads = $loads->whereIn('status', ['washing', 'drying'])->count();
        
        $totalCapacity = $machine->capacity_kg;
        $usedCapacity = $loads->sum('weight');
        
        return [
            'total_loads' => $totalLoads,
            'active_loads' => $activeLoads,
            'capacity_utilization' => $totalCapacity > 0 ? ($usedCapacity / $totalCapacity) * 100 : 0,
            'average_load_weight' => $totalLoads > 0 ? $usedCapacity / $totalLoads : 0,
        ];
    }
}
