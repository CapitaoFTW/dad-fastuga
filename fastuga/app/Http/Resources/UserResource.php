<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $format = 'default';
    public function toArray($request)
    {
        switch (UserResource::$format) {
            case 'withCustomer':
                return [
                    'id' => $this->id,
                    'type' => $this->type,
                    'name' => $this->name,
                    'email' => $this->email,
                    'photo_url' => $this->photo_url,
                    'blocked' => $this->blocked,
                    'customer' => new CustomerResource($this->customer),
                ];
            default:
                return [
                    'id' => $this->id,
                    'type' => $this->type,
                    'name' => $this->name,
                    'email' => $this->email,
                    'photo_url' => $this->photo_url,
                    'blocked' => $this->blocked,
                ];
        }
    }
}
