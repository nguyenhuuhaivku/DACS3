<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated user.
     */
    public function index()
    {
        $orders = Auth::user()->orders()->latest()->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:dine-in,takeaway',
            'items' => 'required|array',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
            'pickup_time' => 'required_if:type,takeaway|nullable|date',
        ]);

        // Create the order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'type' => $request->type,
            'status' => 'pending',
            'order_date' => now(),
            'special_instructions' => $request->special_instructions,
            'pickup_time' => $request->pickup_time,
        ]);

        $totalAmount = 0;

        // Create order items
        foreach ($request->items as $item) {
            $menuItem = Menu::findOrFail($item['menu_id']);
            $subtotal = $menuItem->price * $item['quantity'];
            
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $menuItem->price,
                'subtotal' => $subtotal,
                'special_instructions' => $item['special_instructions'] ?? null,
            ]);

            $totalAmount += $subtotal;
        }

        // Apply tax (assuming 10% tax)
        $taxAmount = $totalAmount * 0.1;
        
        // Update order with the total and tax amounts
        $order->update([
            'total_amount' => $totalAmount + $taxAmount,
            'tax_amount' => $taxAmount,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order)
    {
        $this->authorize('update', $order);
        
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);
            return redirect()->route('orders.index')
                ->with('success', 'Order cancelled successfully.');
        }

        return redirect()->route('orders.index')
            ->with('error', 'This order cannot be cancelled.');
    }
} 