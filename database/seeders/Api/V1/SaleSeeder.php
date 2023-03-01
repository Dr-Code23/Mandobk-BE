<?php

namespace Database\Seeders\Api\V1;

use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\Sale;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Company To Storehouse
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'company')->value('id'))->value('id'),
            'to_id' => User::where('role_id', Role::where('name', 'storehouse')->value('id'))->value('id'),
            'details' => json_encode([]),
            'type' => '1',
        ]);

        // Storehouse To Pharmacy (Admin)
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'storehouse')->value('id'))->value('id'),
            'to_id' => User::where('role_id', Role::where('name', 'pharmacy')->value('id'))->value('id'),
            'details' => json_encode([]),
            'type' => '2',
        ]);

        // Storehouse To Pharmacy (Employee)
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'storehouse')->value('id'))->value('id'),
            'to_id' => User::where('role_id', Role::where('name', 'pharmacy_sub_user')->value('id'))->value('id'),
            'details' => json_encode([]),
            'type' => '2',
        ]);

        // Pharmacy(Admin) To Normal Customer
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'pharmacy')->value('id'))->value('id'),
            'to_id' => User::where('username', 'customer')->value('id'),
            'details' => json_encode([]),
            'type' => '3',
        ]);

        // Pharmacy(Sub User) To Normal Customer
        Sale::create([
            'from_id' => User::where('role_id', Role::where('name', 'pharmacy_sub_user')->value('id'))->value('id'),
            'to_id' => User::where('username', 'customer')->value('id'),
            'details' => json_encode([]),
            'type' => '3',
        ]);
    }
}
