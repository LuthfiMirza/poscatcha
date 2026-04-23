@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Sales Data</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin')}}">Home</a></li>
      <li class="breadcrumb-item active">Sales Data</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <!-- Recent Sales -->
        <div class="col-12">
          <div class="card recent-sales overflow-auto">
            <div class="card-body">
              <h5 class="card-title">Sales Data</h5>

              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Invoice</th>
                    <th scope="col">Total</th>
                    <th scope="col">Payment Method</th>
                    <th scope="col">Action By</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sales as $index => $sale)
                    <tr>
                        <th scope="row"><a href="#">{{ $index + 1 }}</a></th>
                        <td>{{ $sale->sale_id }}</td>
                        <td>Rp{{ number_format($sale->total, 2, ',', '.') }}</td>
                        <td>
                          @if ($sale->payment_method == 1) Cash 
                          @elseif ($sale->payment_method == 2) Transfer 
                          @elseif ($sale->payment_method == 3) QRIS 
                          @endif
                        </td>
                        <td>
                          @foreach ($users as $user)
                            @if ($user->id == $sale->cashier_id)
                              {{ $user->name }}
                            @endif
                          @endforeach
                        </td>
                        <td>{{ date('d F Y', strtotime($sale->created_at)) }}</td>
                        <td>
                          <a href="{{ route('detail_sales_data', $sale->sale_id)}}" class="btn badge bg-primary">Details</a>
                        </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- End Recent Sales -->
    </div>
</section>
@endsection
