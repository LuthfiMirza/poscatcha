@extends('layouts.cashier')

@section('content')
<div class="pagetitle">
  <h1>Pending Selling Product</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('selling_product')}}">Home</a></li>
      <li class="breadcrumb-item active">Pending Selling Product</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <livewire:detail-pending-selling-product :cart_id="$cart_id" />
</section>

</main><!-- End #main -->
@endsection
