<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function order_list(Request $request)
    {
        $request->validate([
            'page' => 'integer',
        ]);

        $orders = Order::query()
            ->with('customer')
            ->where('status', '!=', 'cancelled')   // キャンセル済みは一覧に出さない
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'orders' => $orders->map(fn (Order $o) => [
                'order_no'      => $o->order_no,
                'customer_name' => $o->customer->name,
                'total'         => $o->total,
                'delivery_date' => $o->delivery_date?->format('Y/m/d'),
                'remarks'       => $o->remarks,
                'created_at'    => $o->created_at->format('Y/m/d H:i'),
            ]),
            'total' => $orders->total(),
        ]);
    }
}
