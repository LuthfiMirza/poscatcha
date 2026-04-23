<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
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
        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $purchases = Purchase::query()
            ->with(['supplier', 'creator', 'items.product'])
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('purchase_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('purchase_date', '<=', $request->date_to);
            })
            ->when($request->filled('supplier_id'), function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })
            ->when($request->filled('supplier_name'), function ($query) use ($request) {
                $query->where('supplier_name', 'like', '%' . $request->supplier_name . '%');
            })
            ->latest('purchase_date')
            ->latest('id')
            ->get();

        return view('admin.purchases.index', compact('purchases', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->orderBy('product_name')
            ->get();

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePurchase($request);

        $purchase = DB::transaction(function () use ($validated) {
            $purchaseDate = $validated['purchase_date'];
            $purchaseNumber = $this->generatePurchaseNumber($purchaseDate);

            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'supplier_name' => empty($validated['supplier_id']) ? ($validated['supplier_name'] ?? null) : null,
                'purchase_date' => $purchaseDate,
                'invoice_number' => $validated['invoice_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $this->applyPurchaseItems($purchase, $validated['items']);

            return $purchase;
        });

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Purchase berhasil disimpan.');
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'creator', 'items.product']);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase): View
    {
        $purchase->load('items');

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->orderBy('product_name')
            ->get();

        return view('admin.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $validated = $this->validatePurchase($request);

        try {
            DB::transaction(function () use ($purchase, $validated) {
                $purchase->load('items');

                $affectedProductIds = $purchase->items
                    ->pluck('product_id')
                    ->merge(collect($validated['items'])->pluck('product_id'))
                    ->unique()
                    ->values();

                foreach ($affectedProductIds as $productId) {
                    Product::query()
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();
                }

                foreach ($purchase->items as $item) {
                    $product = Product::query()
                        ->where('product_id', $item->product_id)
                        ->first();

                    if (!$product) {
                        throw ValidationException::withMessages([
                            'purchase' => "Produk dengan ID {$item->product_id} tidak ditemukan.",
                        ]);
                    }

                    $remainingStock = $product->product_quantity - $item->quantity;

                    if ($remainingStock < 0) {
                        throw ValidationException::withMessages([
                            'purchase' => "Stok {$product->product_name} tidak cukup untuk memperbarui purchase ini.",
                        ]);
                    }

                    $product->update([
                        'product_quantity' => $remainingStock,
                    ]);
                }

                StockMovement::query()
                    ->where('source', 'purchase')
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

                foreach ($affectedProductIds as $productId) {
                    $product = Product::query()
                        ->where('product_id', $productId)
                        ->first();

                    if ($product) {
                        $this->syncLatestBuyPrice($product);
                    }
                }
            });
        } catch (ValidationException $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($exception->errors());
        }

        return redirect()
            ->route('purchases.show', $purchase)
            ->with('success', 'Purchase berhasil diperbarui.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        try {
            DB::transaction(function () use ($purchase) {
                $purchase->load('items');
                $affectedProducts = [];

                foreach ($purchase->items as $item) {
                    $product = Product::query()
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw ValidationException::withMessages([
                            'purchase' => "Produk dengan ID {$item->product_id} sudah tidak tersedia, sehingga purchase tidak bisa dibatalkan otomatis.",
                        ]);
                    }

                    $remainingStock = $product->product_quantity - $item->quantity;

                    if ($remainingStock < 0) {
                        throw ValidationException::withMessages([
                            'purchase' => "Stok {$product->product_name} tidak cukup untuk membatalkan purchase ini.",
                        ]);
                    }

                    $product->update([
                        'product_quantity' => $remainingStock,
                    ]);

                    $affectedProducts[$product->product_id] = $product;
                }

                StockMovement::query()
                    ->where('source', 'purchase')
                    ->where('transaction_id', $purchase->purchase_number)
                    ->delete();

                $purchase->delete();

                foreach ($affectedProducts as $product) {
                    $this->syncLatestBuyPrice($product);
                }
            });
        } catch (ValidationException $exception) {
            return redirect()
                ->route('purchases.index')
                ->withErrors($exception->errors());
        }

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase berhasil dihapus dan stok sudah dikembalikan.');
    }

    protected function generatePurchaseNumber(string $purchaseDate): string
    {
        $datePart = date('Ymd', strtotime($purchaseDate));

        $latestNumber = Purchase::query()
            ->where('purchase_number', 'like', "PO-{$datePart}-%")
            ->latest('id')
            ->value('purchase_number');

        $lastSequence = 0;

        if ($latestNumber) {
            $parts = explode('-', $latestNumber);
            $lastSequence = (int) end($parts);
        }

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
            'items.*.product_id' => ['required', 'exists:products,product_id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.buy_price' => ['required', 'integer', 'min:1'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->filled('supplier_id') && !$request->filled('supplier_name')) {
                $validator->errors()->add('supplier_name', 'Pilih supplier atau isi nama supplier manual.');
            }

            $items = collect($request->input('items', []))
                ->pluck('product_id')
                ->filter();

            if ($items->count() !== $items->unique()->count()) {
                $validator->errors()->add('items', 'Produk yang sama tidak boleh dipilih lebih dari satu kali dalam satu purchase.');
            }
        });

        return $validator->validate();
    }

    protected function applyPurchaseItems(Purchase $purchase, array $items): void
    {
        foreach ($items as $item) {
            $product = Product::query()
                ->where('product_id', $item['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $quantityBefore = $product->product_quantity;
            $quantityAfter = $quantityBefore + (int) $item['quantity'];

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->product_id,
                'quantity' => $item['quantity'],
                'buy_price' => $item['buy_price'],
            ]);

            $product->update([
                'product_quantity' => $quantityAfter,
                'buy_price' => $item['buy_price'],
            ]);

            StockMovement::create([
                'product_id' => $product->product_id,
                'transaction_id' => $purchase->purchase_number,
                'product_name' => $product->product_name,
                'status' => 5,
                'source' => 'purchase',
                'reason' => 'Restock',
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'action_by' => Auth::user()->name,
            ]);
        }
    }

    protected function syncLatestBuyPrice(Product $product): void
    {
        $latestItem = PurchaseItem::query()
            ->select('purchase_items.buy_price')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->where('purchase_items.product_id', $product->product_id)
            ->orderByDesc('purchases.purchase_date')
            ->orderByDesc('purchases.id')
            ->orderByDesc('purchase_items.id')
            ->first();

        $product->update([
            'buy_price' => $latestItem?->buy_price,
        ]);
    }
}
