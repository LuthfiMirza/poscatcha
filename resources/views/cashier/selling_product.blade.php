@extends('layouts.cashier')

@section('content')
<div class="pagetitle">
  <h1>Selling Product</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('list_product')}}">Home</a></li>
      <li class="breadcrumb-item active">Selling Product</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
  <livewire:selling-product/>
</section>

</main><!-- End #main -->
@endsection


