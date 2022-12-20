<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\ProductResource;
use App\Models\OrderItem;
use App\Models\User;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function show(Order $order)
    {
        OrderResource::$format = 'withOrderItems';
        return new OrderResource($order);
    }

    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        $lastOrder = Order::latest('id')->first();

        $newOrder = Order::create([
            'ticket_number' => $lastOrder->ticket_number == 99 ? 1 : $lastOrder->ticket_number + 1,
            'status' => 'P',
            'customer_id' => $validated['customer_id'],
            'total_price' => $validated['total_price'],
            'total_paid' => $validated['total_paid'],
            'total_paid_with_points' => $validated['total_paid_with_points'],
            'points_gained' => $validated['points_gained'],
            'points_used_to_pay' => $validated['points_used_to_pay'],
            'payment_type' => $validated['payment_type'],
            'payment_reference' => $validated['payment_reference'],
            'date' => date("Y-m-d"),
            'delivered_by' => null,
        ]);

        if ($newOrder->customer) {
            if ($validated['points_used_to_pay'] != 0) {
                $newOrder->customer->points -= $validated['points_used_to_pay'];
            }

            if ($validated['points_gained'] != 0) {
                $newOrder->customer->points += $validated['points_gained'];
            }

            $newOrder->customer->save();
        }

        $order_local_number = 1;

        for ($i = 0; $i < count($request['order_items']); $i++) {
            $quantity = 0;

            do {
                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'order_local_number' => $order_local_number++,
                    'product_id' => $validated['order_items'][$i]['product_id'],
                    'status' => $validated['order_items'][$i]['product_type'] == 'hot dish' ? 'W' : 'R',
                    'price' => $validated['order_items'][$i]['product_price'],
                ]);

                $quantity++;
            } while ($quantity != $request['order_items'][$i]['product_quantity']);
        }

        return new OrderResource($newOrder);
    }

    public function update_ready(Order $order)
    {
        $order->status = 'R';
        $order->save();

        return new OrderResource($order);
    }

    public function update_delivered(Request $request, Order $order)
    {
        $order->delivered_by = $request->user()->id;
        $order->status = 'D';
        $order->save();

        return new OrderResource($order);
    }

    public function update_cancelled(Order $order)
    {
        if ($order->customer_id) {
            if ($order->points_gained != 0)
                $order->customer->points -= $order->points_gained;

            if ($order->points_used_to_pay != 0)
                $order->customer->points += $order->points_used_to_pay;

            $order->customer->save();
        }

        $order->status = 'C';
        $order->total_paid = 0;
        $order->total_paid_with_points = 0;
        $order->points_gained = 0;
        $order->points_used_to_pay = 0;
        $order->save();

        return new OrderResource($order);
    }
}
