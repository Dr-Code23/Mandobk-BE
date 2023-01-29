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
        $roles = ['ceo','monitor_and_evaluation', 'human_resource', 'markting', 'order_management', 'data_entry', 'company', 'pharmacy', 'super_pharmacy', 'storehouse', 'doctor', 'visitor'];
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
