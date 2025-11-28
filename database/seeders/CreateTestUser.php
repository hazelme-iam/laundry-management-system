<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Seeder
{
    public function run()
    {
        // Create a regular user (not admin)
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user', // This is important - not 'admin'
        ]);

        // Create a customer record for this user
        Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '987-654-3210',
            'address' => '456 User Street, Test City',
            'customer_type' => 'regular',
            'user_id' => $user->id,
        ]);

        echo "Created regular user:\n";
        echo "Email: user@example.com\n";
        echo "Password: password123\n";
        echo "Role: user\n";
        echo "This user should see 'My Orders' in navigation\n";
    }
}
