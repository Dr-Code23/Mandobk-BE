<?php

namespace App\Http\Resources\Api\Web\V1\Dashboard\HumanResource;

use App\Traits\translationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class humanResourceResource extends JsonResource
{
    use translationTrait;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = [
            'user_id' => $this->id,
            'full_name' => $this->full_name,
            'attendance' => $this->attendance,
            'departure' => $this->departure,
            'date' => $this->date,
        ];
        if ($this->role_name) {
            $response['role_name'] = $this->translateWord('Roles/rolesTranslationFile.'.$this->role_name);
        }

        return $response;
    }
}