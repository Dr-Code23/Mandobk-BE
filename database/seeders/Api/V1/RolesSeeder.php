<?php

namespace Database\Seeders\Api\V1;

use App\Models\Api\V1\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('roles.all_roles');
        for ($i = 1; $i <= count($roles); ++$i) {
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
