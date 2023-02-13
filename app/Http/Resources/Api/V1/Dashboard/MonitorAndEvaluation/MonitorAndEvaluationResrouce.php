<?php

namespace App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation;

use App\Traits\DateTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class monitorAndEvaluationResrouce extends JsonResource
{
    use DateTrait;

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
            'full_name' => $this->full_name,
            'username' => $this->username,
            'role_id' => $this->role_id,
            'role_name' => $this->role_name,
        ];
    }
}