<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreUpdateOrderRequest;
use App\Models\Order_Item;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function getOrdersOfCustomer(Customer $customer)
    {
        return OrderResource::collection($customer->orders);
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
        Order_Item::where('order_id', $order->id)->delete();
        $order->delete();
        return new OrderResource($order);
    }
}
