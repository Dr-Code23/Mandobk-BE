<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\SubUser;
use Illuminate\Database\Seeder;

class SubUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubUser::create([
            'parent_id' => 9,
            'sub_user_id' => 10,
        ]);
    }
}
