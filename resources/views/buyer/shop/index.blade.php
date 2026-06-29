@extends('layouts.buyer', ['title' => 'Katalog'])

@section('content')
@php
    $hasFilter = filled($search) || filled($selectedCategory);
@endphp

<section class="space-y-5">
    <div class="overflow-hidden rounded-[2rem] bg-white p-4 shadow-sm ring-1 ring-orange-100/70 md:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">Pickup Store</p>
                <h1 class="mt-2 text-2xl font-black leading-tight text-stone-950 md:text-3xl">
                    Halo, {{ auth()->check() ? auth()->user()->name : 'selamat datang' }} 👋
                </h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-stone-500">
                    Pilih menu favorit dari CATcha, lalu ambil pesananmu di kasir.
                </p>
            </div>
            <div class="hidden rounded-3xl bg-orange-50 px-4 py-3 text-right md:block">
                <p class="text-xs font-semibold text-stone-500">Estimasi pickup</p>
                <p class="text-lg font-black text-orange-700">15–20 menit</p>
            </div>
        </div>

        <form class="mt-5 flex items-center gap-2 rounded-3xl bg-stone-50 p-2 ring-1 ring-stone-100" method="GET" action="{{ route('buyer.shop.index') }}">
            @if (filled($selectedCategory))
                <input type="hidden" name="category" value="{{ $selectedCategory }}">
            @endif
            <span class="pl-3 text-stone-400">⌕</span>
            <input class="min-w-0 flex-1 border-0 bg-transparent px-1 py-2 text-sm font-medium text-stone-700 placeholder:text-stone-400 focus:ring-0" name="q" value="{{ $search }}" placeholder="Cari kopi, minuman, atau menu favorit..." maxlength="80">
            <button class="rounded-2xl bg-orange-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Cari</button>
        </form>
    </div>

    <div class="-mx-4 overflow-x-auto px-4 pb-1 md:mx-0 md:px-0">
        <div class="flex min-w-max gap-2">
            <a href="{{ route('buyer.shop.index', filled($search) ? ['q' => $search] : []) }}" class="rounded-full px-4 py-2 text-sm font-bold transition {{ blank($selectedCategory) ? 'bg-orange-700 text-white shadow-sm' : 'bg-white text-stone-600 ring-1 ring-stone-100 hover:text-orange-700' }}">Semua</a>
            @foreach ($categories as $category)
                <a href="{{ route('buyer.shop.index', array_filter(['q' => $search, 'category' => $category->category_id])) }}" class="rounded-full px-4 py-2 text-sm font-bold transition {{ $selectedCategory === $category->category_id ? 'bg-orange-700 text-white shadow-sm' : 'bg-white text-stone-600 ring-1 ring-stone-100 hover:text-orange-700' }}">{{ $category->category_name }}</a>
            @endforeach
        </div>
    </div>

    <section class="grid gap-3 md:grid-cols-2">
        <div class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-orange-700 via-amber-600 to-yellow-500 p-5 text-white shadow-sm">
            <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/15"></div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-100">Promo & Combo</p>
            <h2 class="mt-2 text-xl font-black">Temani harimu dengan menu favorit</h2>
            <p class="mt-2 text-sm leading-6 text-orange-50">Checkout pickup cepat tanpa antre lama di kasir.</p>
        </div>
        <div class="relative overflow-hidden rounded-[1.75rem] bg-white p-5 shadow-sm ring-1 ring-orange-100/70">
            <div class="absolute -right-6 bottom-0 h-24 w-24 rounded-full bg-orange-100"></div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">Menu Fresh</p>
            <h2 class="mt-2 text-xl font-black text-stone-950">Harga dan stok selalu mengikuti POS</h2>
            <p class="mt-2 max-w-sm text-sm leading-6 text-stone-500">Data produk langsung dari database kasir, tanpa katalog terpisah.</p>
        </div>
    </section>

    <div class="flex items-end justify-between gap-3 pt-1">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">Katalog</p>
            <h2 class="mt-1 text-xl font-black text-stone-950 md:text-2xl">Temukan Menu Favoritmu</h2>
        </div>
        @if ($hasFilter)
            <a href="{{ route('buyer.shop.index') }}" class="shrink-0 rounded-full bg-white px-4 py-2 text-xs font-bold text-orange-700 shadow-sm ring-1 ring-orange-100">Hapus Filter</a>
        @endif
    </div>

    <div class="grid grid-cols-2 items-stretch gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-4">
        @forelse ($products as $product)
            @php
                $availableQuantity = $product->availableQuantity();
                $isAvailable = $availableQuantity > 0;
            @endphp
            <article class="group flex h-full flex-col overflow-hidden rounded-[1.75rem] bg-white shadow-sm ring-1 ring-stone-100 transition hover:-translate-y-0.5 hover:shadow-md">
                <a href="{{ route('buyer.products.show', $product) }}" class="block">
                    <div class="aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-stone-50 to-orange-50/50">
                        @if ($product->imageUrl())
                            <img class="h-full w-full object-contain p-4 transition duration-300 group-hover:scale-105" loading="lazy" src="{{ $product->imageUrl() }}" alt="{{ $product->product_name }}">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-xs font-bold text-stone-400">No Image</div>
                        @endif
                    </div>
                </a>

                <div class="flex flex-1 flex-col p-3 md:p-4">
                    <a href="{{ route('buyer.products.show', $product) }}" class="block">
                        <p class="mb-2 truncate text-[11px] font-bold uppercase tracking-wide text-orange-700">{{ $product->category?->category_name ?? 'Tanpa Kategori' }}</p>
                        <h3 class="line-clamp-2 min-h-[2.5rem] text-sm font-black leading-5 text-stone-950 md:text-base md:leading-6">{{ $product->product_name }}</h3>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <p class="text-sm font-black text-orange-700 md:text-base">Rp{{ number_format($product->product_price, 0, ',', '.') }}</p>
                            <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-bold {{ $isAvailable ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">{{ $isAvailable ? 'Tersedia ' . $availableQuantity : 'Habis' }}</span>
                        </div>
                    </a>

                    <div class="mt-auto grid grid-cols-1 gap-2 pt-4 lg:grid-cols-2">
                        @if (! auth()->check() || auth()->user()->hasRole('buyer'))
                            @if ($isAvailable)
                                <a href="{{ route('buyer.products.show', $product) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-orange-700 px-3 text-center text-xs font-bold leading-tight text-white transition hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Tambah ke Keranjang</a>
                                <a href="{{ route('buyer.products.show', $product) }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl border border-orange-700 px-3 text-center text-xs font-bold leading-tight text-orange-700 transition hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Beli Sekarang</a>
                            @else
                                <span class="inline-flex min-h-11 w-full cursor-not-allowed items-center justify-center rounded-2xl bg-stone-200 px-3 text-center text-xs font-bold leading-tight text-stone-400">Tambah ke Keranjang</span>
                                <span class="inline-flex min-h-11 w-full cursor-not-allowed items-center justify-center rounded-2xl border border-stone-200 px-3 text-center text-xs font-bold leading-tight text-stone-400">Beli Sekarang</span>
                            @endif
                        @else
                            <a href="{{ auth()->user()->hasRole('admin') ? route('dashboard_admin') : route('selling_product') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl border border-orange-700 px-3 text-center text-xs font-bold leading-tight text-orange-700 transition hover:bg-orange-50 lg:col-span-2">Kembali ke Dashboard</a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-2 rounded-[1.75rem] bg-white p-8 text-center shadow-sm ring-1 ring-stone-100 md:col-span-3 xl:col-span-4">
                <p class="text-lg font-black text-stone-900">{{ $hasFilter ? 'Produk yang kamu cari belum ditemukan.' : 'Belum ada produk yang tersedia.' }}</p>
                <p class="mt-2 text-sm text-stone-500">Coba kata kunci atau kategori lain.</p>
                @if ($hasFilter)
                    <a href="{{ route('buyer.shop.index') }}" class="mt-4 inline-flex rounded-full bg-orange-700 px-5 py-2.5 text-sm font-bold text-white">Hapus Filter</a>
                @endif
            </div>
        @endforelse
    </div>

    <div class="pt-2">{{ $products->links('vendor.pagination.buyer') }}</div>
</section>
@endsection
