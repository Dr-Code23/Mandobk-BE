<?php

namespace Database\Seeders\Api\V1;

use App\Models\User;
use App\Models\V1\Role;
use App\Traits\UserTrait;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use UserTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = config('roles.all_roles');
        for ($i = 1; $i <= count($roles); $i++) {
            User::create([
                'username' => $roles[$i - 1],
                'password' => $roles[$i - 1],
                'full_name' => $roles[$i - 1],
                'status' => $this->isActive(),
                'phone' => $i,
                'role_id' => $i,
            ]);
        }

//         for ($i = 1; $i <= 1000; ++$i) {
//             User::create([
//                 'username' => 'test' . $i,
//                 'password' => 'test',
//                 'full_name' => 'test',
//                 'status' => '1',
//                 'phone' => null,
//                 'role_id' => $i % 12 + ($i % 12 == 0 ? 1 : 0),
//             ]);
//         }
        // Add Customer User

        User::create([
            'username' => 'customer',
            'password' => Hash::make('customer'),
            'full_name' => 'Customer',
            'phone' => 10000,
            'role_id' => Role::where('name', 'customer')->value('id'),
        ]);
    }
}
