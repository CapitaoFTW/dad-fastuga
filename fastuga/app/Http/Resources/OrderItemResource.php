<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    //public static $format = 'default';
    public function toArray($request)
    {
        /*switch (OrderItemResource::$format) {
            case 'withProducts':
                return [
                    "id" => $this->id,
                    "ticket_number" => $this->ticket_number,
                    "status" => $this->status,
                    "status_name" => $this->statusName,
                    "customer_id" => $this->customer_id,
                    "customer_name" => $this->customer->name,
                    "total_price" => $this->total_price,
                    "total_paid" => $this->total_paid,
                    "total_paid_with_points" => $this->total_paid_with_points,
                    "points_gained" => $this->points_gained,
                    "points_used_to_pay" => $this->points_used_to_pay,
                    "payment_type" => $this->payment_type,
                    "payment_reference" => $this->payment_reference,
                    "date" => $this->date,
                    "delivered_by" => $this->delivered_by,
                    "deliverer" => $this->deliverer->name,
                    "products" => ProductResource::collection($this->order_items->product->sortByDesc('id'))
                ];
            default:*/
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
        //}
    }
}
