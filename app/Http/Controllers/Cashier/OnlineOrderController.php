<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OnlineOrdering\OrderWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnlineOrderController extends Controller
{
    public function index(Request $request): View
    {
        $kanbanStatuses = [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PROCESSING];
        $kanbanOrders = Order::query()
            ->with('buyer')
            ->withCount('items')
            ->whereIn('status', $kanbanStatuses)
            ->latest()
            ->get()
            ->groupBy('status');

        $orders = Order::query()
            ->with('buyer')
            ->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($nested) use ($request) {
                    $nested->where('order_code', 'like', '%'.$request->q.'%')
                        ->orWhereHas('buyer', fn ($buyer) => $buyer->where('name', 'like', '%'.$request->q.'%'));
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('cashier.online-orders.index', compact('orders', 'kanbanStatuses', 'kanbanOrders'));
    }

    public function show(Order $order): View
    {
        return view('cashier.online-orders.show', [
            'order' => $order->load('buyer', 'items', 'confirmer', 'completer', 'canceller', 'sale', 'statusHistories.actor'),
        ]);
    }

    public function verifyPayment(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $workflow->verifyPayment($order, $request->user());

        return back()->with('success', 'Pembayaran QRIS diverifikasi.');
    }

    public function rejectPayment(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $workflow->rejectPayment($order, $request->user(), $validated['reason'] ?? null);

        return back()->with('success', 'Pembayaran QRIS ditolak. Pembeli dapat membayar ulang dari detail order.');
    }

    public function print(Order $order): View
    {
        return view('cashier.online-orders.print', [
            'order' => $order->load('buyer', 'items'),
        ]);
    }

    public function confirm(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $workflow->confirm($order, $request->user());

        return back()->with('success', 'Pesanan dikonfirmasi dan stok dikurangi.');
    }

    public function process(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $workflow->startProcessing($order, $request->user());

        return back()->with('success', 'Pesanan masuk tahap processing.');
    }

    public function complete(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $workflow->complete($order, $request->user());

        return back()->with('success', 'Pesanan selesai dan penjualan tercatat.');
    }

    public function cancel(Request $request, Order $order, OrderWorkflowService $workflow): RedirectResponse
    {
        $validated = $request->validate(['cancel_reason' => ['nullable', 'string', 'max:500']]);
        $workflow->cancel($order, $request->user(), $validated['cancel_reason'] ?? 'Dibatalkan oleh kasir');

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}
