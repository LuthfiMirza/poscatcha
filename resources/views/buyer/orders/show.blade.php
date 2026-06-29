@extends('layouts.buyer', ['title' => $order->order_code])

@section('content')
<div class="rounded-3xl bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div>
            <a href="{{ route('buyer.orders.index') }}" class="text-sm font-semibold text-orange-700">← Pesanan</a>
            <h1 class="mt-3 text-2xl font-bold">{{ $order->order_code }}</h1>
            <p class="text-sm text-stone-500">{{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <span class="rounded-full bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ $order->status }}</span>
    </div>
    <div class="mt-5 grid gap-3 text-sm md:grid-cols-3">
        <p><span class="text-stone-500">Pembayaran:</span> {{ $order->paymentMethodLabel() }}</p>
        <p><span class="text-stone-500">Status bayar:</span> {{ $order->payment_status }}</p>
        <p><span class="text-stone-500">Fulfillment:</span> pickup</p>
    </div>
    @if ($order->note)
        <p class="mt-3 rounded-2xl bg-stone-50 p-3 text-sm">{{ $order->note }}</p>
    @endif
</div>

<div class="mt-4 rounded-3xl bg-white p-5 shadow-sm">
    <h2 class="font-bold">Item</h2>
    <div class="mt-3 divide-y divide-stone-100">
        @foreach ($order->items as $item)
            <div class="flex justify-between py-3 text-sm">
                <div>
                    <p class="font-semibold">{{ $item->product_name }}</p>
                    @if ($item->customizationSummary())
                        <p class="text-xs font-semibold text-stone-500">{{ $item->customizationSummary() }}</p>
                    @endif
                    <p class="text-stone-500">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                </div>
                <p class="font-semibold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>
    <div class="mt-3 flex justify-between text-lg font-bold">
        <span>Total</span>
        <span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
    </div>
</div>

<div class="mt-4 rounded-3xl bg-white p-5 shadow-sm">
    <h2 class="font-bold">Timeline</h2>
    <ul class="mt-3 space-y-2 text-sm text-stone-600">
        <li>Pesanan dibuat: {{ $order->created_at->format('d M Y H:i') }}</li>
        @if ($order->confirmed_at)<li>Dikonfirmasi: {{ $order->confirmed_at->format('d M Y H:i') }}</li>@endif
        @if ($order->processing_at)<li>Diproses: {{ $order->processing_at->format('d M Y H:i') }}</li>@endif
        @if ($order->completed_at)<li>Selesai: {{ $order->completed_at->format('d M Y H:i') }}</li>@endif
        @if ($order->cancelled_at)<li>Dibatalkan: {{ $order->cancelled_at->format('d M Y H:i') }} {{ $order->cancel_reason ? '- ' . $order->cancel_reason : '' }}</li>@endif
    </ul>
    @if ($order->status === \App\Models\Order::STATUS_PENDING)
        <form method="POST" action="{{ route('buyer.orders.cancel', $order) }}" class="mt-4" onsubmit="return confirm('Batalkan pesanan ini?')">
            @csrf
            <button class="rounded-2xl bg-red-50 px-4 py-3 font-semibold text-red-600">Batalkan Pesanan</button>
        </form>
    @endif
</div>
@endsection
