@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Stock Movement</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin')}}">Home</a></li>
      <li class="breadcrumb-item active">Stock Movement</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Filter Stock Movement</h5>

      <form method="GET" action="{{ route('stock_movement') }}" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Source</label>
          <select name="source" class="form-select">
            <option value="">Semua Source</option>
            <option value="purchase" @selected(request('source') === 'purchase')>Restock</option>
            <option value="sale" @selected(request('source') === 'sale')>Penjualan</option>
            <option value="product" @selected(request('source') === 'product')>Produk / Penyesuaian</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Dari</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Sampai</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="{{ route('stock_movement') }}" class="btn btn-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
        <!-- Recent Sales -->
        <div class="col-12">
          <div class="card recent-sales overflow-auto">

            <div class="card-body">
              {{-- <h5 class="card-title">Stock Movement <span>| Today</span></h5> --}}
              <h5 class="card-title">Stock Movement</h5>

              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Product ID</th>
                    <th scope="col">Transaction ID</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Source</th>
                    <th scope="col">Status</th>
                    <th scope="col">Reason</th>
                    <th scope="col">Stock Before</th>
                    <th scope="col">Stock After</th>
                    <th scope="col">Action By</th>
                    <th scope="col">Action Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($stock_movements as $index => $stock_movement)                      
                  <tr>
                    <th scope="row">{{ $index + 1 }}</th>
                    <td>
                      @if ($stock_movement->product_id == null)
                        Product Deleted 
                      @else
                        {{ $stock_movement->product_id}}
                      @endif
                    </td>
                    <td>{{ $stock_movement->transaction_id}}</td>
                    <td>{{ $stock_movement->product_name}}</td>
                    <td>
                      @if ($stock_movement->source === 'purchase')
                        <span class="badge bg-success">{{ $stock_movement->type_label }}</span>
                      @elseif ($stock_movement->source === 'sale')
                        <span class="badge bg-danger">{{ $stock_movement->type_label }}</span>
                      @else
                        <span class="badge bg-warning text-dark">{{ $stock_movement->type_label }}</span>
                      @endif
                    </td>
                    <td>{{ $stock_movement->source_label }}</td>
                    <td>
                      @if ($stock_movement->status == 1)
                      <span class="badge bg-success">Add Product</span>
                      @elseif ($stock_movement->status == 2)
                      <span class="badge bg-warning">Edit Product</span>
                      @elseif ($stock_movement->status == 3)
                      <span class="badge bg-danger">Delete Product</span>
                      @elseif ($stock_movement->status == 5)
                      <span class="badge bg-success">Restock</span>
                      @else
                      <span class="badge bg-info">Sell Product</span>
                      @endif
                    </td>
                    <td>{{ $stock_movement->reason}}</td>
                    <td>{{ $stock_movement->quantity_before}}</td>
                    <td>{{ $stock_movement->quantity_after}}</td>
                    <td>{{ $stock_movement->action_by}}</td>
                    <td>{{ $stock_movement->created_at}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- End Recent Sales -->
  </div>
</section>

</main><!-- End #main -->
@endsection
