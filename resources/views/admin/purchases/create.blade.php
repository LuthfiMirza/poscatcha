@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Tambah Purchase</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
      <li class="breadcrumb-item active">Tambah Purchase</li>
    </ol>
  </nav>
</div>

<section class="section">
  @include('admin.partials.flash')
  @include('admin.purchases._form')
</section>
@endsection
