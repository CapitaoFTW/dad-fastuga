<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
        switch (OrderResource::$format) {
            case 'withOrderItems':
                return [
                    "id" => $this->id,
                    "ticket_number" => $this->ticket_number,
                    "status" => $this->status,
                    "status_name" => $this->statusName,
                    "customer_id" => $this->customer_id,
                    "customer_name" => $this->customer->user->name ?? null,
                    "total_price" => $this->total_price,
                    "total_paid" => $this->total_paid,
                    "total_paid_with_points" => $this->total_paid_with_points,
                    "points_gained" => $this->points_gained,
                    "points_used_to_pay" => $this->points_used_to_pay,
                    "payment_type" => $this->payment_type,
                    "payment_reference" => $this->payment_reference,
                    "date" => $this->date,
                    "delivered_by" => $this->delivered_by,
                    "deliverer" => $this->deliverer->name ?? null,
                    "total_products" => $this->totalProducts,
                    "order_items" => OrderItemResource::collection($this->orderItems),
                ];
            default:
                return [
                    "id" => $this->id,
                    "ticket_number" => $this->ticket_number,
                    "status" => $this->status,
                    "status_name" => $this->statusName,
                    "customer_id" => $this->customer_id,
                    "customer_userId" => $this->customer->user_id ?? null,
                    "customer_name" => $this->customer->user->name ?? null,
                    "total_price" => $this->total_price,
                    "total_paid" => $this->total_paid,
                    "total_paid_with_points" => $this->total_paid_with_points,
                    "points_gained" => $this->points_gained,
                    "points_used_to_pay" => $this->points_used_to_pay,
                    "payment_type" => $this->payment_type,
                    "payment_reference" => $this->payment_reference,
                    "date" => $this->date,
                    "delivered_by" => $this->delivered_by,
                    "deliverer" => $this->deliverer->name ?? null,
                    "total_products" => $this->totalProducts,
                ];
        }
    }
}
