<?php

namespace Database\Seeders\Api\Web\V1;

use App\Models\Api\Web\V1\Role;
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
        $roles = ['ceo','monitor_and_evaluation', 'human_resource', 'markting', 'order_management', 'data_entry', 'company', 'pharmacy', 'super_pharmacy', 'storehouse', 'doctor', 'visitor'];
        for ($i = 1; $i <= count($roles); ++$i) {
            Role::create([
                'name' => $roles[$i - 1],
            ]);
        }
    }
}
