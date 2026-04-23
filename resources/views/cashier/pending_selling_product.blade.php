@extends('layouts.cashier')

@section('content')
<div class="pagetitle">
  <h1>Pending Selling Product</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('list_product')}}">Home</a></li>
      <li class="breadcrumb-item active">Pending Selling Product</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <div class="row">
        <!-- Recent Sales -->
        <div class="col-12">
          <div class="card recent-sales overflow-auto">
            <div class="card-body">
              <h5 class="card-title">Pending Sales</h5>

              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Cart ID</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($pendings as $index => $pending)
                  <tr>
                    <th scope="row"><a href="#">{{ $index + 1}}</a></th>
                    <td>{{ $pending->cart_id}}</td>
                    <td>Rp{{ number_format($pending->amount) }}</td>
                    <td>{{ date('d F Y', strtotime($pending->created_at)) }}</td>
                    <td>
                      <a href="{{ route('detail_pending_selling_product', $pending->cart_id)}}" class="btn badge bg-primary">Detail</a>
                      <a href="{{ route('delete_pending_selling_product', $pending->id)}}" class="btn badge bg-danger">Delete</a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- End Recent Sales -->
      </div>
    </div><!-- End Left side columns -->
  </div>
</section>

</main><!-- End #main -->
@endsection
