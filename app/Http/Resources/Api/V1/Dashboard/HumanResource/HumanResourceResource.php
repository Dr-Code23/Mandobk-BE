<?php

namespace App\Http\Resources\Api\V1\Dashboard\HumanResource;

use App\Traits\DateTrait;
use App\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * @property mixed $id
 * @property mixed $user_id
 * @property mixed $full_name
 */
class HumanResourceResource extends JsonResource
{
    use Translatable;
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
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'attendance' => $this->changeDateFormat($this->attendance, 'H:i'),
            'departure' => $this->changeDateFormat($this->departure, 'H:i'),
            'role_id' => $this->role_id,
            'role_name' => $this->translateWord($this->role_name),
            'status' => $this->status == '0' ? $this->translateWord('attended')
                : ($this->status == '1' ? $this->translateWord('absense') : $this->translateWord('holiday')),
            'status_code' => $this->status,
            'date' => $this->date,
        ];
    }
}
