<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 washing machines
        for ($i = 1; $i <= 5; $i++) {
            Machine::create([
                'name' => "Washer {$i}",
                'type' => 'washer',
                'capacity_kg' => 8.00,
                'status' => 'available',
                'notes' => "8kg capacity washing machine #{$i}",
            ]);
        }

        // Create 5 drying machines  
        for ($i = 1; $i <= 5; $i++) {
            Machine::create([
                'name' => "Dryer {$i}",
                'type' => 'dryer',
                'capacity_kg' => 8.00,
                'status' => 'available',
                'notes' => "8kg capacity drying machine #{$i}",
            ]);
        }
    }
}
