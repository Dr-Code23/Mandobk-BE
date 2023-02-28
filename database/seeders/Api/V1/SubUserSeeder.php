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
        SubUser::create([
            'parent_id' => 9,
            'sub_user_id' => 10,
        ]);
    }
}
