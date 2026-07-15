@php
  $isEdit = isset($purchase);
  $formAction = $isEdit ? route('purchases.update', $purchase) : route('purchases.store');
  $buttonLabel = $isEdit ? 'Update Purchase' : 'Simpan Purchase';
  $cancelRoute = $isEdit ? route('purchases.show', $purchase) : route('purchases.index');

  $defaultItems = $isEdit
      ? $purchase->items->map(fn ($item) => [
          'raw_material_id' => $item->raw_material_id,
          'package_quantity' => $item->package_quantity ?? 1,
          'package_size' => $item->package_size ?? $item->quantity,
          'package_label' => $item->package_label,
          'quantity' => $item->quantity,
          'buy_price' => $item->buy_price,
      ])->toArray()
      : [['raw_material_id' => '', 'package_quantity' => 1, 'package_size' => 1, 'package_label' => '', 'quantity' => 1, 'buy_price' => '']];

  $oldItems = old('items', $defaultItems);
  $lastPurchaseItems = $lastPurchaseItems ?? collect();
@endphp

@push('styles')
<style>
  #purchase-items-table {
    min-width: 1120px;
  }

  #purchase-items-table th,
  #purchase-items-table td {
    vertical-align: top;
  }

  #purchase-items-table th {
    white-space: nowrap;
  }

  #purchase-items-table .material-cell {
    min-width: 260px;
  }

  #purchase-items-table .package-quantity-cell,
  #purchase-items-table .package-size-cell,
  #purchase-items-table .package-label-cell,
  #purchase-items-table .quantity-cell {
    min-width: 150px;
  }

  #purchase-items-table .price-cell {
    min-width: 180px;
  }

  #purchase-items-table .action-cell {
    min-width: 96px;
    text-align: center;
  }

  #purchase-items-table .input-group-text {
    white-space: nowrap;
  }

  #purchase-items-table .last-price-hint,
  #purchase-items-table .unit-price-hint {
    display: block;
    line-height: 1.35;
    margin-top: .35rem;
  }
</style>
@endpush

<form method="POST" action="{{ $formAction }}">
  @csrf
  @if ($isEdit)
    @method('PUT')
  @endif

  <div class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Informasi Purchase</h5>

          @if ($isEdit)
            <div class="mb-3">
              <label class="form-label">Nomor Purchase</label>
              <input type="text" class="form-control" value="{{ $purchase->purchase_number }}" readonly>
            </div>
          @endif

          <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier Terdaftar</label>
            <select class="form-select" id="supplier_id" name="supplier_id">
              <option value="">Pilih Supplier</option>
              @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $isEdit ? $purchase->supplier_id : null) == $supplier->id)>{{ $supplier->name }}</option>
              @endforeach
            </select>
            <small class="text-muted">Kosongkan jika ingin isi supplier manual.</small>
          </div>

          <div class="mb-3">
            <label for="supplier_name" class="form-label">Nama Supplier Manual</label>
            <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ old('supplier_name', $isEdit ? $purchase->supplier_name : null) }}" placeholder="Isi jika supplier belum ada di master">
          </div>

          <div class="mb-3">
            <label for="purchase_date" class="form-label">Tanggal Purchase</label>
            <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $isEdit ? $purchase->purchase_date->toDateString() : now()->toDateString()) }}" required>
          </div>

          <div class="mb-3">
            <label for="invoice_number" class="form-label">Nomor Invoice</label>
            <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $isEdit ? $purchase->invoice_number : null) }}">
          </div>

          <div class="mb-3">
            <label for="notes" class="form-label">Catatan</label>
            <textarea class="form-control" id="notes" name="notes" rows="5">{{ old('notes', $isEdit ? $purchase->notes : null) }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">Item Restock Bahan Baku</h5>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" id="toggle-package-mode">
              <label class="form-check-label" for="toggle-package-mode">Input per kemasan (jumlah &times; isi)</label>
            </div>
            <button type="button" class="btn btn-success btn-sm" id="add-item-row">
              <i class="bx bx-plus"></i> Tambah Item
            </button>
          </div>
          <p class="text-muted small mb-2">Secara default, isi langsung <strong>Total Masuk</strong> sesuai nota. Aktifkan saklar di atas kalau mau hitung otomatis dari jumlah kemasan &times; isi per kemasan.</p>

          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="purchase-items-table">
              <thead>
                <tr>
                  <th class="material-cell">Bahan Baku</th>
                  <th class="pkg-col package-quantity-cell d-none">Jumlah Kemasan</th>
                  <th class="pkg-col package-size-cell d-none">Isi / Kemasan</th>
                  <th class="pkg-col package-label-cell d-none">Nama Kemasan</th>
                  <th class="quantity-cell">Total Masuk</th>
                  <th class="price-cell">Harga Total</th>
                  <th class="action-cell">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($oldItems as $index => $item)
                  <tr>
                    <td class="material-cell">
                      <select name="items[{{ $index }}][raw_material_id]" class="form-select material-select" required>
                        <option value="">Pilih Bahan</option>
                        @foreach ($rawMaterials as $material)
                          <option value="{{ $material->id }}" data-unit="{{ $material->unit }}" @selected(($item['raw_material_id'] ?? '') == $material->id)>
                            {{ $material->name }} ({{ $material->unit }})
                          </option>
                        @endforeach
                      </select>
                      <small class="text-muted last-price-hint"></small>
                    </td>
                    <td class="pkg-col package-quantity-cell d-none">
                      <input type="number" name="items[{{ $index }}][package_quantity]" class="form-control package-quantity" min="0.01" step="0.01" value="{{ $item['package_quantity'] ?? 1 }}">
                    </td>
                    <td class="pkg-col package-size-cell d-none">
                      <div class="input-group">
                        <input type="number" name="items[{{ $index }}][package_size]" class="form-control package-size" min="0.01" step="0.01" value="{{ $item['package_size'] ?? ($item['quantity'] ?? 1) }}">
                        <span class="input-group-text material-unit">{{ optional($rawMaterials->firstWhere('id', $item['raw_material_id'] ?? null))->unit ?: 'unit' }}</span>
                      </div>
                    </td>
                    <td class="pkg-col package-label-cell d-none">
                      <input type="text" name="items[{{ $index }}][package_label]" class="form-control" value="{{ $item['package_label'] ?? '' }}" placeholder="kotak/botol/dus">
                    </td>
                    <td class="quantity-cell">
                      <div class="input-group">
                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control total-quantity" min="0.01" step="0.01" value="{{ $item['quantity'] ?? 1 }}" required>
                        <span class="input-group-text material-unit">{{ optional($rawMaterials->firstWhere('id', $item['raw_material_id'] ?? null))->unit ?: 'unit' }}</span>
                      </div>
                    </td>
                    <td class="price-cell">
                      <input type="number" name="items[{{ $index }}][buy_price]" class="form-control item-buy-price" min="0" step="0.01" value="{{ $item['buy_price'] ?? '' }}" placeholder="Harga total" required>
                      <small class="text-muted unit-price-hint"></small>
                    </td>
                    <td class="action-cell">
                      <button type="button" class="btn btn-danger btn-sm remove-item-row">Hapus</button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end fw-bold" id="grand-total-label">Total Nota</td>
                  <td class="fw-bold" id="grand-total">Rp0</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <button type="submit" class="btn btn-primary">{{ $buttonLabel }}</button>
          <a href="{{ $cancelRoute }}" class="btn btn-secondary">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</form>

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('purchase-items-table');
    const tableBody = table.querySelector('tbody');
    const addButton = document.getElementById('add-item-row');
    const packageModeToggle = document.getElementById('toggle-package-mode');
    const lastPurchaseItems = @json($lastPurchaseItems);
    let rowIndex = {{ count($oldItems) }};

    function formatRupiah(value) {
      return 'Rp' + Number(value || 0).toLocaleString('id-ID', { maximumFractionDigits: 2 });
    }

    function buildMaterialOptions() {
      return `{!! collect($rawMaterials)->map(function ($material) {
          return '<option value="' . e($material->id) . '" data-unit="' . e($material->unit) . '">' . e($material->name) . ' (' . e($material->unit) . ')</option>';
      })->implode('') !!}`;
    }

    function isPackageMode() {
      return packageModeToggle.checked;
    }

    function updateRow(row) {
      const materialSelect = row.querySelector('.material-select');
      const selectedOption = materialSelect.options[materialSelect.selectedIndex];
      const unit = selectedOption?.dataset.unit || 'unit';
      const packageQuantityInput = row.querySelector('.package-quantity');
      const packageSizeInput = row.querySelector('.package-size');
      const totalQuantityInput = row.querySelector('.total-quantity');
      const lastPurchase = lastPurchaseItems[materialSelect.value];

      row.querySelectorAll('.material-unit').forEach((element) => {
        element.textContent = unit;
      });

      if (isPackageMode()) {
        const packageQuantity = parseFloat(packageQuantityInput.value) || 0;
        const packageSize = parseFloat(packageSizeInput.value) || 0;
        const totalQuantity = packageQuantity * packageSize;
        totalQuantityInput.value = totalQuantity ? totalQuantity.toFixed(2) : '';
      } else {
        // Simple mode: Total Masuk is typed directly, keep package fields in sync
        // (package_quantity = 1, package_size = total) so the saved data stays consistent.
        const totalQuantity = parseFloat(totalQuantityInput.value) || 0;
        packageQuantityInput.value = 1;
        packageSizeInput.value = totalQuantity || '';
      }

      const lastPriceHint = row.querySelector('.last-price-hint');
      if (lastPurchase) {
        lastPriceHint.textContent = `Terakhir: ${lastPurchase.package_quantity} ${lastPurchase.package_label} x ${lastPurchase.package_size} ${lastPurchase.unit || unit} = ${lastPurchase.quantity} ${lastPurchase.unit || unit}, Rp${Number(lastPurchase.buy_price).toLocaleString('id-ID')} (${Number(lastPurchase.unit_price).toLocaleString('id-ID')} / ${lastPurchase.unit || unit}) ${lastPurchase.date || ''}`;
      } else {
        lastPriceHint.textContent = 'Belum ada riwayat harga untuk bahan ini.';
      }

      updateUnitPriceHint(row, unit);
    }

    function updateUnitPriceHint(row, unit) {
      const quantity = parseFloat(row.querySelector('.total-quantity').value) || 0;
      const buyPrice = parseFloat(row.querySelector('.item-buy-price').value) || 0;
      const hint = row.querySelector('.unit-price-hint');

      hint.textContent = quantity > 0 && buyPrice > 0
        ? `≈ ${formatRupiah(buyPrice / quantity)} / ${unit}`
        : '';
    }

    function updateGrandTotal() {
      let total = 0;
      tableBody.querySelectorAll('.item-buy-price').forEach((input) => {
        total += parseFloat(input.value) || 0;
      });
      document.getElementById('grand-total').textContent = formatRupiah(total);
    }

    function applyPackageModeVisibility() {
      const showPackageCols = isPackageMode();
      document.getElementById('grand-total-label').colSpan = showPackageCols ? 5 : 2;
      table.querySelectorAll('.pkg-col').forEach((cell) => {
        cell.classList.toggle('d-none', !showPackageCols);
      });
      tableBody.querySelectorAll('tr').forEach((row) => {
        row.querySelector('.package-quantity').required = showPackageCols;
        row.querySelector('.package-size').required = showPackageCols;
        row.querySelector('.total-quantity').readOnly = showPackageCols;
        updateRow(row);
      });
      updateGrandTotal();
    }

    packageModeToggle.addEventListener('change', applyPackageModeVisibility);

    addButton.addEventListener('click', function () {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td class="material-cell">
          <select name="items[${rowIndex}][raw_material_id]" class="form-select material-select" required>
            <option value="">Pilih Bahan</option>
            ${buildMaterialOptions()}
          </select>
          <small class="text-muted last-price-hint"></small>
        </td>
        <td class="pkg-col package-quantity-cell d-none">
          <input type="number" name="items[${rowIndex}][package_quantity]" class="form-control package-quantity" min="0.01" step="0.01" value="1">
        </td>
        <td class="pkg-col package-size-cell d-none">
          <div class="input-group">
            <input type="number" name="items[${rowIndex}][package_size]" class="form-control package-size" min="0.01" step="0.01" value="1">
            <span class="input-group-text material-unit">unit</span>
          </div>
        </td>
        <td class="pkg-col package-label-cell d-none">
          <input type="text" name="items[${rowIndex}][package_label]" class="form-control" placeholder="kotak/botol/dus">
        </td>
        <td class="quantity-cell">
          <div class="input-group">
            <input type="number" name="items[${rowIndex}][quantity]" class="form-control total-quantity" min="0.01" step="0.01" value="" required>
            <span class="input-group-text material-unit">unit</span>
          </div>
        </td>
        <td class="price-cell">
          <input type="number" name="items[${rowIndex}][buy_price]" class="form-control item-buy-price" min="0" step="0.01" placeholder="Harga total" required>
          <small class="text-muted unit-price-hint"></small>
        </td>
        <td class="action-cell">
          <button type="button" class="btn btn-danger btn-sm remove-item-row">Hapus</button>
        </td>
      `;

      tableBody.appendChild(row);
      row.querySelector('.package-quantity').required = isPackageMode();
      row.querySelector('.package-size').required = isPackageMode();
      row.querySelector('.total-quantity').readOnly = isPackageMode();
      updateRow(row);
      rowIndex += 1;
    });

    // Initial render: only refresh hints/labels, never overwrite existing package
    // breakdown values (important when editing a purchase that already has real
    // package data saved).
    tableBody.querySelectorAll('tr').forEach(function (row) {
      const materialSelect = row.querySelector('.material-select');
      const unit = materialSelect.options[materialSelect.selectedIndex]?.dataset.unit || 'unit';
      row.querySelectorAll('.material-unit').forEach((element) => { element.textContent = unit; });
      const lastPurchase = lastPurchaseItems[materialSelect.value];
      const lastPriceHint = row.querySelector('.last-price-hint');
      lastPriceHint.textContent = lastPurchase
        ? `Terakhir: ${lastPurchase.package_quantity} ${lastPurchase.package_label} x ${lastPurchase.package_size} ${lastPurchase.unit || unit} = ${lastPurchase.quantity} ${lastPurchase.unit || unit}, Rp${Number(lastPurchase.buy_price).toLocaleString('id-ID')} (${Number(lastPurchase.unit_price).toLocaleString('id-ID')} / ${lastPurchase.unit || unit}) ${lastPurchase.date || ''}`
        : 'Belum ada riwayat harga untuk bahan ini.';
      updateUnitPriceHint(row, unit);
    });
    updateGrandTotal();

    tableBody.addEventListener('input', function (event) {
      const row = event.target.closest('tr');
      if (!row) return;

      if (event.target.classList.contains('package-quantity') || event.target.classList.contains('package-size')) {
        updateRow(row);
      } else if (event.target.classList.contains('total-quantity')) {
        updateRow(row);
      } else if (event.target.classList.contains('item-buy-price')) {
        const materialSelect = row.querySelector('.material-select');
        const unit = materialSelect.options[materialSelect.selectedIndex]?.dataset.unit || 'unit';
        updateUnitPriceHint(row, unit);
        updateGrandTotal();
      }
    });

    tableBody.addEventListener('change', function (event) {
      if (!event.target.classList.contains('material-select')) {
        return;
      }

      updateRow(event.target.closest('tr'));
    });

    tableBody.addEventListener('click', function (event) {
      if (!event.target.classList.contains('remove-item-row')) {
        return;
      }

      if (tableBody.querySelectorAll('tr').length === 1) {
        return;
      }

      event.target.closest('tr').remove();
      updateGrandTotal();
    });
  });
</script>
@endsection
