<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Machine;
use App\Models\Order;

echo "=== MACHINE STATUS ===\n";
$machine = Machine::find(2);
echo "Machine 2 Status: " . $machine->status . "\n";
echo "Machine 2 Washing End: " . ($machine->washing_end ? $machine->washing_end->format('Y-m-d H:i:s') : 'null') . "\n";
echo "Machine 2 Current Order: " . ($machine->current_order_id ?? 'null') . "\n";

echo "\n=== ORDER STATUS ===\n";
$order = Order::find(1);
echo "Order 1 Status: " . $order->status . "\n";
echo "Order 1 Assigned Washer: " . ($order->assigned_washer_id ?? 'null') . "\n";

echo "\n=== COMPLETION CHECK ===\n";
$now = now();
echo "Current Time: " . $now->format('Y-m-d H:i:s') . "\n";
echo "Is washing end in past: " . ($machine->washing_end && $machine->washing_end->isPast() ? 'YES' : 'NO') . "\n";

// Check if machine should be detected as completed
$completedWashers = Machine::washers()
    ->where('status', 'in_use')
    ->where('washing_end', '<=', now())
    ->with('currentOrder')
    ->get();

echo "Completed washers found: " . $completedWashers->count() . "\n";
