<?php

namespace Database\Seeders\Api\V1;

use App\Models\V1\PayMethod;
use Illuminate\Database\Seeder;

class PayMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        PayMethod::create([
            'name' => 'cash',
        ]);
    }
}
