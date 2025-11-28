<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;

class TestCustomerSeeder extends Seeder
{
    public function run()
    {
        // Get the first user (or create one if none exists)
        $user = User::first();
        
        if ($user) {
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
}
