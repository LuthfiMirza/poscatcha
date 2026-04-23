@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Edit Supplier</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
      <li class="breadcrumb-item active">Edit Supplier</li>
    </ol>
  </nav>
</div>

<section class="section">
  @include('admin.partials.flash')

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Form Edit Supplier</h5>

          <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3">
              <label for="name" class="col-sm-2 col-form-label">Nama</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
              </div>
            </div>

            <div class="row mb-3">
              <label for="phone" class="col-sm-2 col-form-label">Telepon</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}">
              </div>
            </div>

            <div class="row mb-3">
              <label for="address" class="col-sm-2 col-form-label">Alamat</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="address" name="address" rows="4">{{ old('address', $supplier->address) }}</textarea>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">Update Supplier</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
