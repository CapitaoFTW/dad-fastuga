<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\OrderItemResource;
use App\Models\OrderItem;

class OrderItemController extends Controller
{
    public function update_preparing(Request $request, OrderItem $orderItem)
    {
        $orderItem->preparation_by = $request->user()->id;
        $orderItem->status = 'P';
        $orderItem->save();

        return new OrderItemResource($orderItem);
    }

    public function update_ready(OrderItem $orderItem)
    {
        $orderItem->status = 'R';
        $orderItem->save();

        return new OrderItemResource($orderItem);
    }
}
