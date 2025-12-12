<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create washing machines
        Machine::firstOrCreate([
            'name' => 'Washer 1',
        ], [
            'type' => 'washer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Main washing machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Washer 2',
        ], [
            'type' => 'washer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Secondary washing machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Washer 3',
        ], [
            'type' => 'washer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Third washing machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Washer 4',
        ], [
            'type' => 'washer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Fourth washing machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Washer 5',
        ], [
            'type' => 'washer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Fifth washing machine',
        ]);

        // Create drying machines
        Machine::firstOrCreate([
            'name' => 'Dryer 1',
        ], [
            'type' => 'dryer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Main drying machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Dryer 2',
        ], [
            'type' => 'dryer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Secondary drying machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Dryer 3',
        ], [
            'type' => 'dryer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Large capacity drying machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Dryer 4',
        ], [
            'type' => 'dryer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Fourth drying machine',
        ]);

        Machine::firstOrCreate([
            'name' => 'Dryer 5',
        ], [
            'type' => 'dryer',
            'status' => 'idle',
            'capacity_kg' => 8,
            'notes' => 'Extra large capacity drying machine',
        ]);
    }
}
