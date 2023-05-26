<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('roles.all_roles');
        for ($i = 1; $i <= count($roles); $i++) {
            Role::create([
                'name' => $roles[$i - 1],
            ]);
        }

        // Add Customer Role

        Role::create([
            'name' => 'customer',
        ]);
    }
}
