<?php

namespace Database\Seeders\Api\Web\V1;

use App\Models\Api\Web\V1\PayMethod;
use Illuminate\Database\Seeder;

class payMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PayMethod::create([
            'name' => 'cash',
        ]);
    }
}
