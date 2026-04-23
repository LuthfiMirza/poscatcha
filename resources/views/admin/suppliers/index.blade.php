@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Suppliers</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Suppliers</li>
    </ol>
  </nav>
</div>

<section class="section dashboard">
  @include('admin.partials.flash')

  <div class="row">
    <div class="col-12">
      <div class="card recent-sales overflow-auto">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title">Data Supplier</h5>
            <a href="{{ route('suppliers.create') }}" class="btn btn-success btn-sm">
              <i class="bx bx-plus"></i> Tambah Supplier
            </a>
          </div>

          <table class="table table-borderless datatable">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th>Dibuat</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($suppliers as $index => $supplier)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $supplier->name }}</td>
                  <td>{{ $supplier->phone ?: '-' }}</td>
                  <td>{{ $supplier->address ?: '-' }}</td>
                  <td>{{ $supplier->created_at->format('d M Y H:i') }}</td>
                  <td class="d-flex gap-1">
                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-primary btn-sm">Detail</a>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus supplier ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
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
