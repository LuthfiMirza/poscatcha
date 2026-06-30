@extends('layouts.buyer', ['title' => 'Pesanan Saya'])

@section('content')
<h1 class="mb-4 text-2xl font-bold">Pesanan Saya</h1>
<div class="mb-4 flex gap-2">
    <a class="rounded-full px-4 py-2 text-sm font-semibold {{ $group === 'active' ? 'bg-orange-700 text-white' : 'bg-white text-stone-600' }}" href="{{ route('buyer.orders.index', ['group' => 'active']) }}">Berjalan</a>
    <a class="rounded-full px-4 py-2 text-sm font-semibold {{ $group === 'completed' ? 'bg-orange-700 text-white' : 'bg-white text-stone-600' }}" href="{{ route('buyer.orders.index', ['group' => 'completed']) }}">Selesai</a>
    <a class="rounded-full px-4 py-2 text-sm font-semibold {{ $group === 'cancelled' ? 'bg-orange-700 text-white' : 'bg-white text-stone-600' }}" href="{{ route('buyer.orders.index', ['group' => 'cancelled']) }}">Dibatalkan</a>
</div>
<div class="space-y-3">
    @forelse ($orders as $order)
        <a href="{{ route('buyer.orders.show', $order) }}" class="block rounded-3xl bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="font-bold">{{ $order->order_code }}</p>
                <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">{{ $order->statusLabel() }}</span>
            </div>
            <p class="mt-1 text-sm text-stone-500">{{ $order->created_at->format('d M Y H:i') }}</p>
            <p class="mt-2 font-bold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
        </a>
    @empty
        <div class="rounded-3xl bg-white p-6 text-center text-stone-500 shadow-sm">Belum ada pesanan.</div>
    @endforelse
</div>
<div class="mt-5">{{ $orders->links() }}</div>
@endsection
