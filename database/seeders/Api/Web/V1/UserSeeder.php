<?php

namespace Database\Seeders\Api\Web\V1;

use App\Models\User;
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
                'password' => Hash::make($roles[$i - 1]),
                'full_name' => $roles[$i - 1],
                'phone' => '123',
                'role_id' => $i,
            ]);
        }
    }
}
