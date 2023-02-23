<?php

namespace Database\Seeders\Api\V1;

use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\SubUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SubUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 14; $i < 24; ++$i) {
            User::create([
                'username' => 'sub_user_' . $i,
                'password' => Hash::make('sub_user_' . $i),
                'full_name' => 'sub_user_' . $i,
                'phone' => 1000 + $i,
                'role_id' => Role::where('name', 'pharmacy_sub_user')->value('id'),
            ]);

            SubUser::create([
                'parent_id' => 9,
                'sub_user_id' => $i,
            ]);
        }
    }
}
