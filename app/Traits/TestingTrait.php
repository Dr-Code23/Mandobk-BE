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

    /**
     * Set Token For Testing Phase.
     *
     * @return void
     */
    public function setToken(string $token)
    {
        if (config('test.store_response')) {
            if (!is_dir(__DIR__.'/../../tests/responsesExamples/Auth')) {
                mkdir(__DIR__.'/../../tests/responsesExamples/Auth', recursive: true);
            }
            $handle = fopen(__DIR__.'/../../tests/responsesExamples/Auth/token.txt', 'w');
            fwrite($handle, $token);
            fclose($handle);
        }
    }

    /**
     * Get Token For Testing.
     *
     * @return string
     */
    public function getToken()
    {
        return file_get_contents(__DIR__.'/../../tests/responsesExamples/Auth/token.txt');
    }
}
