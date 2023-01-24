<?php

namespace Database\Seeders\Api\Web\V1;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::create([
            'username' => 'doctor',
            'password' => Hash::make('doctor'),
            'full_name' => 'doctor',
            'phone' => '123'
        ]);

        User::create([
            'username' => 'company',
            'password' => Hash::make('company'),
            'full_name' => 'company',
            'phone' => '123'
        ]);
        User::create([
            'username' => 'storehouse',
            'password' => Hash::make('storehouse'),
            'full_name' => 'storehouse',
            'phone' => '123'
        ]);
        User::create([
            'username' => 'pharmacy',
            'password' => Hash::make('pharmacy'),
            'full_name' => 'pharmacy',
            'phone' => '123'
        ]);
        User::create([
            'username' => 'super_pharmacy',
            'password' => Hash::make('super_pharmacy'),
            'full_name' => 'super_pharmacy',
            'phone' => '123'
        ]);

    }
}
