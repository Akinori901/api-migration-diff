<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderListRequest;
use App\Models\Order;

class OrderListController extends Controller
{
    public function __invoke(OrderListRequest $request)
    {
        $orders = Order::query()
            ->with('customer')
            ->orderBy('id')
            ->paginate($request->integer('per_page', 50));

        return response()->json([
            'orders' => $orders->map(fn (Order $o) => [
                'orderNo'       => $o->order_no,
                'customer_name' => $o->customer->name,
                'total'         => $o->total,
                'created_at'    => $o->created_at->toIso8601String(),
            ]),
            'total' => $orders->total(),
        ]);
    }
}
