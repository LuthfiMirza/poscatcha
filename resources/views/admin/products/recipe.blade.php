@extends('layouts.admin')

@section('content')
@php
  $formatQuantity = fn ($value) => $value === null || $value === '' ? '' : rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
  $materialCosts = $materialCosts ?? collect();
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
      <div class="alert alert-info">
        Setelah resep disimpan, modal produk otomatis dihitung dari harga restock terakhir tiap bahan. Kalau harga bahan belum ada, nilainya dihitung Rp0 sampai bahan pernah direstock.
      </div>
      <div class="alert alert-warning d-none" id="zero-cost-warning"></div>

      <form method="POST" action="{{ route('admin.products.recipe.update', $product) }}">
        @csrf
        <table class="table align-middle" id="recipe-table">
          <thead><tr><th>Bahan Baku</th><th>Qty per Produk</th><th>Harga / Satuan</th><th>Estimasi Biaya</th><th></th></tr></thead>
          <tbody>
            @php $rows = old('recipes', $product->recipes->map(fn ($recipe) => ['raw_material_id' => $recipe->raw_material_id, 'quantity_required' => $recipe->quantity_required])->toArray()); @endphp
            @forelse ($rows as $index => $row)
              <tr>
                <td>
                  <select name="recipes[{{ $index }}][raw_material_id]" class="form-select recipe-material">
                    <option value="">Pilih bahan</option>
                    @foreach ($materials as $material)
                      <option value="{{ $material->id }}" data-unit="{{ $material->unit }}" data-cost="{{ (float) ($materialCosts[$material->id] ?? 0) }}" @selected(($row['raw_material_id'] ?? '') == $material->id)>{{ $material->name }} ({{ $material->unit }})</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" step="0.01" min="0.01" name="recipes[{{ $index }}][quantity_required]" class="form-control recipe-quantity" value="{{ $formatQuantity($row['quantity_required'] ?? '') }}"></td>
                <td class="unit-cost">Rp0 / unit</td>
                <td class="row-cost">Rp0</td>
                <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button></td>
              </tr>
            @empty
              <tr>
                <td>
                  <select name="recipes[0][raw_material_id]" class="form-select recipe-material">
                    <option value="">Pilih bahan</option>
                    @foreach ($materials as $material)
                      <option value="{{ $material->id }}" data-unit="{{ $material->unit }}" data-cost="{{ (float) ($materialCosts[$material->id] ?? 0) }}">{{ $material->name }} ({{ $material->unit }})</option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" step="0.01" min="0.01" name="recipes[0][quantity_required]" class="form-control recipe-quantity"></td>
                <td class="unit-cost">Rp0 / unit</td>
                <td class="row-cost">Rp0</td>
                <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button></td>
              </tr>
            @endforelse
          </tbody>
        </table>
        <div class="mb-3 fw-bold">Estimasi modal produk: <span id="recipe-total-cost">Rp0</span></div>
        <button type="button" class="btn btn-outline-secondary" id="add-row">Tambah Bahan</button>
        <button type="submit" class="btn btn-success">Simpan Resep</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Lewati, isi nanti</a>
      </form>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  let rowIndex = document.querySelectorAll('#recipe-table tbody tr').length;
  const options = `@foreach ($materials as $material)<option value="{{ $material->id }}" data-unit="{{ $material->unit }}" data-cost="{{ (float) ($materialCosts[$material->id] ?? 0) }}">{{ $material->name }} ({{ $material->unit }})</option>@endforeach`;

  function formatRupiah(value) {
    return 'Rp' + Number(value || 0).toLocaleString('id-ID', { maximumFractionDigits: 2 });
  }

  function updateRecipeCosts() {
    let totalCost = 0;
    const zeroCostMaterials = [];

    document.querySelectorAll('#recipe-table tbody tr').forEach(function (row) {
      const materialSelect = row.querySelector('.recipe-material');
      const quantityInput = row.querySelector('.recipe-quantity');
      const selectedOption = materialSelect.options[materialSelect.selectedIndex];
      const unit = selectedOption?.dataset.unit || 'unit';
      const unitCost = parseFloat(selectedOption?.dataset.cost || 0);
      const quantity = parseFloat(quantityInput.value || 0);
      const rowCost = unitCost * quantity;

      row.querySelector('.unit-cost').textContent = `${formatRupiah(unitCost)} / ${unit}`;
      row.querySelector('.row-cost').textContent = formatRupiah(rowCost);
      totalCost += rowCost;

      if (materialSelect.value && unitCost === 0) {
        zeroCostMaterials.push(selectedOption.textContent.trim());
      }
    });

    document.getElementById('recipe-total-cost').textContent = formatRupiah(totalCost);

    const warningBox = document.getElementById('zero-cost-warning');
    if (zeroCostMaterials.length > 0) {
      warningBox.textContent = `Bahan berikut belum pernah direstock sehingga harganya dihitung Rp0: ${zeroCostMaterials.join(', ')}. Modal produk akan tidak akurat sampai bahan ini direstock lewat menu Restock.`;
      warningBox.classList.remove('d-none');
    } else {
      warningBox.classList.add('d-none');
    }
  }

  document.getElementById('add-row').addEventListener('click', function () {
    const tbody = document.querySelector('#recipe-table tbody');
    tbody.insertAdjacentHTML('beforeend', `<tr><td><select name="recipes[${rowIndex}][raw_material_id]" class="form-select recipe-material"><option value="">Pilih bahan</option>${options}</select></td><td><input type="number" step="0.01" min="0.01" name="recipes[${rowIndex}][quantity_required]" class="form-control recipe-quantity"></td><td class="unit-cost">Rp0 / unit</td><td class="row-cost">Rp0</td><td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button></td></tr>`);
    rowIndex++;
    updateRecipeCosts();
  });

  document.addEventListener('input', function (event) {
    if (event.target.classList.contains('recipe-quantity')) {
      updateRecipeCosts();
    }
  });

  document.addEventListener('change', function (event) {
    if (event.target.classList.contains('recipe-material')) {
      updateRecipeCosts();
    }
  });

  document.addEventListener('click', function (event) {
    if (event.target.classList.contains('remove-row')) {
      event.target.closest('tr').remove();
      updateRecipeCosts();
    }
  });

  updateRecipeCosts();
</script>
@endsection
