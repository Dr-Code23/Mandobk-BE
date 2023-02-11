<?php

namespace App\Traits;

trait TestingTrait
{
    public function getSignUpData(string $Test = null, string $value = ''): array
    {
        $data = [
            'username' => 'Aa2302',
            'full_name' => 'TestName',
            'password' => 'Aa234!#!1',
            'phone' => '123123',
            'role' => '8',
        ];
        if ($Test) {
            $data[$Test] = $value;
        }

        return $data;
    }
}
