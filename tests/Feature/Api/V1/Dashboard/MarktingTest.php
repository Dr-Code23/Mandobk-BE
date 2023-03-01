<?php

namespace Tests\Feature\Api\V1\Dashboard;

use App\Models\V1\Marketing;
use App\Traits\FileOperationTrait;
use App\Traits\TestingTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarktingTest extends TestCase
{
    use TestingTrait;
    use FileOperationTrait;

    private string $path = 'Dashboard/Marketing/';
    public function testLogin(array $credentials = ['username' => 'markting', 'password' => 'markting'])
    {
        $response = $this->postJson(route('v1-login'), $credentials);
        $response->assertSuccessful();
        $this->setToken(json_decode($response->getContent())->data->token);
    }

    public function testGetAllAds()
    {

        Marketing::create([
            'medicine_name' => fake()->name(),
            'company_name' => fake()->name(),
            'discount' => rand(1, 100),
            'img' => 'Google',
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('markting-all'));
        $this->writeAFileForTesting($this->path, 'AllAds', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'company_name',
                    'medicine_name',
                    'discount',
                    'img',
                ]
            ]
        ]);
    }
    public function testGetOneAd()
    {

        $ad = Marketing::create([
            'medicine_name' => fake()->name(),
            'company_name' => fake()->name(),
            'discount' => rand(1, 100),
            'img' => 'Google',
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->getJson(route('markting-one', ['ad' => $ad->id]));
        $this->writeAFileForTesting($this->path, 'GetOneAd', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'company_name',
                'medicine_name',
                'discount',
                'img',
            ]
        ]);
    }
    public function testStoreAd()
    {
        Storage::fake('ads');
        $adImage = UploadedFile::fake()->image(fake()->name() . '.jpg');
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('markting_store'), [
                'company_name' => fake()->name(),
                'medicine_name' => fake()->name(),
                'discount' => rand(1, 100),
                'img' => $adImage
            ]);
        $this->writeAFileForTesting($this->path, 'StoreAd', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'company_name',
                'medicine_name',
                'discount',
                'img',
            ]
        ]);
    }
    public function testUpdateAd()
    {
        $ad = Marketing::create([
            'medicine_name' => fake()->name(),
            'company_name' => fake()->name(),
            'discount' => rand(1, 100),
            'img' => 'Google',
        ]);

        Storage::fake('ads');
        $adImage = UploadedFile::fake()->image(fake()->name() . '.jpg');
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->postJson(route('markting_update', ['ad' => $ad->id]), [
                'company_name' => fake()->name(),
                'medicine_name' => fake()->name(),
                'discount' => rand(1, 100),
                'img' => $adImage
            ]);
        $this->writeAFileForTesting($this->path, 'UpdateAd', $response->getContent());
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'company_name',
                'medicine_name',
                'discount',
                'img',
            ]
        ]);
    }
    public function testDeleteAd()
    {
        $ad = Marketing::create([
            'medicine_name' => fake()->name(),
            'company_name' => fake()->name(),
            'discount' => rand(1, 100),
            'img' => 'Google',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->deleteJson(route('markting_update', ['ad' => $ad->id]));
        $this->writeAFileForTesting($this->path, 'DeleteAd', $response->getContent());
        $response->assertSuccessful();
    }
}
