<?php

namespace Database\Seeders\Api\V1;

use App\Models\User;
use App\Models\V1\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
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
            User::create([
                'username' => $roles[$i - 1],
                'password' => $roles[$i - 1],
                'full_name' => $roles[$i - 1],
                'role_id' => $i,
            ]);
        }

        // Add Customer User

        User::create([
            'username' => 'customer',
            'password' => Hash::make(Hash::make('customer')),
            'full_name' => 'Unknow Customer',
            'role_id' => Role::where('name', 'customer')->value('id'),
        ]);
    }
}
