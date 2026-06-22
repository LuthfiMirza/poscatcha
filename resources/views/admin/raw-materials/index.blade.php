@extends('layouts.admin')

@section('content')
<div class="pagetitle">
  <h1>Bahan Baku</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item active">Bahan Baku</li>
    </ol>
  </nav>
</div>

@include('admin.partials.flash')

<section class="section dashboard">
  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Tambah Bahan</h5>
          <form method="POST" action="{{ route('raw-materials.store') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Nama Bahan</label>
              <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Satuan</label>
              <select name="unit" class="form-select" required>
                @foreach (['pcs', 'ml', 'liter', 'gram', 'kg'] as $unit)
                  <option value="{{ $unit }}" @selected(old('unit') === $unit)>{{ $unit }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Stok Awal</label>
              <input type="number" step="0.01" min="0" name="stock" class="form-control" value="{{ old('stock', 0) }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Stok Minimum</label>
              <input type="number" step="0.01" min="0" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', 0) }}" required>
            </div>
            <button class="btn btn-success" type="submit">Simpan Bahan</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Stok Bahan Baku</h5>
          <table class="table table-borderless datatable align-middle">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Stok</th>
                <th>Minimum</th>
                <th>Update</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($materials as $material)
                <tr>
                  <form method="POST" action="{{ route('raw-materials.update', $material) }}">
                    @csrf
                    @method('PUT')
                    <td><input type="text" name="name" class="form-control" value="{{ $material->name }}" required></td>
                    <td>
                      <div class="input-group">
                        <input type="number" step="0.01" min="0" name="stock" class="form-control" value="{{ $material->stock }}" required>
                        <span class="input-group-text">{{ $material->unit }}</span>
                      </div>
                      @if ($material->stock <= $material->minimum_stock)
                        <span class="badge bg-warning text-dark mt-1">Stok rendah</span>
                      @endif
                    </td>
                    <td><input type="number" step="0.01" min="0" name="minimum_stock" class="form-control" value="{{ $material->minimum_stock }}" required></td>
                    <td>
                      <input type="hidden" name="unit" value="{{ $material->unit }}">
                      <input type="text" name="reason" class="form-control" value="Restock/Koreksi" required>
                    </td>
                    <td class="d-flex gap-1">
                      <button class="btn badge bg-primary" type="submit">Save</button>
                  </form>
                  <form method="POST" action="{{ route('raw-materials.destroy', $material) }}" onsubmit="return confirm('Hapus bahan ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn badge bg-danger" type="submit">Delete</button>
                  </form>
                    </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Riwayat Stok Bahan Terbaru</h5>
          <table class="table table-sm align-middle">
            <thead><tr><th>Waktu</th><th>Bahan</th><th>Tipe</th><th>Qty</th><th>Stok</th><th>Alasan</th></tr></thead>
            <tbody>
              @foreach ($movements as $movement)
                <tr>
                  <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                  <td>{{ $movement->rawMaterial->name }}</td>
                  <td>{{ strtoupper($movement->type) }}</td>
                  <td>{{ number_format((float) $movement->quantity, 2, ',', '.') }} {{ $movement->rawMaterial->unit }}</td>
                  <td>{{ number_format((float) $movement->quantity_before, 2, ',', '.') }} → {{ number_format((float) $movement->quantity_after, 2, ',', '.') }}</td>
                  <td>{{ $movement->reason }}</td>
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
