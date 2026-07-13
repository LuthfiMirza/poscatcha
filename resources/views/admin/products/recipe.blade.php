@extends('layouts.admin')

@section('content')
@php
  $formatQuantity = fn ($value) => $value === null || $value === '' ? '' : rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
@endphp
<div class="pagetitle">
  <h1>Resep Produk</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard_admin') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produk</a></li>
      <li class="breadcrumb-item active">Resep</li>
    </ol>
  </nav>
</div>

@include('admin.partials.flash')

<section class="section dashboard">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">{{ $product->product_name }}</h5>
      <p class="text-muted">Isi bahan yang dibutuhkan untuk membuat 1 produk. Contoh: cup 1 pcs, susu 200 ml, bubuk matcha 20 gram.</p>

      <form method="POST" action="{{ route('admin.products.recipe.update', $product) }}">
        @csrf
        <table class="table align-middle" id="recipe-table">
          <thead><tr><th>Bahan Baku</th><th>Qty per Produk</th><th></th></tr></thead>
          <tbody>
            @php $rows = old('recipes', $product->recipes->map(fn ($recipe) => ['raw_material_id' => $recipe->raw_material_id, 'quantity_required' => $recipe->quantity_required])->toArray()); @endphp
            @forelse ($rows as $index => $row)
              <tr>
                <td>
                  <select name="recipes[{{ $index }}][raw_material_id]" class="form-select">
                    <option value="">Pilih bahan</option>
                    @foreach ($materials as $material)
                      <option value="{{ $material->id }}" @selected(($row['raw_material_id'] ?? '') == $material->id)>{{ $material->name }} ({{ $material->unit }})</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" step="0.01" min="0.01" name="recipes[{{ $index }}][quantity_required]" class="form-control" value="{{ $formatQuantity($row['quantity_required'] ?? '') }}"></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button></td>
              </tr>
            @empty
              <tr>
                <td>
                  <select name="recipes[0][raw_material_id]" class="form-select">
                    <option value="">Pilih bahan</option>
                    @foreach ($materials as $material)
                      <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" step="0.01" min="0.01" name="recipes[0][quantity_required]" class="form-control"></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button></td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <button type="button" class="btn btn-outline-secondary" id="add-row">Tambah Bahan</button>
        <button type="submit" class="btn btn-success">Simpan Resep</button>
      </form>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  let rowIndex = document.querySelectorAll('#recipe-table tbody tr').length;
  const options = `@foreach ($materials as $material)<option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>@endforeach`;

  document.getElementById('add-row').addEventListener('click', function () {
    const tbody = document.querySelector('#recipe-table tbody');
    tbody.insertAdjacentHTML('beforeend', `<tr><td><select name="recipes[${rowIndex}][raw_material_id]" class="form-select"><option value="">Pilih bahan</option>${options}</select></td><td><input type="number" step="0.01" min="0.01" name="recipes[${rowIndex}][quantity_required]" class="form-control"></td><td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button></td></tr>`);
    rowIndex++;
  });

  document.addEventListener('click', function (event) {
    if (event.target.classList.contains('remove-row')) {
      event.target.closest('tr').remove();
    }
  });
</script>
@endsection
