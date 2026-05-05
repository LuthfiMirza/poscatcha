<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\DetailSale;
use App\Models\Sale;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CashierController extends Controller
{
    public function list_product()
    {  
        $categories = Category::all();
        $products = Product::all();
        return view('cashier.list_product', compact('categories', 'products'));
    }

    public function selling_product()
    {
        return view('cashier.selling_product');
    }

    public function printReceipt($sale_id)
    {
        // Ambil data transaksi
        $sale = Sale::where('sale_id', $sale_id)->first();
        
        if (!$sale) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        // Ambil detail item transaksi
        $saleDetails = DetailSale::where('sale_id', $sale_id)->get();

        $cashier_name = User::where('id', $sale->cashier_id)->first()->name;

        // Data untuk struk
        $receiptData = [
            'sale' => $sale,
            'details' => $saleDetails,
            'store_name' => 'Catcha', 
            'store_address' => 'Kecamatan Ciputat, Kota Tangerang Selatan', 
            'store_phone' => '0812-3456-7890',
            'cashier_name' => $cashier_name
        ];

        return view('cashier.print', $receiptData);
    }

    public function cashier_profile()
    {
        $user = Auth::user();
        return view('cashier.cashier_profile', compact('user'));
    }

    public function update_cashier_profile(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::where('id', Auth::user()->id)->first();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('cashier_profile')->with('success', 'Update Profil Berhasil');
    }

    public function update_cashier_password(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'new_password_confirmation' => 'required|string|min:6',
        ]);

        $user = User::find(Auth::user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Password Lama Salah');
        }

        if ($request->new_password != $request->new_password_confirmation) {
            return redirect()->back()->with('error', 'Password Baru Tidak Sama');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('cashier_profile')->with('success', 'Password Berhasil Diubah');
    }

}
