<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;

class CustomerForExistingUserSeeder extends Seeder
{
    public function run()
    {
        // Get the existing user (likely user ID 8 based on the error logs)
        $user = User::find(8);
        
        if ($user) {
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
                
                echo "Created customer for user: {$user->name} ({$user->email})\n";
                echo "Login credentials:\n";
                echo "Email: {$user->email}\n";
                echo "Password: (your existing password)\n";
            } else {
                echo "Customer already exists for user: {$user->name}\n";
            }
        } else {
            echo "User with ID 8 not found. Creating new test user...\n";
            
            // Create new test user if user 8 doesn't exist
            $newUser = User::create([
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]);
            
            Customer::create([
                'name' => $newUser->name,
                'email' => $newUser->email,
                'phone' => '123-456-7890',
                'address' => '123 Test Street, Test City',
                'customer_type' => 'regular',
                'user_id' => $newUser->id,
            ]);
            
            echo "Created new test user:\n";
            echo "Email: testuser@example.com\n";
            echo "Password: password123\n";
        }
    }
}
