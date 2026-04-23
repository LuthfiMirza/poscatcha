@extends('layouts.cashier')

@section('content')
<div class="pagetitle">
  <h1>List Product</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('list_product')}}">Home</a></li>
      <li class="breadcrumb-item active">List Product</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">

    @foreach ($products as $index => $product)
      <!-- Sales Card -->
      <div class="col-xxl-4 col-md-4">
        <div class="card info-card sales-card">
          <div class="card-body">
            <h5 class="card-title">{{ $product->product_name }} 
              <span>|
                @foreach ($categories as $category)
                  @if ($category->category_id == $product->product_category)
                    {{ $category->category_name}}
                  @endif
                @endforeach
              </span>
            </h5>

            <div class="d-flex align-items-center">
              <img src="{{ asset('storage/assets/product/'.$product->product_image)}}" alt="" class="card-icon rounded-circle d-flex align-items-center justify-content-center">
              {{-- <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-cart"></i>
              </div> --}}
              <div class="ps-3 card-description">
                <h6 class="text-success small pt-1 fw-bold">Rp{{ number_format($product->product_price) }}</h6>
                <span class="small pt-1 fw-bold">Product ID : {{$product->product_id }}</span>
                <span class="small pt-1 fw-bold">Stock : {{ number_format($product->product_quantity) }}</span>
                <span class="small pt-1 fw-bold">Expired: {{ date('d F Y', strtotime($product->product_expired)) }}</span>
                {{-- <span class="text-success small pt-1 fw-bold">Rp{{ number_format($product->product_price) }}</span> <span class="text-muted small pt-2 ps-1">increase</span> --}}
              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- End Sales Card -->
    @endforeach

      {{-- <!-- Sales Card -->
      <div class="col-xxl-3 col-md-3">
        <div class="card info-card sales-card">

          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body">
            <h5 class="card-title">Sales <span>| Today</span></h5>

            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-cart"></i>
              </div>
              <div class="ps-3">
                <h6>145</h6>
                <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>

              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- End Sales Card -->

      <div class="col-xxl-3 col-md-3">
        <div class="card info-card sales-card">

          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body">
            <h5 class="card-title">Sales <span>| Today</span></h5>

            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-cart"></i>
              </div>
              <div class="ps-3">
                <h6>145</h6>
                <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>

              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- End Sales Card -->

      <div class="col-xxl-3 col-md-3">
        <div class="card info-card sales-card">

          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body">
            <h5 class="card-title">Sales <span>| Today</span></h5>

            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-cart"></i>
              </div>
              <div class="ps-3">
                <h6>145</h6>
                <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>

              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- End Sales Card -->

      <div class="col-xxl-3 col-md-3">
        <div class="card info-card sales-card">

          <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>

              <li><a class="dropdown-item" href="#">Today</a></li>
              <li><a class="dropdown-item" href="#">This Month</a></li>
              <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
          </div>

          <div class="card-body">
            <h5 class="card-title">Sales <span>| Today</span></h5>

            <div class="d-flex align-items-center">
              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-cart"></i>
              </div>
              <div class="ps-3">
                <h6>145</h6>
                <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>

              </div>
            </div>
          </div>

        </div>
      </div>
      <!-- End Sales Card --> --}}
  </div>
</section>

</main><!-- End #main -->
@endsection
