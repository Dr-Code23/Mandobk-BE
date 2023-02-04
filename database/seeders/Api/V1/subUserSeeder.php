<?php

namespace Database\Seeders\Api\V1;

use App\Models\Api\V1\Role;
use App\Models\Api\V1\SubUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class subUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i < 10; ++$i) {
            User::create([
                'username' => 'sub_user_'.$i,
                'password' => Hash::make('sub_user'.$i),
                'full_name' => 'sub_user_'.$i,
                'role_id' => Role::where('name', 'pharmacy_sub_user')->first(['id'])->id,
            ]);

            SubUser::create([
                'parent_id' => 9,
                'sub_user_id' => $i,
            ]);
        }
    }
}
