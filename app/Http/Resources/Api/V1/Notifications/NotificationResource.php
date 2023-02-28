<?php

namespace App\Http\Resources\Api\V1\Notifications;

use App\Traits\DateTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    use DateTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'data' => $this->data ?? [],
            'msg' => config('notifications.messages')[$this->type],
            'read' => $this->read_at != null,
            'type' => config('notifications.types')[$this->type],
            'since' => $this->messageSentFrom($this->created_at)
        ];
    }
}
