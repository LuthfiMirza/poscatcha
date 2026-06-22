<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class ProductRecipeController extends Controller
{
    public function edit(Product $product)
    {
        $product->load('recipes.rawMaterial');
        $materials = RawMaterial::query()->orderBy('name')->get();

        return view('admin.products.recipe', compact('product', 'materials'));
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

        return redirect()->route('admin.products.index')->with('success', 'Resep produk berhasil disimpan');
    }
}
