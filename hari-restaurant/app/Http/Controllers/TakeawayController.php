<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TakeawayController extends Controller
{
    /**
     * Display the takeaway order form.
     */
    public function index()
    {
        return view('takeaway.index');
    }

    /**
     * Store a new takeaway order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pickup_time' => 'required|date|after:now',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.id' => 'required|exists:menus,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        // Calculate order totals
        $cartItems = $request->cart_items;
        $totalAmount = 0;
        $taxRate = 0.1; // 10% tax
        
        // Create the order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'TO-' . Str::upper(Str::random(8)),
            'type' => 'takeaway',
            'status' => 'pending',
            'order_date' => now(),
            'pickup_time' => $request->pickup_time,
            'special_instructions' => $request->special_instructions,
        ]);

        // Process each cart item
        foreach ($cartItems as $item) {
            $menuItem = Menu::findOrFail($item['id']);
            $subtotal = $menuItem->price * $item['quantity'];
            $totalAmount += $subtotal;

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menuItem->id,
                'quantity' => $item['quantity'],
                'price' => $menuItem->price,
                'subtotal' => $subtotal,
                'special_instructions' => $item['special_instructions'] ?? null,
            ]);
        }

        // Update order with totals
        $taxAmount = $totalAmount * $taxRate;
        $order->update([
            'total_amount' => $totalAmount + $taxAmount,
            'tax_amount' => $taxAmount,
        ]);

        return redirect()->route('takeaway.confirmation', $order->id)
            ->with('success', __('Takeaway order placed successfully!'));
    }

    /**
     * Display the order confirmation page.
     */
    public function confirmation(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('takeaway.confirmation', compact('order'));
    }

    /**
     * Display user's takeaway order history.
     */
    public function history()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('type', 'takeaway')
            ->latest()
            ->paginate(10);

        return view('takeaway.history', compact('orders'));
    }
} 