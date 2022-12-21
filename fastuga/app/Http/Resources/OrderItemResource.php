<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "order_id" => $this->order_id,
            "order_local_number" => $this->order_local_number,
            "product_id" => $this->product_id,
            "product_name" => $this->product->name ?? null,
            "status" => $this->status,
            "status_name" => $this->statusName,
            "price" => $this->price,
            "preparation_by" => $this->preparation_by,
            "chef" => $this->chef->name ?? null,
            "notes" => $this->notes,
        ];
    }
}
