<?php

namespace App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation;

use App\Traits\DateTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class MonitorAndEvaluationResrouce extends JsonResource
{
    use DateTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
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
