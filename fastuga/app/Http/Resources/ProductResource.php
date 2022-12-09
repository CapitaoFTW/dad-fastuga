<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\OrderResource;

class ProductResource extends JsonResource
{
    //public static $format = "default";
    public function toArray($request)
    {
        /*switch (ProductResource::$format) {
            case 'detailed':
                return [
                    'id' => $this->id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'photo_url' => $this->photo_url,
                    'price' => $this->price,
                ];
            case 'withOrders':
                return [
                    'id' => $this->id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'photo_url' => $this->photo_url,
                    'price' => $this->price,
                    'order' => new OrderResource($this->order_items->order),
                    'order' => new ProductResource::collection($this->order_items->product->sortByDesc('id'))
                    'assigned_users' => new UserResource($this->assignedUsers),
                    'notes' => $this->notes
                ];
            default:*/
                return [
                    'id' => $this->id,
                    'name' => $this->name,
                    'type' => $this->type,
                    'description' => $this->description,
                    'photo_url' => $this->photo_url,
                    'price' => $this->price,
                ];
        //}
    }
}
