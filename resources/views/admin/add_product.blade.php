@extends('layouts.admin')

@section('content')

<div class="pagetitle">
    <h1>Add Product</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard_admin')}}">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Add Product Form</h5>

            @if ($errors->any())
              <div class="alert alert-danger">
                <strong>Produk belum bisa disimpan.</strong>
                <ul class="mb-0 mt-2">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('add_product_process') }}" enctype="multipart/form-data">
              @csrf
              <div class="row mb-3">
                <label for="product_id" class="col-sm-2 col-form-label">Product ID</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="product_id" name="product_id" value="{{ old('product_id', $productId) }}" readonly>
                  <div class="form-text">Product ID dibuat otomatis saat produk disimpan supaya tidak duplikat.</div>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_name" class="col-sm-2 col-form-label">Product Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_category" class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                  <select class="form-select" aria-label="Default select example" id="product_category" name="product_category" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $index => $category)
                      <option value="{{ $category->category_id }}" {{ old('product_category') == $category->category_id ? 'selected' : ''}}>{{ $category->category_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <input type="hidden" id="buy_price" name="buy_price" value="0">
              <div class="row mb-3">
                <label for="product_price" class="col-sm-2 col-form-label">Sell Price</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" id="product_price" name="product_price" value="{{ old('product_price') }}" required min="1">
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_profit" class="col-sm-2 col-form-label">Product Profit</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" id="product_profit" name="product_profit" value="{{ old('product_profit', 0) }}" readonly>
                  <div class="form-text">Profit sementara. Setelah resep diisi, modal produk akan dihitung otomatis dari bahan baku.</div>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_image" class="col-sm-2 col-form-label">Product Image</label>
                <div class="col-sm-10">
                  <input class="form-control" type="file" id="product_image" name="product_image" value="{{ old('product_image') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_expired" class="col-sm-2 col-form-label">Expired</label>
                <div class="col-sm-10">
                  <input type="date" class="form-control" id="product_expired" name="product_expired" value="{{ old('product_expired') }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_quantity" class="col-sm-2 col-form-label">Quantity</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" id="product_quantity" name="product_quantity" value="{{ old('product_quantity') }}" required min="1">
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
              </div>

            </form><!-- End General Form Elements -->
        </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const buyPriceInput = document.getElementById('buy_price');
    const sellPriceInput = document.getElementById('product_price');
    const profitInput = document.getElementById('product_profit');

    const syncProfit = () => {
      const buyPrice = Number(buyPriceInput.value || 0);
      const sellPrice = Number(sellPriceInput.value || 0);
      profitInput.value = Math.max(sellPrice - buyPrice, 0);
    };

    buyPriceInput.addEventListener('input', syncProfit);
    sellPriceInput.addEventListener('input', syncProfit);
    syncProfit();
  });
</script>
@endsection
