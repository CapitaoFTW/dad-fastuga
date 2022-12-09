<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderItemResource;
use App\Http\Requests\StoreUpdateOrderItemRequest;
use App\Models\OrderItem;
use App\Models\User;

class OrderItemController extends Controller
{
    public function store(StoreUpdateOrderItemRequest $request)
    {
        $newOrder = OrderItem::create($request->validated());
        return new OrderItemResource($newOrder);
    }
}
