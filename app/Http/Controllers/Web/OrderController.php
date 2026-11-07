<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(string $orderNumber): View
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->firstOrFail();

        return view('pages.order-details', compact('order'));
    }
}
