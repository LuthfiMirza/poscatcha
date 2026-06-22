<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\RawMaterial;
use App\Models\RawMaterialStockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $suppliers = Supplier::query()->orderBy('name')->get();

        $purchases = Purchase::query()
            ->with(['supplier', 'creator', 'items.rawMaterial'])
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('purchase_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('purchase_date', '<=', $request->date_to))
            ->when($request->filled('supplier_id'), fn ($query) => $query->where('supplier_id', $request->supplier_id))
            ->when($request->filled('supplier_name'), fn ($query) => $query->where('supplier_name', 'like', '%' . $request->supplier_name . '%'))
            ->latest('purchase_date')
            ->latest('id')
            ->get();

        return view('admin.purchases.index', compact('purchases', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::query()->orderBy('name')->get();
        $rawMaterials = RawMaterial::query()->orderBy('name')->get();

        return view('admin.purchases.create', compact('suppliers', 'rawMaterials'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePurchase($request);

        $purchase = DB::transaction(function () use ($validated) {
            $purchase = Purchase::create([
                'purchase_number' => $this->generatePurchaseNumber($validated['purchase_date']),
                'supplier_id' => $validated['supplier_id'] ?? null,
                'supplier_name' => empty($validated['supplier_id']) ? ($validated['supplier_name'] ?? null) : null,
                'purchase_date' => $validated['purchase_date'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->applyPurchaseItems($purchase, $validated['items']);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Restock bahan baku berhasil disimpan.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'creator', 'items.rawMaterial']);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase): View
    {
        $purchase->load('items.rawMaterial');
        $suppliers = Supplier::query()->orderBy('name')->get();
        $rawMaterials = RawMaterial::query()->orderBy('name')->get();

        return view('admin.purchases.edit', compact('purchase', 'suppliers', 'rawMaterials'));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $validated = $this->validatePurchase($request);

        try {
            DB::transaction(function () use ($purchase, $validated) {
                $purchase->load('items.rawMaterial');
                $this->reversePurchaseItems($purchase);

                RawMaterialStockMovement::query()
                    ->where('type', 'in')
                    ->where('transaction_id', $purchase->purchase_number)
                    ->delete();

                $purchase->items()->delete();
                $purchase->update([
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'supplier_name' => empty($validated['supplier_id']) ? ($validated['supplier_name'] ?? null) : null,
                    'purchase_date' => $validated['purchase_date'],
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                $this->applyPurchaseItems($purchase, $validated['items']);
            });
        } catch (ValidationException $exception) {
            return redirect()->back()->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Restock bahan baku berhasil diperbarui.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        try {
            DB::transaction(function () use ($purchase) {
                $purchase->load('items.rawMaterial');
                $this->reversePurchaseItems($purchase);

                RawMaterialStockMovement::query()
                    ->where('type', 'in')
                    ->where('transaction_id', $purchase->purchase_number)
                    ->delete();

                $purchase->delete();
            });
        } catch (ValidationException $exception) {
            return redirect()->route('purchases.index')->withErrors($exception->errors());
        }

        return redirect()->route('purchases.index')->with('success', 'Restock dihapus dan stok bahan dikembalikan.');
    }

    protected function generatePurchaseNumber(string $purchaseDate): string
    {
        $datePart = date('Ymd', strtotime($purchaseDate));
        $latestNumber = Purchase::query()
            ->where('purchase_number', 'like', "PO-{$datePart}-%")
            ->latest('id')
            ->value('purchase_number');

        $lastSequence = $latestNumber ? (int) last(explode('-', $latestNumber)) : 0;

        return sprintf('PO-%s-%04d', $datePart, $lastSequence + 1);
    }

    protected function validatePurchase(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'supplier_name' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.buy_price' => ['required', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->filled('supplier_id') && !$request->filled('supplier_name')) {
                $validator->errors()->add('supplier_name', 'Pilih supplier atau isi nama supplier manual.');
            }

            $items = collect($request->input('items', []))->pluck('raw_material_id')->filter();
            if ($items->count() !== $items->unique()->count()) {
                $validator->errors()->add('items', 'Bahan baku yang sama tidak boleh dipilih lebih dari satu kali.');
            }
        });

        return $validator->validate();
    }

    protected function applyPurchaseItems(Purchase $purchase, array $items): void
    {
        foreach ($items as $item) {
            $material = RawMaterial::query()->whereKey($item['raw_material_id'])->lockForUpdate()->firstOrFail();
            $quantityBefore = (float) $material->stock;
            $quantityAfter = $quantityBefore + (float) $item['quantity'];

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'raw_material_id' => $material->id,
                'product_id' => null,
                'quantity' => $item['quantity'],
                'buy_price' => $item['buy_price'],
            ]);

            $material->update(['stock' => $quantityAfter]);

            RawMaterialStockMovement::create([
                'raw_material_id' => $material->id,
                'transaction_id' => $purchase->purchase_number,
                'type' => 'in',
                'reason' => 'Restock Purchase',
                'quantity' => $item['quantity'],
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'action_by' => Auth::user()->name,
            ]);
        }
    }

    protected function reversePurchaseItems(Purchase $purchase): void
    {
        foreach ($purchase->items as $item) {
            $material = RawMaterial::query()->whereKey($item->raw_material_id)->lockForUpdate()->first();
            if (!$material) {
                continue;
            }

            $quantityBefore = (float) $material->stock;
            $quantityAfter = $quantityBefore - (float) $item->quantity;

            if ($quantityAfter < 0) {
                throw ValidationException::withMessages([
                    'purchase' => "Stok {$material->name} tidak cukup untuk membatalkan restock ini.",
                ]);
            }

            $material->update(['stock' => $quantityAfter]);

            RawMaterialStockMovement::create([
                'raw_material_id' => $material->id,
                'transaction_id' => $purchase->purchase_number,
                'type' => 'adjustment',
                'reason' => 'Reverse Purchase',
                'quantity' => $item->quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'action_by' => Auth::user()->name,
            ]);
        }
    }
}
