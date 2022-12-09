<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreUpdateOrderRequest;
use App\Http\Resources\ProductResource;
use App\Models\OrderItem;
use App\Models\User;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function getOrdersOfCustomer(User $user)
    {
        return OrderResource::collection($user->customer->orders);
    }

    public function getOrdersOfCustomerForPickup(Customer $customer)
    {
        return OrderResource::collection($customer->orders()->where('status', 'in', 'P,R')->get());
    }

    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    public function showWithProducts(Order $order)
    {
        OrderResource::$format = 'withProducts';
        return new OrderResource($order);
    }

    public function store(StoreUpdateOrderRequest $request)
    {
        $newOrder = Order::create($request->validated());
        return new OrderResource($newOrder);
    }

    public function update(StoreUpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        OrderItem::where('order_id', $order->id)->delete();
        $order->delete();
        return new OrderResource($order);
    }
}
