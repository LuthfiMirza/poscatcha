@php
    $productCollection = collect($products);
    $productMap = $productCollection->keyBy('product_id');
    $categoryMap = \App\Models\Category::query()
        ->orderBy('category_name')
        ->pluck('category_name', 'category_id');
    $categories = $categoryMap->filter(function ($categoryName, $categoryId) use ($productCollection) {
        return $productCollection->contains('product_category', $categoryId);
    });
    $visibleProducts = $selected_category === ''
        ? $productCollection
        : $productCollection->where('product_category', $selected_category);
    $cartItemCount = collect($carts)->sum('quantity');
    $cartIsEmpty = $cartItemCount === 0;
    $lowStockCount = $productCollection->filter(function ($product) {
        return $product->recipes->contains(function ($recipe) {
            return $recipe->rawMaterial && (float) $recipe->rawMaterial->stock <= (float) $recipe->rawMaterial->minimum_stock;
        });
    })->count();
    $selectedProductIds = collect($carts)->pluck('product_id')->map(function ($id) {
        return (string) $id;
    })->all();
    $firstCart = collect($carts)->first();
    $displayTax = 0;
@endphp

<div id="selling-product-root" class="pos-root">
    <style>
        .footer,
        .back-to-top {
            display: none !important;
        }

        .pos-root {
            --cha-orange: #e8650a;
            --cat-green: #6aaa2a;
            --white: #ffffff;
            --surface: #f8f8f8;
            --border: #f0f0f0;
            --text-dark: #1a1a1a;
            --text-muted: #9e9e9e;
            --orange-bg: #fef0e6;
            --green-bg: #eefbe6;
            min-height: calc(100vh - 89px);
            background: #ffffff;
            color: var(--text-dark);
            font-size: 13px;
            line-height: 1.5;
        }

        .pos-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            min-height: calc(100vh - 89px);
            background: #ffffff;
        }

        .pos-products {
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }

        .pos-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 20px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
        }

        .pos-search {
            flex: 1 1 auto;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 36px;
            padding: 0 16px;
            border-radius: 999px;
            background: #f5f5f5;
        }

        .pos-search i {
            color: var(--text-muted);
            font-size: 14px;
        }

        .pos-search input {
            width: 100%;
            border: 0;
            background: transparent;
            box-shadow: none;
            outline: none;
            color: var(--text-dark);
            font-size: 13px;
            padding: 0;
        }

        .pos-stock-warning {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--cha-orange);
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }

        .pos-category-row {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 12px 20px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            scrollbar-width: none;
        }

        .pos-category-row::-webkit-scrollbar,
        .pos-order-list::-webkit-scrollbar {
            display: none;
        }

        .pos-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e5e5e5;
            border-radius: 999px;
            background: #ffffff;
            color: #8b8b8b;
            font-size: 12px;
            font-weight: 400;
            padding: 6px 16px;
            white-space: nowrap;
        }

        .pos-pill.is-active {
            border-color: var(--cha-orange);
            color: var(--cha-orange);
            font-weight: 500;
        }

        .pos-pill__dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            flex: 0 0 7px;
            background: #d4d4d4;
        }

        .pos-pill__dot.is-matcha {
            background: var(--cat-green);
        }

        .pos-pill__dot.is-thai {
            background: var(--cha-orange);
        }

        .pos-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            padding: 20px;
        }

        .pos-card {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #ffffff;
            cursor: pointer;
            transition: border-color 160ms ease, background-color 160ms ease;
        }

        .pos-card:hover {
            border-color: var(--cha-orange);
        }

        .pos-card.is-selected {
            border: 2px solid var(--cha-orange);
            background: var(--orange-bg);
            padding: 15px;
        }

        .pos-card__media {
            display: flex;
            justify-content: center;
        }

        .pos-card__ring {
            width: 88px;
            height: 88px;
            border-radius: 999px;
            background: #f6f6f6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .pos-card__ring.is-matcha {
            background: var(--green-bg);
        }

        .pos-card__ring.is-thai {
            background: var(--orange-bg);
        }

        .pos-card__ring img {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            object-fit: cover;
        }

        .pos-card__placeholder,
        .pos-order__placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #c7c7c7;
        }

        .pos-card__name {
            margin-top: 12px;
            color: #111827;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            min-height: 39px;
        }

        .pos-card__price {
            margin-top: 4px;
            color: var(--cha-orange);
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }

        .pos-card__stock {
            margin-top: 2px;
            color: #9ca3af;
            font-size: 11px;
            text-align: center;
        }

        .pos-card__stock.is-low {
            color: var(--cha-orange);
        }

        .pos-card__stock i {
            margin-right: 3px;
        }

        .pos-card__action {
            margin-top: 12px;
        }

        .pos-add-btn {
            width: 100%;
            min-height: 32px;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            background: #ffffff;
            color: #4b5563;
            font-size: 12px;
            font-weight: 500;
            transition: border-color 160ms ease, color 160ms ease;
        }

        .pos-add-btn:hover,
        .pos-add-btn:focus {
            border-color: var(--cha-orange);
            color: var(--cha-orange);
        }

        .pos-order {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 89px);
            padding: 16px;
            border-left: 1px solid var(--border);
            background: #ffffff;
        }

        .pos-order__invoice {
            color: var(--text-dark);
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
        }

        .pos-order__subtitle {
            margin-top: 4px;
            color: var(--text-muted);
            font-size: 11px;
        }

        .pos-payment-tabs {
            display: flex;
            gap: 8px;
            margin-top: 12px;
            margin-bottom: 14px;
        }

        .pos-payment-tab {
            position: relative;
            cursor: pointer;
            flex: 1 1 0;
        }

        .pos-payment-tab input {
            position: absolute;
            inset: 0;
            opacity: 0;
            pointer-events: none;
        }

        .pos-payment-tab span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 31px;
            padding: 0 12px;
            border: 1px solid #e5e5e5;
            border-radius: 999px;
            background: #ffffff;
            color: #8b8b8b;
            font-size: 12px;
            font-weight: 400;
            transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease;
        }

        .pos-payment-tab input:checked + span {
            border-color: var(--cha-orange);
            background: var(--cha-orange);
            color: #ffffff;
            font-weight: 600;
        }

        .pos-order-list {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }

        .pos-order-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 260px;
            color: #cfcfcf;
            text-align: center;
        }

        .pos-order-empty i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .pos-order-empty span {
            color: #9ca3af;
            font-size: 12px;
        }

        .pos-order-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f8f8f8;
        }

        .pos-order-item__thumb {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: #f5f5f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex: 0 0 36px;
        }

        .pos-order-item__thumb.is-matcha {
            background: var(--green-bg);
        }

        .pos-order-item__thumb.is-thai {
            background: var(--orange-bg);
        }

        .pos-order-item__thumb img {
            width: 36px;
            height: 36px;
            object-fit: cover;
        }

        .pos-order-item__info {
            min-width: 0;
            flex: 1 1 auto;
        }

        .pos-order-item__name {
            color: #111827;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.35;
        }

        .pos-order-item__note {
            margin-top: 2px;
            color: #9ca3af;
            font-size: 11px;
        }

        .pos-order-item__controls {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
        }

        .pos-qty-btn,
        .pos-remove-btn {
            width: 22px;
            height: 22px;
            border: 0;
            border-radius: 8px;
            background: #f3f4f6;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .pos-qty-input {
            width: 28px;
            min-width: 20px;
            border: 0;
            background: transparent;
            box-shadow: none !important;
            color: #111827;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            padding: 0;
        }

        .pos-order-item__price {
            margin-left: auto;
            color: #111827;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .pos-order-footer {
            padding-top: 14px;
        }

        .pos-payment-box {
            margin-bottom: 14px;
            padding: 12px;
            border-radius: 14px;
            background: #fafafa;
            border: 1px solid #f3f3f3;
        }

        .pos-payment-box label {
            display: block;
            margin-bottom: 6px;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 500;
        }

        .pos-pay-field {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 36px;
            padding: 0 12px;
            border: 1px solid #ededed;
            border-radius: 10px;
            background: #ffffff;
        }

        .pos-pay-field span {
            color: #6b7280;
            font-size: 12px;
            font-weight: 500;
        }

        .pos-pay-field input {
            width: 100%;
            border: 0;
            background: transparent;
            box-shadow: none;
            color: #111827;
            font-size: 13px;
            padding: 0;
        }

        .pos-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 6px;
        }

        .pos-summary-row span:first-child {
            color: var(--text-muted);
            font-size: 12px;
        }

        .pos-summary-row span:last-child {
            color: #111827;
            font-size: 12px;
        }

        .pos-summary-divider {
            margin: 12px 0;
            border: 0;
            border-top: 1px solid var(--border);
            opacity: 1;
        }

        .pos-summary-row.is-total span {
            color: #111827;
            font-size: 14px;
            font-weight: 700;
        }

        .pos-print-btn {
            width: 100%;
            min-height: 46px;
            border: 1px solid var(--cha-orange);
            border-radius: 12px;
            background: var(--cha-orange);
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            transition: background-color 160ms ease, border-color 160ms ease;
        }

        .pos-print-btn:hover,
        .pos-print-btn:focus {
            background: #c85508;
            border-color: #c85508;
            color: #ffffff;
        }

        .pos-print-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .pos-mobile-toggle,
        .pos-mobile-close,
        .pos-backdrop {
            display: none;
        }

        @media (max-width: 1199.98px) {
            .pos-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .pos-shell {
                grid-template-columns: 1fr;
            }

            .pos-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .pos-order {
                position: fixed;
                top: 56px;
                right: 0;
                z-index: 1040;
                width: min(280px, calc(100vw - 72px));
                height: calc(100vh - 56px);
                transform: translateX(108%);
                transition: transform 180ms ease;
            }

            .pos-root.cart-open .pos-order {
                transform: translateX(0);
            }

            .pos-mobile-close {
                display: inline-flex;
                width: 28px;
                height: 28px;
                border: 0;
                border-radius: 8px;
                background: #f3f4f6;
                color: #6b7280;
                align-items: center;
                justify-content: center;
                padding: 0;
                margin-left: auto;
                margin-bottom: 8px;
            }

            .pos-mobile-toggle {
                position: fixed;
                right: 16px;
                bottom: 16px;
                z-index: 1038;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                min-height: 42px;
                padding: 0 14px;
                border: 1px solid var(--cha-orange);
                border-radius: 999px;
                background: var(--cha-orange);
                color: #ffffff;
                font-size: 12px;
                font-weight: 600;
            }

            .pos-backdrop {
                position: fixed;
                inset: 56px 0 0 60px;
                z-index: 1037;
                background: rgba(17, 24, 39, 0.24);
            }

            .pos-root.cart-open .pos-backdrop {
                display: block;
            }
        }

        @media (max-width: 767.98px) {
            .pos-topbar {
                flex-direction: column;
                align-items: stretch;
                padding: 12px;
            }

            .pos-stock-warning {
                white-space: normal;
            }

            .pos-category-row,
            .pos-grid {
                padding-left: 12px;
                padding-right: 12px;
            }

            .pos-grid {
                grid-template-columns: 1fr;
                padding-top: 16px;
            }
        }
    </style>

    <div class="pos-shell">
        <section class="pos-products">
            <div class="pos-topbar">
                <label class="pos-search" aria-label="Cari produk">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search menu, drink, or snack">
                </label>

                <div class="pos-stock-warning">
                    <i class="bi bi-info-circle"></i>
                    <span>{{ $lowStockCount }} produk stok menipis</span>
                </div>
            </div>

            <div class="pos-category-row">
                <button type="button" class="pos-pill {{ $selected_category === '' ? 'is-active' : '' }}" wire:click="filterByCategory('')">
                    <span>Semua</span>
                </button>

                @foreach ($categories as $categoryId => $categoryName)
                    @php
                        $categoryNameLower = \Illuminate\Support\Str::lower($categoryName);
                        $pillDotClass = str_contains($categoryNameLower, 'matcha')
                            ? 'is-matcha'
                            : (str_contains($categoryNameLower, 'thai') ? 'is-thai' : '');
                    @endphp

                    <button type="button" class="pos-pill {{ (string) $selected_category === (string) $categoryId ? 'is-active' : '' }}" wire:click="filterByCategory('{{ $categoryId }}')">
                        @if ($pillDotClass)
                            <span class="pos-pill__dot {{ $pillDotClass }}"></span>
                        @endif
                        <span>{{ $categoryName }}</span>
                    </button>
                @endforeach
            </div>

            <div class="pos-grid">
                @foreach ($visibleProducts as $product)
                    @php
                        $productNameLower = \Illuminate\Support\Str::lower($product->product_name);
                        $tintClass = str_contains($productNameLower, 'matcha')
                            ? 'is-matcha'
                            : (str_contains($productNameLower, 'thai') ? 'is-thai' : '');
                        $isLowStock = $product->recipes->contains(function ($recipe) {
                            return $recipe->rawMaterial && (float) $recipe->rawMaterial->stock <= (float) $recipe->rawMaterial->minimum_stock;
                        });
                        $canMake = $product->recipes->isNotEmpty()
                            ? $product->recipes->map(function ($recipe) {
                                if (!$recipe->rawMaterial || (float) $recipe->quantity_required <= 0) {
                                    return 0;
                                }

                                return floor((float) $recipe->rawMaterial->stock / (float) $recipe->quantity_required);
                            })->min()
                            : 0;
                        $isSelected = in_array((string) $product->product_id, $selectedProductIds, true);
                    @endphp

                    <article class="pos-card {{ $isSelected ? 'is-selected' : '' }}" wire:key="product-card-{{ $product->id }}">
                        <div class="pos-card__media">
                            <div class="pos-card__ring {{ $tintClass }}">
                                @php
                                    $storageImageExists = !empty($product->product_image) && \Illuminate\Support\Facades\Storage::disk('public')->exists('assets/product/' . $product->product_image);
                                    $publicImageExists = !empty($product->product_image) && file_exists(public_path('assets/product/' . $product->product_image));
                                @endphp
                                @if ($storageImageExists || $publicImageExists)
                                    <img src="{{ $storageImageExists ? asset('storage/assets/product/' . $product->product_image) : asset('assets/product/' . $product->product_image) }}" alt="{{ $product->product_name }}">
                                @else
                                    <div class="pos-card__placeholder">
                                        <i class="bi bi-cup-straw fs-4"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="pos-card__name">{{ $product->product_name }}</div>
                        <div class="pos-card__price">Rp{{ number_format($product->product_price) }}</div>

                        @if ($isLowStock)
                            <div class="pos-card__stock is-low">
                                <i class="bi bi-exclamation-triangle"></i>
                                Bahan menipis
                            </div>
                        @else
                            <div class="pos-card__stock">
                                Bisa dibuat ± {{ number_format($canMake) }} cup
                            </div>
                        @endif

                        <div class="pos-card__action">
                            <button wire:click.prevent="addToCart({{ $product->id }})" wire:loading.attr="disabled" type="button" class="pos-add-btn">
                                <span wire:loading.remove>Tambah Item</span>
                                <span wire:loading>Loading...</span>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside id="selling-product-cart-panel" class="pos-order">
            <button type="button" class="pos-mobile-close" data-pos-cart-toggle>
                <i class="bi bi-x-lg"></i>
            </button>

            <div class="pos-order__invoice">{{ $firstCart->id ?? 'INV-XXXXXX' }}</div>
            <div class="pos-order__subtitle">{{ $cartItemCount }} item dalam pesanan</div>

            <div class="pos-payment-tabs">
                <label class="pos-payment-tab">
                    <input type="radio" wire:model.live="payment_method" value="1">
                    <span>Cash</span>
                </label>
                <label class="pos-payment-tab">
                    <input type="radio" wire:model.live="payment_method" value="2">
                    <span>Transfer</span>
                </label>
                <label class="pos-payment-tab">
                    <input type="radio" wire:model.live="payment_method" value="3">
                    <span>QRIS</span>
                </label>
            </div>

            <div class="pos-order-list">
                @if (!$cartIsEmpty)
                    @foreach ($carts as $cart)
                        @php
                            $cartProduct = $productMap->get($cart->product_id);
                            $cartNameLower = \Illuminate\Support\Str::lower($cart->product_name);
                            $cartTintClass = str_contains($cartNameLower, 'matcha')
                                ? 'is-matcha'
                                : (str_contains($cartNameLower, 'thai') ? 'is-thai' : '');
                        @endphp

                        <div class="pos-order-item" wire:key="cart-item-{{ $cart->id }}">
                            <div class="pos-order-item__thumb {{ $cartTintClass }}">
                                @php
                                    $cartStorageImageExists = $cartProduct && !empty($cartProduct->product_image) && \Illuminate\Support\Facades\Storage::disk('public')->exists('assets/product/' . $cartProduct->product_image);
                                    $cartPublicImageExists = $cartProduct && !empty($cartProduct->product_image) && file_exists(public_path('assets/product/' . $cartProduct->product_image));
                                @endphp
                                @if ($cartStorageImageExists || $cartPublicImageExists)
                                    <img src="{{ $cartStorageImageExists ? asset('storage/assets/product/' . $cartProduct->product_image) : asset('assets/product/' . $cartProduct->product_image) }}" alt="{{ $cart->product_name }}">
                                @else
                                    <div class="pos-order__placeholder">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="pos-order-item__info">
                                <div class="pos-order-item__name">{{ $cart->product_name }}</div>
                                <div class="pos-order-item__note">Rp{{ number_format($cart->product_price) }} per item</div>

                                <div class="pos-order-item__controls">
                                    <button wire:click.prevent="decrementQuantity({{ $cart->id }})" wire:loading.attr="disabled" type="button" class="pos-qty-btn">-</button>
                                    <input
                                        type="number"
                                        min="1"
                                        class="form-control form-control-sm pos-qty-input"
                                        wire:model.lazy="quantities.{{ $cart->id }}"
                                        wire:keydown.enter="updateQuantityManual({{ $cart->id }})"
                                    >
                                    <button wire:click.prevent="incrementQuantity({{ $cart->id }})" wire:loading.attr="disabled" type="button" class="pos-qty-btn">+</button>
                                    <button wire:click.prevent="removeFromCart({{ $cart->id }})" wire:loading.attr="disabled" type="button" class="pos-remove-btn">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="pos-order-item__price">Rp{{ number_format($cart->product_price * $cart->quantity) }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="pos-order-empty">
                        <i class="bi bi-cart3"></i>
                        <span>Belum ada item pada pesanan aktif.</span>
                    </div>
                @endif
            </div>

            <div class="pos-order-footer">
                @if (!$cartIsEmpty)
                    <div class="pos-payment-box">
                        <label>Jumlah Bayar</label>
                        <div class="pos-pay-field">
                            <span>Rp</span>
                            <input type="number" wire:model.live="pay" min="0" placeholder="0">
                        </div>

                        <div class="pos-summary-row">
                            <span>Kembalian</span>
                            <span>Rp{{ number_format($change) }}</span>
                        </div>
                    </div>
                @endif

                <div class="pos-summary-row">
                    <span>Subtotal</span>
                    <span>Rp{{ number_format($total) }}</span>
                </div>
                <div class="pos-summary-row">
                    <span>Tax</span>
                    <span>Rp{{ number_format($displayTax) }}</span>
                </div>

                <hr class="pos-summary-divider">

                <div class="pos-summary-row is-total">
                    <span>Total</span>
                    <span>Rp{{ number_format($total + $displayTax) }}</span>
                </div>

                <div class="mt-3">
                    <button
                        class="pos-print-btn"
                        wire:click="sellProduct()"
                        @if($cartIsEmpty || $pay < $total || empty($payment_method)) disabled @endif
                    >
                        Print Bill
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <button type="button" class="pos-mobile-toggle" data-pos-cart-toggle>
        <i class="bi bi-receipt"></i>
        <span>{{ $cartItemCount }} item</span>
    </button>

    <div class="pos-backdrop" data-pos-cart-toggle></div>
</div>

<script>
document.addEventListener('click', function (event) {
    const toggle = event.target.closest('[data-pos-cart-toggle]');

    if (!toggle) {
        return;
    }

    const root = document.getElementById('selling-product-root');

    if (!root) {
        return;
    }

    root.classList.toggle('cart-open');
});

document.addEventListener('livewire:init', function () {
    Livewire.on('print-receipt', function (data) {
        const printWindow = window.open('/print-receipt/' + data[0].sale_id, '_blank', 'width=400,height=600,scrollbars=yes');

        printWindow.onload = function() {
            setTimeout(function() {
                printWindow.print();
            }, 500);
        };
    });
});
</script>
