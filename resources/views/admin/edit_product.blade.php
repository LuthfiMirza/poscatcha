@extends('layouts.admin')

@section('content')

<div class="pagetitle">
    <h1>Edit Product</h1>
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
            <h5 class="card-title">Edit Product Form</h5>

            <!-- General Form Elements -->
            <form method="POST" action="{{ route('edit_product_process', $product->id)}}" enctype="multipart/form-data">
              @csrf
              <div class="row mb-3">
                <label for="product_id" class="col-sm-2 col-form-label">Product ID</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="product_id" name="product_id" value="{{ $product->product_id }}" disabled>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_name" class="col-sm-2 col-form-label">Product Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="product_name" name="product_name" value="{{ $product->product_name }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_category" class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                  <select class="form-select" aria-label="Default select example" id="product_category" name="product_category" required>
                    <option selected>Select Category</option>
                    @foreach ($categories as $index => $category)
                      <option value="{{ $category->category_id }}" {{ $product->product_category == $category->category_id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_price" class="col-sm-2 col-form-label">Price</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" id="product_price" name="product_price" value="{{ $product->product_price }}" required min="1">
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_profit" class="col-sm-2 col-form-label">Product Profit</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" id="product_profit" name="product_profit" value="{{ $product->product_profit }}" required min="1">
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_image" class="col-sm-2 col-form-label">Product Image</label>
                <div class="col-sm-10">
                  <div class="image-preview" id="imagePreview">
                    <span><img src="{{ asset('storage/assets/product/' . $product->product_image) }}" alt="No Image"></span>
                  </div>
                  <input class="form-control" type="file" id="product_image" name="product_image">
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_quantity" class="col-sm-2 col-form-label">Quantity</label>
                <div class="col-sm-10">
                  <input type="number" class="form-control" id="product_quantity" name="product_quantity" value="{{ $product->product_quantity }}" required min="1">
                </div>
              </div>
              <div class="row mb-3">
                <label for="product_expired" class="col-sm-2 col-form-label">Expired</label>
                <div class="col-sm-10">
                  <input type="date" class="form-control" id="product_expired" name="product_expired" value="{{ $product->product_expired }}" required>
                </div>
              </div>
              <div class="row mb-3">
                <label for="reason" class="col-sm-2 col-form-label">Reason</label>
                <div class="col-sm-10">
                  <select class="form-select" aria-label="Default select example" id="reason" name="reason" required min:1 max:4>
                    <option selected value="null">Select Reason</option>
                    <option value="1">Add Stock</option>
                    <option value="2">Wrong Input</option>
                    <option value="3">Product Is Lost</option>
                    <option value="4">Product Is Damaged</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-sm-10">
                  <button type="submit" class="btn btn-primary">Edit Product</button>
                </div>
              </div>

            </form><!-- End General Form Elements -->
        </div>
    </div>
  </div>
@endsection