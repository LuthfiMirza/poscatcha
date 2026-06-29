<div class="space-y-5">
    <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-orange-100/70 md:p-6">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">Keranjang Buyer</p>
        <div class="mt-2 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-stone-950 md:text-3xl">Keranjang</h1>
                <p class="mt-1 text-sm text-stone-500">Tambah atau kurangi quantity langsung seperti POS kasir.</p>
            </div>
            <a href="{{ route('buyer.shop.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-orange-700 px-5 text-sm font-bold text-orange-700 transition hover:bg-orange-50">Lanjut Belanja</a>
        </div>
    </div>

    @if ($errorMessage)
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            {{ $errorMessage }}
        </div>
    @endif

    @php $total = 0; @endphp

    <div class="space-y-3">
        @forelse ($cart->items as $item)
            @php
                $subtotal = (int) $item->product->product_price * (int) $item->quantity;
                $total += $subtotal;
                $availableQuantity = $item->product->availableQuantity();
            @endphp

            <div wire:key="cart-item-{{ $item->id }}" class="grid gap-3 rounded-3xl bg-white p-3 shadow-sm ring-1 ring-stone-100 md:grid-cols-[6rem_1fr_auto] md:items-center md:p-4">
                <div class="flex gap-3 md:block">
                    @if ($item->product->imageUrl())
                        <img class="h-24 w-24 rounded-2xl bg-stone-50 object-contain p-2 md:h-24 md:w-24" src="{{ $item->product->imageUrl() }}" alt="{{ $item->product->product_name }}" loading="lazy">
                    @else
                        <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-stone-100 text-[11px] font-bold text-stone-400">No Image</div>
                    @endif
                </div>

                <div class="min-w-0">
                    <h2 class="line-clamp-2 text-base font-black text-stone-950">{{ $item->product->product_name }}</h2>
                    @if ($item->customizationSummary())
                        <p class="mt-1 text-xs font-semibold leading-5 text-stone-500">{{ $item->customizationSummary() }}</p>
                    @endif
                    <p class="mt-1 text-sm font-bold text-orange-700">Rp{{ number_format($item->product->product_price, 0, ',', '.') }}</p>
                    <p class="mt-1 text-sm font-semibold text-stone-700">Subtotal Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs font-semibold {{ $availableQuantity >= $item->quantity ? 'text-green-700' : 'text-red-600' }}">Tersedia {{ $availableQuantity }}</p>
                </div>

                <div class="grid gap-3 md:min-w-56">
                    <div class="inline-flex h-12 overflow-hidden rounded-2xl border border-stone-200 bg-stone-50">
                        <button type="button" wire:click="decrement({{ $item->id }})" wire:loading.attr="disabled" class="flex w-12 items-center justify-center text-lg font-black text-stone-600 transition hover:bg-white disabled:opacity-50">−</button>
                        <input type="number" min="1" max="{{ $availableQuantity }}" value="{{ $item->quantity }}" wire:change="setQuantity({{ $item->id }}, $event.target.value)" class="w-full border-0 bg-white text-center text-sm font-black text-stone-900 focus:ring-0">
                        <button type="button" wire:click="increment({{ $item->id }})" wire:loading.attr="disabled" @disabled($item->quantity >= $availableQuantity) class="flex w-12 items-center justify-center text-lg font-black text-orange-700 transition hover:bg-white disabled:cursor-not-allowed disabled:text-stone-300">+</button>
                    </div>

                    <button type="button" wire:click="remove({{ $item->id }})" wire:loading.attr="disabled" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-red-50 px-4 text-sm font-bold text-red-700 transition hover:bg-red-100 disabled:opacity-50">Hapus</button>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-stone-100">
                <p class="text-lg font-black text-stone-950">Keranjang masih kosong.</p>
                <p class="mt-2 text-sm text-stone-500">Pilih menu favoritmu dulu dari katalog.</p>
                <a href="{{ route('buyer.shop.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-2xl bg-orange-700 px-5 text-sm font-bold text-white">Lanjut Belanja</a>
            </div>
        @endforelse
    </div>

    <div class="sticky bottom-24 rounded-3xl bg-white p-4 shadow-lg ring-1 ring-orange-100 md:bottom-4 md:p-5">
        <div class="flex items-center justify-between text-lg font-black text-stone-950">
            <span>Total</span>
            <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
        </div>
        <a href="{{ route('buyer.checkout.index') }}" class="mt-4 inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-orange-700 px-4 text-center font-black text-white transition hover:bg-orange-800 {{ $cart->items->isEmpty() ? 'pointer-events-none opacity-50' : '' }}">Lanjut Checkout</a>
    </div>
</div>
