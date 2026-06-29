@extends('layouts.buyer', ['title' => 'Checkout'])

@section('content')
@php
    $total = 0;
    $paymentMethods = [
        'cash' => ['label' => 'Cash saat pickup', 'description' => 'Bayar langsung di kasir saat pesanan diambil.', 'icon' => '💵'],
        'qris' => ['label' => 'QRIS manual', 'description' => 'Bayar dengan QRIS saat pickup atau sesuai arahan kasir.', 'icon' => '▦'],
    ];
    $selectedPayment = array_key_exists(old('payment_method', 'cash'), $paymentMethods) ? old('payment_method', 'cash') : 'cash';
@endphp

<section class="space-y-5">
    <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-orange-100/70 md:p-6">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">Checkout Pickup</p>
        <div class="mt-2 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-stone-950 md:text-3xl">Buat Pesanan</h1>
                <p class="mt-1 text-sm text-stone-500">Periksa item dan pilih metode pembayaran untuk pickup di CATcha.</p>
            </div>
            <a href="{{ route('buyer.cart.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-orange-700 px-5 text-sm font-bold text-orange-700 transition hover:bg-orange-50">Kembali ke Keranjang</a>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-[1fr_380px]">
        <div class="space-y-3">
            @forelse ($cart->items as $item)
                @php
                    $subtotal = (int) $item->product->product_price * (int) $item->quantity;
                    $total += $subtotal;
                @endphp
                <div class="flex items-start justify-between gap-3 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-stone-100">
                    <div class="min-w-0">
                        <p class="line-clamp-2 font-black text-stone-950">{{ $item->product->product_name }}</p>
                        @if ($item->customizationSummary())
                            <p class="mt-1 text-xs font-semibold leading-5 text-stone-500">{{ $item->customizationSummary() }}</p>
                        @endif
                        <p class="mt-1 text-sm font-semibold text-stone-500">{{ $item->quantity }} x Rp{{ number_format($item->product->product_price, 0, ',', '.') }}</p>
                    </div>
                    <p class="shrink-0 text-sm font-black text-orange-700 md:text-base">Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                </div>
            @empty
                <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-stone-100">
                    <p class="text-lg font-black text-stone-950">Keranjang masih kosong.</p>
                    <p class="mt-2 text-sm text-stone-500">Tambahkan menu sebelum checkout.</p>
                </div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('buyer.checkout.store') }}" class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-orange-100/70 md:p-6">
            @csrf

            <div class="rounded-3xl bg-orange-50 p-4">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-700">Fulfillment</p>
                <p class="mt-1 font-black text-stone-950">Pickup di toko</p>
                <p class="mt-1 text-sm text-stone-500">Pesanan diproses oleh kasir setelah order masuk.</p>
            </div>

            <fieldset class="mt-5" x-data="{ paymentMethod: @js($selectedPayment) }">
                <legend class="text-sm font-black text-stone-900">Metode pembayaran</legend>
                <div class="mt-3 grid gap-2">
                    @foreach ($paymentMethods as $value => $method)
                        <label
                            class="cursor-pointer rounded-2xl border p-4 transition"
                            :class="paymentMethod === @js($value) ? 'border-orange-700 bg-orange-50 ring-2 ring-orange-100' : 'border-stone-200 bg-white hover:border-orange-300'"
                        >
                            <input type="radio" name="payment_method" value="{{ $value }}" class="sr-only" x-model="paymentMethod" @checked($selectedPayment === $value) required>
                            <span class="flex items-start gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-lg shadow-sm">{{ $method['icon'] }}</span>
                                <span class="min-w-0">
                                    <span class="flex items-center justify-between gap-3 text-sm font-black text-stone-950">
                                        <span>{{ $method['label'] }}</span>
                                        <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border text-[10px]" :class="paymentMethod === @js($value) ? 'border-orange-700 bg-orange-700 text-white' : 'border-stone-300 text-transparent'">✓</span>
                                    </span>
                                    <span class="mt-1 block text-xs leading-5 text-stone-500">{{ $method['description'] }}</span>
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div x-show="paymentMethod === 'cash'" x-cloak class="mt-3 rounded-2xl border border-stone-200 bg-stone-50 p-4">
                    <p class="text-sm font-black text-stone-950">Pembayaran cash saat pickup</p>
                    <p class="mt-1 text-xs leading-5 text-stone-500">Pesanan dibuat terlebih dahulu, lalu pembayaran dilakukan langsung di kasir saat pesanan diambil.</p>
                </div>

                <div x-show="paymentMethod === 'qris'" x-cloak class="mt-3 rounded-3xl border border-orange-200 bg-orange-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-black text-stone-950">QRIS manual</p>
                            <p class="mt-1 text-xs leading-5 text-stone-600">Tunjukkan bukti pembayaran kepada kasir saat pickup atau ikuti arahan kasir.</p>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-orange-700 shadow-sm">Pickup</span>
                    </div>

                    <div class="mt-4 rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-orange-100">
                        <a href="{{ asset('assets/payment/qris-catcha.jpg') }}" target="_blank" rel="noopener" class="block">
                            <img
                                src="{{ asset('assets/payment/qris-catcha.jpg') }}"
                                alt="QRIS CATcha"
                                class="mx-auto h-72 w-full max-w-56 rounded-2xl bg-white object-contain p-2 ring-1 ring-stone-200"
                            >
                        </a>
                        <p class="mt-2 text-xs font-semibold text-stone-500">Ketuk gambar QRIS untuk membuka ukuran penuh.</p>
                        <p class="mt-4 text-xs font-bold uppercase tracking-[0.16em] text-stone-500">Nominal pembayaran</p>
                        <p class="mt-1 text-2xl font-black text-orange-700">Rp{{ number_format($total, 0, ',', '.') }}</p>
                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                            <a
                                href="{{ asset('assets/payment/qris-catcha.jpg') }}"
                                download="qris-catcha.jpg"
                                class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-orange-700 px-4 text-sm font-black text-white transition hover:bg-orange-800"
                            >Download QRIS</a>
                            <a
                                href="{{ asset('assets/payment/qris-catcha.jpg') }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-orange-700 px-4 text-sm font-black text-orange-700 transition hover:bg-orange-50"
                            >Buka QRIS</a>
                        </div>
                        <p class="mt-3 rounded-2xl bg-stone-50 px-3 py-2 text-xs font-semibold leading-5 text-stone-600">Jika pakai 1 HP, download QRIS lalu buka aplikasi pembayaran dan pilih scan dari galeri/upload gambar.</p>
                    </div>
                </div>

                @error('payment_method')
                    <p class="mt-2 rounded-2xl bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">{{ $message }}</p>
                @enderror
            </fieldset>

            <label class="mt-5 block text-sm font-black text-stone-900" for="note">Catatan opsional</label>
            <textarea id="note" name="note" rows="3" class="mt-2 w-full rounded-2xl border-stone-200 text-sm focus:border-orange-600 focus:ring-orange-600" maxlength="500" placeholder="Contoh: less sugar, pickup jam 15.00">{{ old('note') }}</textarea>
            @error('note')
                <p class="mt-2 rounded-2xl bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">{{ $message }}</p>
            @enderror

            <div class="mt-5 border-t border-stone-100 pt-5">
                <div class="flex items-center justify-between text-lg font-black text-stone-950">
                    <span>Total</span>
                    <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <button @disabled($cart->items->isEmpty()) class="mt-5 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-orange-700 px-4 text-center font-black text-white transition hover:bg-orange-800 disabled:cursor-not-allowed disabled:bg-stone-300">Buat Pesanan</button>
            </div>
        </form>
    </div>
</section>
@endsection
