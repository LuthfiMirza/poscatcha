@extends('layouts.buyer', ['title' => $order->order_code])

@section('content')
@php
    $canPayQris = $order->payment_method === \App\Models\Order::PAYMENT_QRIS
        && in_array($order->payment_status, [\App\Models\Order::PAYMENT_STATUS_WAITING_VERIFICATION, \App\Models\Order::PAYMENT_STATUS_REJECTED], true)
        && $order->status === \App\Models\Order::STATUS_PENDING;
    $shouldAutoRefresh = in_array($order->status, [\App\Models\Order::STATUS_PENDING, \App\Models\Order::STATUS_CONFIRMED, \App\Models\Order::STATUS_PROCESSING], true);
@endphp

@if (! in_array($order->status, [\App\Models\Order::STATUS_COMPLETED, \App\Models\Order::STATUS_CANCELLED], true))
    <script>
        setTimeout(function () {
            window.location.reload();
        }, 15000);
    </script>
@endif

<div class="rounded-3xl bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div>
            <a href="{{ route('buyer.orders.index') }}" class="text-sm font-semibold text-orange-700">← Pesanan</a>
            <h1 class="mt-3 text-2xl font-bold">{{ $order->order_code }}</h1>
            <p class="text-sm text-stone-500">{{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <span class="rounded-full bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ $order->statusLabel() }}</span>
    </div>
    <div class="mt-5 grid gap-3 text-sm md:grid-cols-3">
        <p><span class="text-stone-500">Pembayaran:</span> {{ $order->paymentMethodLabel() }}</p>
        <p><span class="text-stone-500">Status bayar:</span> {{ $order->paymentStatusLabel() }}</p>
        <p><span class="text-stone-500">Fulfillment:</span> pickup</p>
    </div>
    @if ($order->note)
        <p class="mt-3 rounded-2xl bg-stone-50 p-3 text-sm">{{ $order->note }}</p>
    @endif
</div>

@if ($canPayQris)
    <div class="mt-4 rounded-3xl border border-orange-200 bg-orange-50 p-5 shadow-sm">
        <div class="grid gap-5 md:grid-cols-[1fr_220px] md:items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">Menunggu Pembayaran</p>
                <h2 class="mt-2 text-xl font-black text-stone-950">Selesaikan Pembayaran QRIS</h2>
                @if ($order->payment_status === \App\Models\Order::PAYMENT_STATUS_REJECTED)
                    <p class="mt-2 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold leading-6 text-red-600">
                        Pembayaran sebelumnya ditolak. Silakan bayar ulang sesuai total pesanan, lalu tunjukkan bukti pembayaran yang benar ke kasir.
                    </p>
                @else
                    <p class="mt-2 text-sm leading-6 text-stone-600">
                        Kalau tadi lupa scan QRIS, kamu masih bisa bayar dari halaman ini. Scan QRIS di samping, bayar sesuai total pesanan, lalu tunjukkan bukti pembayaran ke kasir saat pickup.
                    </p>
                @endif
                <div class="mt-4 rounded-2xl bg-white/80 p-4 text-sm font-semibold text-stone-700">
                    <div class="flex justify-between gap-3">
                        <span>Kode Order</span>
                        <span>{{ $order->order_code }}</span>
                    </div>
                    <div class="mt-2 flex justify-between gap-3 text-lg font-black text-orange-700">
                        <span>Total Bayar</span>
                        <span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="mt-3 text-xs font-semibold leading-5 text-stone-500">
                    Status akan berubah setelah kasir/admin memverifikasi pembayaran. Jika ingin ganti bayar cash, batalkan pesanan ini lalu checkout ulang dengan metode Cash.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-3 shadow-sm ring-1 ring-orange-100">
                <a href="{{ asset('assets/payment/qris-catcha.jpg') }}" target="_blank" rel="noopener" class="block">
                    <img
                        src="{{ asset('assets/payment/qris-catcha.jpg') }}"
                        alt="QRIS CATcha"
                        class="mx-auto aspect-square w-full rounded-2xl object-contain"
                    >
                </a>
                <div class="mt-3 grid grid-cols-2 gap-2 text-center text-xs font-bold">
                    <a
                        href="{{ asset('assets/payment/qris-catcha.jpg') }}"
                        download="qris-catcha.jpg"
                        class="rounded-2xl bg-orange-700 px-3 py-2 text-white"
                    >Download</a>
                    <a
                        href="{{ asset('assets/payment/qris-catcha.jpg') }}"
                        target="_blank"
                        rel="noopener"
                        class="rounded-2xl border border-orange-700 px-3 py-2 text-orange-700"
                    >Buka QRIS</a>
                </div>
            </div>
        </div>
    </div>
@endif

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
@if ($shouldAutoRefresh)
    <script>
        setTimeout(function () {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 10000);
    </script>
@endif
@endsection
