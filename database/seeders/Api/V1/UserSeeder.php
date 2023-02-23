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
                'status' => $this->isActive(),
                'phone' => $i,
                'role_id' => $i,
            ]);
        }

        for ($i = 0; $i < 200; $i++) {
            User::create([
                'username' => "user$i",
                'password' => "user$i",
                'full_name' => "user $i",
                'role_id' => fake()->numberBetween(7, 12),
                'phone' => 300 + $i,
                'status' => fake()->numberBetween(1, 2) . ''
            ]);
        }
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
