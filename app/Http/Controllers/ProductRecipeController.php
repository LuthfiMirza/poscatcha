<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\PurchaseItem;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class ProductRecipeController extends Controller
{
    public function edit(Product $product)
    {
        $product->load('recipes.rawMaterial');
        $materials = RawMaterial::query()->orderBy('name')->get();
        $materialCosts = $this->latestMaterialUnitCosts();

        return view('admin.products.recipe', compact('product', 'materials', 'materialCosts'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'recipes' => ['nullable', 'array'],
            'recipes.*.raw_material_id' => ['nullable', 'exists:raw_materials,id'],
            'recipes.*.quantity_required' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $recipes = collect($validated['recipes'] ?? [])
            ->filter(fn ($recipe) => !empty($recipe['raw_material_id']) && !empty($recipe['quantity_required']))
            ->groupBy('raw_material_id')
            ->map(fn ($items) => (float) $items->sum('quantity_required'));

        ProductRecipe::where('product_id', $product->product_id)->delete();

        foreach ($recipes as $rawMaterialId => $quantityRequired) {
            ProductRecipe::create([
                'product_id' => $product->product_id,
                'raw_material_id' => $rawMaterialId,
                'quantity_required' => $quantityRequired,
            ]);
        }

        $buyPrice = $this->calculateRecipeCost($recipes);
        $product->update([
            'buy_price' => $buyPrice,
            'product_profit' => (float) $product->product_price - $buyPrice,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Resep produk berhasil disimpan dan modal produk otomatis diperbarui.');
    }

    protected function latestMaterialUnitCosts()
    {
        return PurchaseItem::query()
            ->whereNotNull('raw_material_id')
            ->where('quantity', '>', 0)
            ->latest('id')
            ->get()
            ->unique('raw_material_id')
            ->mapWithKeys(fn (PurchaseItem $item) => [
                $item->raw_material_id => (float) $item->buy_price / (float) $item->quantity,
            ]);
    }

    protected function calculateRecipeCost($recipes): float
    {
        $materialCosts = $this->latestMaterialUnitCosts();

        return (float) $recipes
            ->map(fn (float $quantityRequired, int|string $rawMaterialId) => $quantityRequired * (float) ($materialCosts[$rawMaterialId] ?? 0))
            ->sum();
    }
}
