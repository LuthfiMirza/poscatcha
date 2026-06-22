@extends('layouts.cashier')

@section('page-title', 'List Product')

@section('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #ffffff;
      font-family: "Inter", sans-serif;
    }

    #main.main {
      margin-top: 56px !important;
      margin-left: 60px !important;
      padding: 0 !important;
      min-height: calc(100vh - 56px);
      background: #ffffff;
    }

    .cashier-list-page {
      min-height: calc(100vh - 56px);
      padding: 24px 32px;
      background: #ffffff;
    }

    .cashier-list-heading__title {
      margin: 0;
      color: #111827;
      font-size: 20px;
      font-weight: 600;
      line-height: 1.2;
    }

    .cashier-list-heading__breadcrumb {
      margin-top: 4px;
      color: #9ca3af;
      font-size: 12px;
      line-height: 1.4;
    }

    .cashier-list-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
      margin-top: 24px;
    }

    .cashier-list-card {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 20px;
      border: 1px solid #f0f0f0;
      border-radius: 16px;
      background: #ffffff;
      transition: border-color 150ms ease;
    }

    .cashier-list-card:hover {
      border-color: #e8650a;
    }

    .cashier-list-card__media {
      width: 72px;
      height: 72px;
      border-radius: 999px;
      overflow: hidden;
      flex: 0 0 72px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
    }

    .cashier-list-card__media.is-matcha {
      background: #eef6e1;
    }

    .cashier-list-card__media.is-thai {
      background: #fef0e6;
    }

    .cashier-list-card__image {
      width: 72px;
      height: 72px;
      object-fit: cover;
      border-radius: 999px;
    }

    .cashier-list-card__placeholder {
      color: #d1d5db;
      font-size: 28px;
    }

    .cashier-list-card__body {
      min-width: 0;
      flex: 1 1 auto;
    }

    .cashier-list-card__meta {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .cashier-list-card__name {
      color: #111827;
      font-size: 14px;
      font-weight: 600;
      line-height: 1.3;
    }

    .cashier-list-card__badge {
      display: inline-flex;
      align-items: center;
      min-height: 21px;
      padding: 0 8px;
      border: 1px solid #fed7aa;
      border-radius: 999px;
      background: #fff7ed;
      color: #e8650a;
      font-size: 10px;
      font-weight: 500;
      line-height: 1;
    }

    .cashier-list-card__price {
      margin-top: 6px;
      color: #e8650a;
      font-size: 22px;
      font-weight: 700;
      line-height: 1.1;
    }

    .cashier-list-card__detail {
      margin-top: 6px;
      color: #6b7280;
      font-size: 12px;
      line-height: 1.6;
    }

    .cashier-list-card__detail span {
      display: block;
    }

    .cashier-list-card__detail .is-low-stock {
      color: #e8650a;
      font-weight: 500;
    }

    .cashier-list-empty {
      padding: 80px 20px;
      text-align: center;
      color: #9ca3af;
    }

    .cashier-list-empty__icon {
      margin-bottom: 12px;
      color: #e5e7eb;
      font-size: 48px;
      line-height: 1;
    }

    .cashier-list-empty__text {
      font-size: 13px;
    }

    .cashier-list-footer {
      margin-top: 32px;
      padding-top: 16px;
      border-top: 1px solid #f0f0f0;
      color: #d1d5db;
      font-size: 11px;
      text-align: center;
    }

    @media (max-width: 1199.98px) {
      .cashier-list-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 991.98px) {
      .cashier-list-page {
        padding: 20px 16px;
      }
    }

    @media (max-width: 767.98px) {
      .cashier-list-page {
        padding: 16px 12px;
      }

      .cashier-list-grid {
        grid-template-columns: 1fr;
      }

      .cashier-list-card {
        padding: 16px;
      }
    }
  </style>
@endsection

@section('content')
<div class="cashier-list-page">
  <div class="cashier-list-heading">
    <h1 class="cashier-list-heading__title">List product</h1>
    <div class="cashier-list-heading__breadcrumb">CATcha POS / List product</div>
  </div>

  @if (count($products) > 0)
    <div class="cashier-list-grid">
      @foreach ($products as $index => $product)
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
        @endphp

        <div class="cashier-list-card">
          <div class="cashier-list-card__media {{ $tintClass }}">
            @php
              $storageImageExists = !empty($product->product_image) && \Illuminate\Support\Facades\Storage::disk('public')->exists('assets/product/' . $product->product_image);
              $publicImageExists = !empty($product->product_image) && file_exists(public_path('assets/product/' . $product->product_image));
            @endphp
            @if ($storageImageExists || $publicImageExists)
              <img src="{{ $storageImageExists ? asset('storage/assets/product/'.$product->product_image) : asset('assets/product/'.$product->product_image) }}" alt="{{ $product->product_name }}" class="cashier-list-card__image">
            @else
              <div class="cashier-list-card__placeholder">
                <i class="bi bi-box-seam"></i>
              </div>
            @endif
          </div>

          <div class="cashier-list-card__body">
            <div class="cashier-list-card__meta">
              <div class="cashier-list-card__name">{{ $product->product_name }}</div>
              @foreach ($categories as $category)
                @if ($category->category_id == $product->product_category)
                  <span class="cashier-list-card__badge">{{ $category->category_name}}</span>
                @endif
              @endforeach
            </div>

            <div class="cashier-list-card__price">Rp{{ number_format($product->product_price) }}</div>

            <div class="cashier-list-card__detail">
              <span>Product ID : {{$product->product_id }}</span>
              @if ($isLowStock)
                <span class="is-low-stock">Bahan menipis ⚠</span>
              @else
                <span>Bisa dibuat ± {{ number_format($canMake) }} cup</span>
              @endif
              <span>Expired: {{ date('d F Y', strtotime($product->product_expired)) }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="cashier-list-empty">
      <div class="cashier-list-empty__icon">
        <i class="bi bi-box-seam"></i>
      </div>
      <div class="cashier-list-empty__text">Belum ada produk tersedia</div>
    </div>
  @endif

  <div class="cashier-list-footer">© CATcha POS · {{ date('Y') }}</div>
</div>
@endsection
