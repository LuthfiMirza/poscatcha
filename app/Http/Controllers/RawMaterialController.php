<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\RawMaterialStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::query()->orderBy('name')->get();
        $movements = RawMaterialStockMovement::with('rawMaterial')->latest()->take(20)->get();

        return view('admin.raw-materials.index', compact('materials', 'movements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:raw_materials,name'],
            'unit' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $material = RawMaterial::create($validated);

            if ((float) $validated['stock'] > 0) {
                RawMaterialStockMovement::create([
                    'raw_material_id' => $material->id,
                    'transaction_id' => null,
                    'type' => 'in',
                    'reason' => 'Initial Stock',
                    'quantity' => $validated['stock'],
                    'quantity_before' => 0,
                    'quantity_after' => $validated['stock'],
                    'action_by' => Auth::user()->name,
                ]);
            }
        });

        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil ditambahkan');
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:raw_materials,name,' . $rawMaterial->id],
            'unit' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:80'],
        ]);

        DB::transaction(function () use ($validated, $rawMaterial) {
            $before = (float) $rawMaterial->stock;
            $after = (float) $validated['stock'];
            $delta = $after - $before;

            $rawMaterial->update([
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'stock' => $after,
                'minimum_stock' => $validated['minimum_stock'],
            ]);

            if ($delta !== 0.0) {
                RawMaterialStockMovement::create([
                    'raw_material_id' => $rawMaterial->id,
                    'transaction_id' => null,
                    'type' => $delta > 0 ? 'in' : 'adjustment',
                    'reason' => $validated['reason'],
                    'quantity' => abs($delta),
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'action_by' => Auth::user()->name,
                ]);
            }
        });

        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil diperbarui');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        if ($rawMaterial->recipes()->exists()) {
            return redirect()->route('raw-materials.index')->with('error', 'Bahan masih dipakai di resep produk');
        }

        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil dihapus');
    }
}
