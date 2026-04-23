@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Produk</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Produk</li>
    </ol>
  </nav>
</div>

@include('admin.partials.flash')

<section class="section dashboard">
  <div class="row">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">Master Produk</h5>
            <a href="{{ route('add_product') }}" class="btn btn-success">
              <i class="bx bx-list-plus me-1"></i> Add Product
            </a>
          </div>

          <table class="table table-borderless datatable align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>Product ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Buy Price</th>
                <th>Sell Price</th>
                <th>Profit</th>
                <th>Stock</th>
                <th>Expired</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($products as $index => $product)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $product->product_id }}</td>
                  <td>{{ $product->product_name }}</td>
                  <td>{{ $categoriesById[$product->product_category] ?? '-' }}</td>
                  <td>Rp{{ number_format((float) $product->buy_price, 2, ',', '.') }}</td>
                  <td>Rp{{ number_format($product->product_price, 2, ',', '.') }}</td>
                  <td>Rp{{ number_format($product->product_profit, 2, ',', '.') }}</td>
                  <td>
                    @if ($product->product_quantity <= 5)
                      <span class="badge bg-warning text-dark">{{ $product->product_quantity }}</span>
                    @else
                      <span class="badge bg-success">{{ $product->product_quantity }}</span>
                    @endif
                  </td>
                  <td>
                    @if ($product->product_expired)
                      {{ \Illuminate\Support\Carbon::parse($product->product_expired)->format('d M Y') }}
                    @else
                      -
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('edit_product', $product->id) }}" class="btn badge bg-warning">Edit</a>
                    <a href="{{ route('delete_product', $product->id) }}" class="btn badge bg-danger">Delete</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
