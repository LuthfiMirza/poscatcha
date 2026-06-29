@extends('layouts.buyer', ['title' => $product->product_name])

@section('content')
@php
    $availableQuantity = $product->availableQuantity();
    $isAvailable = $availableQuantity > 0;
@endphp

<div class="space-y-4">
    <a href="{{ route('buyer.shop.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-bold text-orange-700 shadow-sm ring-1 ring-orange-100">
        <span>←</span>
        <span>Kembali ke katalog</span>
    </a>

    <article class="overflow-visible rounded-[2rem] bg-white shadow-sm ring-1 ring-orange-100/70 md:grid md:grid-cols-2">
        <div class="bg-gradient-to-br from-stone-50 to-orange-50 p-5 md:p-8">
            <div class="aspect-square overflow-hidden rounded-[1.75rem] bg-white shadow-inner ring-1 ring-stone-100">
                @if ($product->imageUrl())
                    <img class="h-full w-full object-contain p-6" loading="lazy" src="{{ $product->imageUrl() }}" alt="{{ $product->product_name }}">
                @else
                    <div class="flex h-full w-full items-center justify-center text-sm font-bold text-stone-400">No Image</div>
                @endif
            </div>
        </div>

        <div class="flex flex-col p-5 md:p-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">{{ $product->category?->category_name ?? 'Tanpa Kategori' }}</p>
                <h1 class="mt-3 text-3xl font-black leading-tight text-stone-950 md:text-4xl">{{ $product->product_name }}</h1>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <p class="rounded-2xl bg-orange-50 px-4 py-3 text-2xl font-black text-orange-700">Rp{{ number_format($product->product_price, 0, ',', '.') }}</p>
                    <span class="rounded-full px-3 py-2 text-xs font-bold {{ $isAvailable ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $isAvailable ? 'Tersedia: ' . $availableQuantity : 'Stok habis' }}
                    </span>
                </div>
                <p class="mt-5 rounded-3xl bg-stone-50 p-4 text-sm leading-6 text-stone-500">
                    Data harga dan ketersediaan mengikuti POS kasir berdasarkan resep produk dan stok bahan baku.
                </p>
            </div>

            <div class="mt-auto pt-6">
                <div class="mb-5 rounded-3xl bg-orange-50 p-4">
                    <h2 class="text-sm font-black text-stone-950">Pilih detail pesanan</h2>
                    <p class="mt-1 text-xs leading-5 text-stone-500">Atur ice level, sugar level, dan add-ons sebelum masuk keranjang.</p>
                </div>

                @guest
                    <div class="space-y-4">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <p class="mb-2 text-sm font-bold text-stone-700">Ice Level</p>
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-500">Pilih setelah login</div>
                            </div>
                            <div>
                                <p class="mb-2 text-sm font-bold text-stone-700">Sugar Level</p>
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-500">Pilih setelah login</div>
                            </div>
                        </div>
                        @if ($isAvailable)
                            <div class="grid gap-3 sm:grid-cols-2">
                                <a href="{{ route('buyer.login.required', ['redirect_to' => route('buyer.products.show', $product)]) }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-orange-700 px-4 text-center font-bold text-white transition hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Tambah ke Keranjang</a>
                                <a href="{{ route('buyer.login.required', ['redirect_to' => route('buyer.products.show', $product)]) }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-orange-700 px-4 text-center font-bold text-orange-700 transition hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Beli Sekarang</a>
                            </div>
                        @else
                            <div class="grid gap-3 sm:grid-cols-2">
                                <span class="inline-flex min-h-12 w-full cursor-not-allowed items-center justify-center rounded-2xl bg-stone-200 px-4 text-center font-bold text-stone-400">Tambah ke Keranjang</span>
                                <span class="inline-flex min-h-12 w-full cursor-not-allowed items-center justify-center rounded-2xl border border-stone-200 px-4 text-center font-bold text-stone-400">Beli Sekarang</span>
                            </div>
                        @endif
                    </div>
                @elseif (auth()->user()->hasRole('buyer'))
                    <form method="POST" action="{{ route('buyer.cart.add', $product) }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-stone-700" for="quantity">Quantity</label>
                            <input id="quantity" type="number" min="1" max="{{ $availableQuantity }}" name="quantity" value="1" @disabled(! $isAvailable) class="mt-2 min-h-12 w-full rounded-2xl border-stone-200 text-center font-bold disabled:bg-stone-100 disabled:text-stone-400 sm:w-32">
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-bold text-stone-700" id="ice-level-label">Ice Level</label>
                                <div
                                    x-data="{
                                        open: false,
                                        value: @js(old('ice_level', 'Normal Ice')),
                                        options: ['No Ice', 'Less Ice', 'Normal Ice', 'Extra Ice'],
                                    }"
                                    class="relative mt-2"
                                    @keydown.escape.window="open = false"
                                >
                                    <input type="hidden" name="ice_level" x-model="value">
                                    <button
                                        type="button"
                                        class="flex min-h-12 w-full items-center justify-between gap-3 rounded-2xl border border-stone-200 bg-white px-4 text-left text-base font-semibold text-stone-900 transition focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-100"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        :aria-expanded="open.toString()"
                                        aria-labelledby="ice-level-label"
                                    >
                                        <span x-text="value"></span>
                                        <svg class="h-5 w-5 shrink-0 text-stone-500 transition" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div
                                        x-cloak
                                        x-show="open"
                                        x-transition.origin.top
                                        class="absolute left-0 right-0 top-full z-[70] mt-2 max-h-60 overflow-y-auto rounded-2xl border border-stone-200 bg-white p-1 shadow-xl"
                                    >
                                        <template x-for="option in options" :key="option">
                                            <button
                                                type="button"
                                                class="flex min-h-11 w-full items-center justify-between rounded-xl px-3 text-left text-sm font-semibold text-stone-700 hover:bg-orange-50 hover:text-orange-700"
                                                :class="value === option ? 'bg-orange-50 text-orange-700' : ''"
                                                @click="value = option; open = false"
                                            >
                                                <span x-text="option"></span>
                                                <span x-show="value === option" class="text-orange-700">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-stone-700" id="sugar-level-label">Sugar Level</label>
                                <div
                                    x-data="{
                                        open: false,
                                        value: @js(old('sugar_level', 'Normal Sugar')),
                                        options: ['No Sugar', 'Less Sugar', 'Normal Sugar', 'Extra Sugar'],
                                    }"
                                    class="relative mt-2"
                                    @keydown.escape.window="open = false"
                                >
                                    <input type="hidden" name="sugar_level" x-model="value">
                                    <button
                                        type="button"
                                        class="flex min-h-12 w-full items-center justify-between gap-3 rounded-2xl border border-stone-200 bg-white px-4 text-left text-base font-semibold text-stone-900 transition focus:border-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-100"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                        :aria-expanded="open.toString()"
                                        aria-labelledby="sugar-level-label"
                                    >
                                        <span x-text="value"></span>
                                        <svg class="h-5 w-5 shrink-0 text-stone-500 transition" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div
                                        x-cloak
                                        x-show="open"
                                        x-transition.origin.top
                                        class="absolute left-0 right-0 top-full z-[70] mt-2 max-h-60 overflow-y-auto rounded-2xl border border-stone-200 bg-white p-1 shadow-xl"
                                    >
                                        <template x-for="option in options" :key="option">
                                            <button
                                                type="button"
                                                class="flex min-h-11 w-full items-center justify-between rounded-xl px-3 text-left text-sm font-semibold text-stone-700 hover:bg-orange-50 hover:text-orange-700"
                                                :class="value === option ? 'bg-orange-50 text-orange-700' : ''"
                                                @click="value = option; open = false"
                                            >
                                                <span x-text="option"></span>
                                                <span x-show="value === option" class="text-orange-700">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="block text-sm font-bold text-stone-700">Add-ons</p>
                            <div class="mt-2 grid gap-2">
                                @forelse ($addOnProducts as $addOn)
                                    @php $addOnAvailable = $addOn->availableQuantity() > 0; @endphp
                                    <label class="flex items-center justify-between gap-3 rounded-2xl border border-stone-200 px-4 py-3 {{ $addOnAvailable ? 'bg-white' : 'bg-stone-50 opacity-60' }}">
                                        <span class="min-w-0">
                                            <span class="block text-sm font-black text-stone-950">{{ $addOn->product_name }}</span>
                                            <span class="text-xs font-semibold text-orange-700">Rp{{ number_format($addOn->product_price, 0, ',', '.') }} • {{ $addOnAvailable ? 'Tersedia' : 'Habis' }}</span>
                                        </span>
                                        <input type="checkbox" name="add_ons[]" value="{{ $addOn->product_name }}" @disabled(! $addOnAvailable) class="rounded border-stone-300 text-orange-700 focus:ring-orange-600">
                                    </label>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-stone-200 px-4 py-3 text-sm font-semibold text-stone-500">Belum ada add-ons tersedia.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <button @disabled(! $isAvailable) class="min-h-12 rounded-2xl bg-orange-700 px-4 font-bold text-white transition hover:bg-orange-800 disabled:cursor-not-allowed disabled:bg-stone-200 disabled:text-stone-400">Tambah ke Keranjang</button>
                            <button formaction="{{ route('buyer.cart.buy-now', $product) }}" @disabled(! $isAvailable) class="min-h-12 rounded-2xl border border-orange-700 px-4 font-bold text-orange-700 transition hover:bg-orange-50 disabled:cursor-not-allowed disabled:border-stone-200 disabled:text-stone-400">Beli Sekarang</button>
                        </div>
                    </form>
                @else
                    <a href="{{ auth()->user()->hasRole('admin') ? route('dashboard_admin') : route('selling_product') }}" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl border border-orange-700 px-4 text-center font-bold text-orange-700 transition hover:bg-orange-50">Kembali ke Dashboard</a>
                @endguest
            </div>
        </div>
    </article>
</div>
@endsection
