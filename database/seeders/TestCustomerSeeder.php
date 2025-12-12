<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;

class TestCustomerSeeder extends Seeder
{
    public function run()
    {
        // Get all regular users (non-admin)
        $users = User::where('role', '!=', 'admin')->get();
        
        foreach ($users as $user) {
            // Check if customer already exists for this user
            $existingCustomer = Customer::where('user_id', $user->id)->first();
            
            if (!$existingCustomer) {
                Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => '123-456-7890',
                    'address' => '123 Test Street, Test City',
                    'customer_type' => 'regular',
                    'user_id' => $user->id,
                ]);
            }
        }
        
        // Also create some walk-in customers for testing
        Customer::firstOrCreate([
            'email' => 'walkin1@example.com',
        ], [
            'name' => 'John Doe',
            'phone' => '555-0101',
            'address' => '456 Oak Avenue, Test City',
            'customer_type' => 'walk-in',
            'user_id' => null,
        ]);
        
        Customer::firstOrCreate([
            'email' => 'walkin2@example.com',
        ], [
            'name' => 'Jane Smith',
            'phone' => '555-0102',
            'address' => '789 Pine Street, Test City',
            'customer_type' => 'walk-in',
            'user_id' => null,
        ]);
    }
}
