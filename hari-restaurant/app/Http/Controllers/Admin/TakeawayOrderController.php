<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TakeawayOrder;
use App\Models\TakeawayOrderItem;
use App\Models\TakeawayOrderTracking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TakeawayOrderController extends Controller
{
    /**
     * Display a listing of the current takeaway orders.
     */
    public function current()
    {
        $orders = TakeawayOrder::with(['user', 'items'])
            ->current()
            ->latest()
            ->paginate(10);

        // Count orders by status
        $statusCounts = TakeawayOrder::current()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.takeaway.current', compact('orders', 'statusCounts'));
    }

    /**
     * Display a listing of the completed takeaway orders.
     */
    public function completed()
    {
        $orders = TakeawayOrder::with(['user', 'items'])
            ->completed()
            ->latest()
            ->paginate(10);

        return view('admin.takeaway.completed', compact('orders'));
    }

    /**
     * Display the specified takeaway order.
     */
    public function show(TakeawayOrder $order)
    {
        $order->load(['user', 'items.menuItem', 'tracking.admin']);
        
        // Get all statuses for dropdown
        $statuses = [
            'Pending' => 'Pending',
            'Confirmed' => 'Confirmed',
            'In Preparation' => 'In Preparation',
            'Out for Delivery' => 'Out for Delivery',
            'Delivered' => 'Delivered',
            'Cancelled' => 'Cancelled'
        ];

        // Get tracking timeline
        $timeline = $order->tracking()->orderBy('created_at')->get();

        return view('admin.takeaway.show', compact('order', 'statuses', 'timeline'));
    }

    /**
     * Update the status of a takeaway order.
     */
    public function updateStatus(Request $request, TakeawayOrder $order)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,In Preparation,Out for Delivery,Delivered,Cancelled',
            'comment' => 'nullable|string|max:500',
        ]);

        // Start transaction
        DB::beginTransaction();

        try {
            // Update the order status
            $order->update([
                'status' => $request->status,
            ]);

            // Create tracking record
            TakeawayOrderTracking::create([
                'order_id' => $order->id,
                'status' => $request->status,
                'comment' => $request->comment,
                'created_by' => Auth::id(),
            ]);

            // If order is delivered, set delivery time
            if ($request->status === 'Delivered') {
                $order->update([
                    'delivery_time' => now(),
                ]);
            }

            // If order is confirmed, set estimated delivery time (1 hour from now by default)
            if ($request->status === 'Confirmed') {
                $order->update([
                    'estimated_delivery_time' => now()->addHour(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.takeaway-orders.show', $order->id)
                ->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Update the estimated delivery time.
     */
    public function updateDeliveryTime(Request $request, TakeawayOrder $order)
    {
        $request->validate([
            'estimated_delivery_time' => 'required|date|after:now',
        ]);

        $order->update([
            'estimated_delivery_time' => $request->estimated_delivery_time,
        ]);

        return redirect()->route('admin.takeaway-orders.show', $order->id)
            ->with('success', 'Estimated delivery time updated successfully.');
    }

    /**
     * Export the order details as PDF.
     */
    public function export(TakeawayOrder $order)
    {
        $order->load(['user', 'items.menuItem']);
        
        // Implementation of PDF generation would go here
        // For now, just redirect back with a message
        return back()->with('info', 'PDF export functionality will be implemented soon.');
    }

    /**
     * Search orders by criteria
     */
    public function search(Request $request)
    {
        $query = TakeawayOrder::with(['user', 'items']);

        // Filter by order code
        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        // Filter by customer name
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        // Filter by phone
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter completed/current
        if ($request->filled('completed') && $request->completed == 1) {
            $query->completed();
            $view = 'admin.takeaway.completed';
        } else {
            $query->current();
            
            // Count orders by status for the current view
            $statusCounts = TakeawayOrder::current()
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
                
            $view = 'admin.takeaway.current';
        }

        $orders = $query->latest()->paginate(10);
        
        // If we're on the current page, pass the status counts
        if ($view === 'admin.takeaway.current') {
            return view($view, compact('orders', 'statusCounts'));
        }
        
        return view($view, compact('orders'));
    }
} 