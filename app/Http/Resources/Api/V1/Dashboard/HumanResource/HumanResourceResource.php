<?php

namespace App\Http\Resources\Api\V1\Dashboard\HumanResource;

use App\Traits\Translatable;
use Illuminate\Http\Resources\Json\JsonResource;

class HumanResourceResource extends JsonResource
{
    use Translatable;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'attendance' => $this->attendance,
            'departure' => $this->departure,
            'role_id' => $this->role_id,
            'role_name' => $this->translateWord($this->role_name),
            'status' => $this->status == '0' ? $this->translateWord('attended')
                : ($this->status == '1' ? $this->translateWord('absense') : $this->translateWord('holiday')),
            'status_code' => $this->status,
            'date' => $this->date,
        ];
    }
}
