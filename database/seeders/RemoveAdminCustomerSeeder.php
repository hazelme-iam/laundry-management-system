<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class RemoveAdminCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find admin user
        $adminUser = User::where('role', 'admin')->first();
        
        if ($adminUser) {
            // Remove any customer record associated with admin
            $adminCustomer = Customer::where('user_id', $adminUser->id)->first();
            
            if ($adminCustomer) {
                $adminCustomer->delete();
                echo "Removed customer record for admin user: " . $adminUser->name . "\n";
            }
        }
    }
}
