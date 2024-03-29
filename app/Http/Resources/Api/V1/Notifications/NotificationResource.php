<?php

namespace App\Http\Resources\Api\V1\Notifications;

use App\Traits\DateTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    use DateTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'data' => $this->data ?? [],
            'msg' => config('notifications.messages')[$this->type] ?? null,
            'read' => $this->read_at != null,
            'type' => config('notifications.types')[$this->type] ?? null,
            'since' => $this->messageSentFrom($this->created_at),
        ];
    }
}
