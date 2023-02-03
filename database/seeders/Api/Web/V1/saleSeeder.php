<?php

namespace Database\Seeders\Api\Web\V1;

use App\Models\Api\Web\V1\Role;
use App\Models\Api\Web\V1\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class saleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Company To Storehouse
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'company')->first(['id'])->id)->first(['id'])->id,
            'to_id' => User::where('role_id', Role::where('name', 'storehouse')->first(['id'])->id)->first(['id'])->id,
            'details' => json_encode([]),
            'type' => '1',
        ]);

        // Storehouse To Pharmacy (Admin)
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'storehouse')->first(['id'])->id)->first(['id'])->id,
            'to_id' => User::where('role_id', Role::where('name', 'pharmacy')->first(['id'])->id)->first(['id'])->id,
            'details' => json_encode([]),
            'type' => '2',
        ]);

        // Storehouse To Pharmacy (Employee)
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'storehouse')->first(['id'])->id)->first(['id'])->id,
            'to_id' => User::where('role_id', Role::where('name', 'pharmacy_sub_user')->first(['id'])->id)->first(['id'])->id,
            'details' => json_encode([]),
            'type' => '2',
        ]);

        // Pharmacy(Admin) To Normal Customer
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'pharmacy')->first(['id'])->id)->first(['id'])->id,
            'to_id' => User::where('username', 'customer')->first(['id'])->id,
            'details' => json_encode([]),
            'type' => '3',
        ]);

        // Pharmacy(Sub User) To Normal Customer
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'pharmacy_sub_user')->first(['id'])->id)->first(['id'])->id,
            'to_id' => User::where('username', 'customer')->first(['id'])->id,
            'details' => json_encode([]),
            'type' => '3',
        ]);
    }
}
