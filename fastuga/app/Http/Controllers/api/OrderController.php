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

    public function store(Request $request)
    {
        //$validated = $request->validated();

        $lastOrder = Order::latest('id')->first();

        $newOrder = Order::create([
            'ticket_number' => $lastOrder->ticket_number == 99 ? 1 : $lastOrder->ticket_number + 1,
            'status' => 'P',
            'customer_id' => $request['customer_id'],
            'total_price' => $request['total_price'],
            'total_paid' => $request['total_paid'],
            'total_paid_with_points' => $request['total_paid_with_points'],
            'points_gained' => $request['points_gained'],
            'points_used_to_pay' => $request['points_used_to_pay'],
            'payment_type' => $request['payment_type'],
            'payment_reference' => $request['payment_reference'],
            'date' => date("Y-m-d"),
            'delivered_by' => null,
        ]);

        $order_local_number = 1;

        for ($i = 0; $i < count($request['order_items']); $i++) {
            $quantity = 0;

            do {
                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'order_local_number' => $order_local_number++,
                    'product_id' => $request['order_items'][$i]['product_id'],
                    'status' => $request['order_items'][$i]['type'] == 'hot dish' ? 'W' : 'R',
                    'price' => $request['order_items'][$i]['price'],
                    'preparation_by' => null,
                    'notes' => null,
                ]);

                $quantity++;

            } while ($quantity != $request['order_items'][$i]['quantity']);
        }

        return new OrderResource($newOrder);
    }

    public function update(StoreUpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return new OrderResource($order);
    }

    public function update_ready(Order $order)
    {
        $order->status = 'R';
        $order->save();

        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        OrderItem::where('order_id', $order->id)->delete();
        $order->delete();
        return new OrderResource($order);
    }
}
