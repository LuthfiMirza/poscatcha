<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Category;
use App\Models\CashierShift;
use App\Models\DetailSale;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\RawMaterialStockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    // public function create(): View
    // {
    //     return view('auth.login');
    // }
    // /**
    //  * Handle an incoming authentication request.
    //  */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     if (Auth::check()) {
    //         $user = Auth::user();
    
    //         if ($user->hasRole('admin')) {
    //             return redirect()->route('dashboard_admin');
    //         }
    
    //         if ($user->hasRole('cashier')) {
    //             return redirect()->route('dashboard_cashier');
    //         }
    //     }


    //     return redirect()->route('login');
    // }
    
    public function dashboard_admin()
    {
        $today = Carbon::today();
        $expiringLimit = $today->copy()->addDays(30);

        $todaySales = Sale::query()->whereDate('created_at', $today)->get();
        $lowStockMaterials = RawMaterial::query()
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->orderBy('name')
            ->take(5)
            ->get();
        $expiringProducts = Product::query()
            ->whereNotNull('product_expired')
            ->whereDate('product_expired', '>=', $today)
            ->whereDate('product_expired', '<=', $expiringLimit)
            ->orderBy('product_expired')
            ->take(5)
            ->get();
        $recentSales = Sale::query()->latest()->take(5)->get();
        $recentPurchases = Purchase::query()
            ->with('supplier')
            ->latest('purchase_date')
            ->latest('id')
            ->take(5)
            ->get();
        $categories = Category::query()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $categoryProductCounts = Product::query()
            ->select('product_category', DB::raw('COUNT(*) as total_products'))
            ->groupBy('product_category')
            ->pluck('total_products', 'product_category');
        $cashiers = User::query()
            ->where('id', '!=', 1)
            ->get()
            ->keyBy('id');

        $stats = [
            'total_products' => Product::count(),
            'supplier_count' => Supplier::count(),
            'low_stock_count' => RawMaterial::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'expiring_count' => Product::query()
                ->whereNotNull('product_expired')
                ->whereDate('product_expired', '>=', $today)
                ->whereDate('product_expired', '<=', $expiringLimit)
                ->count(),
            'sales_today_count' => $todaySales->count(),
            'sales_today_total' => $todaySales->sum('total'),
            'active_shifts' => CashierShift::open()->count(),
        ];

        return view('admin.dashboard_admin', compact(
            'categories',
            'categoryProductCounts',
            'cashiers',
            'expiringProducts',
            'lowStockMaterials',
            'recentPurchases',
            'recentSales',
            'stats'
        ));
    }

    public function products_index()
    {
        $products = Product::query()
            ->withCount('recipes')
            ->orderBy('product_name')
            ->get();
        $categoriesById = Category::query()
            ->orderBy('category_name')
            ->pluck('category_name', 'category_id');

        return view('admin.products.index', compact('products', 'categoriesById'));
    }
    
    public function add_product()
    {
        $categories = Category::all();
        $productId = $this->generateProductId();

        return view('admin.add_product', compact('categories', 'productId'));
    }

    public function add_product_process(Request $request)
    {
        $request->merge([
            'product_id' => $this->generateProductId(),
        ]);

        $validated = $request->validate([
            'product_id' => ['required', 'string', 'max:5', 'unique:products,product_id'],
            'product_name' => ['required', 'string', 'max:50'],
            'product_category' => ['required', 'string', 'max:6'],
            'product_price' => ['required', 'integer', 'min:1'],
            'buy_price' => ['nullable', 'integer', 'min:0'],
            'product_quantity' => ['required', 'integer', 'min:1'],
            'product_expired' => ['required', 'date'],
            'product_image' => ['required', 'file', 'image'],
        ]);

        $transaction_id = "-";
        $status = 1;
        $reason = "Add Product";
        $quantity_before = 0; 
        $user = Auth::user()->name;
        $buyPrice = (int) ($validated['buy_price'] ?? 0);
        $productProfit = (int) $validated['product_price'] - $buyPrice;


        $product_image = null;

        if ($request->hasFile('product_image')) { 
            $image = $request->file('product_image');
            $name = date('Ymd_His') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Simpan ke storage Laravel 
            $image->storeAs('assets/product', $name);
            
            // Simpan hanya nama file untuk database
            $product_image = str_replace('public/', '', $name);
        }    
    
        $product = Product::postProduct(
            $validated['product_id'],
            $validated['product_name'],
            $validated['product_category'],
            $product_image,
            $validated['product_price'],
            $buyPrice,
            $productProfit,
            $validated['product_quantity'],
            $validated['product_expired'],
            $transaction_id,
            $status,
            $reason,
            $quantity_before,
            $user,
        );

        return redirect()->route('admin.products.recipe.edit', $product)
            ->with('success', 'Produk berhasil dibuat. Lanjut isi resep agar modal/buy price dihitung otomatis.');
    }

    protected function generateProductId(): string
    {
        $latestProductId = Product::query()
            ->where('product_id', 'like', 'P%')
            ->orderByRaw('CAST(SUBSTRING(product_id, 2) AS UNSIGNED) DESC')
            ->value('product_id');

        $latestSequence = $latestProductId ? (int) substr($latestProductId, 1) : 0;

        do {
            $latestSequence++;
            $productId = 'P'.str_pad((string) $latestSequence, 4, '0', STR_PAD_LEFT);
        } while (Product::query()->where('product_id', $productId)->exists());

        return $productId;
    }
    public function edit_product($id)
    {
        $product = Product::find($id);
        $categories = Category::all();
        return view('admin.edit_product', compact('product', 'categories'));
    }

    public function edit_product_process(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:50'],
            'product_category' => ['required', 'string', 'max:6'],
            'product_price' => ['required', 'integer', 'min:1', 'gte:buy_price'],
            'buy_price' => ['required', 'integer', 'min:0'],
            'product_quantity' => ['required', 'integer', 'min:1'],
            'product_expired' => ['required', 'date'],
            'product_image' => ['nullable', 'file', 'image'],
            'reason' => ['required', 'in:2,3,4'],
        ]);

        $product_id = $product->product_id;
        $transaction_id = "-";
        $status = 2;
        $reason_message = $validated['reason'];
        if ($reason_message == 2) {
            $reason = "Wrong Input";
        } elseif ($reason_message == 3) {
            $reason = "Product Is Lost";
        } elseif ($reason_message == 4) {
            $reason = "Product Is Damaged";
        } else {
            $reason = "Wrong Input";
        }

        $quantity_before = $product->product_quantity;
        $user = Auth::user()->name;
        $productProfit = (int) $validated['product_price'] - (int) $validated['buy_price'];

        if ((int) $validated['product_quantity'] > (int) $quantity_before && $reason_message != 2) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'product_quantity' => 'Penambahan stok rutin tidak bisa dilakukan dari Edit Produk. Gunakan menu Restock.',
                ]);
        }

        if ($request->hasFile('product_image')) {
            if ($product->product_image) {
                Storage::delete('assets/product/' . $product->product_image);
            }

            $image = $request->file('product_image');
            $name = date('Ymd_His') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('assets/product', $name);
            $product_image = str_replace('public/', '', $name);


        } else {
            $product_image = $product->product_image;
        }

        $product = Product::updateProduct(
            $id,
            $product_id,
            $validated['product_name'],
            $validated['product_category'],
            $product_image,
            $validated['product_price'],
            $validated['buy_price'],
            $productProfit,
            $validated['product_quantity'],
            $validated['product_expired'],
            $transaction_id,
            $status,
            $reason,
            $quantity_before,
            $user,
        );

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function delete_product($id)
    {
        $product = Product::find($id);
        $categories = Category::all();
        return view('admin.delete_product', compact('product', 'categories'));
    }

    public function delete_product_process(Request $request, $id)
    {
        $transaction_id = "-";
        $status = 3;
        $reason_message = $request->reason;
        if ($reason_message == 2) {
            $reason = "Wrong Input";
        } elseif ($reason_message == 3) {
            $reason = "Expired";
        } elseif ($reason_message == 4) {
            $reason = "Product Is Lost";
        } elseif ($reason_message == 5) {
            $reason = "Product Is Damaged";
        } else {
            $reason = "Wrong Input";
        }


        $quantity_after = 0; 
        $user = Auth::user()->name;

       $product = Product::find($id);

       Storage::delete('assets/product/' . $product->product_image);
       
       $product = Product::deleteProduct(
           $id,
           $transaction_id,
           $status,
           $reason,
           $quantity_after,
           $user,
       );
       
       return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }

    public function add_category()
    {
        return view('admin.add_category');
    }

    public function categories_index()
    {
        $categories = Category::query()
            ->orderBy('category_name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function add_category_process(Request $request)
    {
        $user = Auth::user()->name;
        
        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'category_name' => 'required',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();

        $data_category['category_id'] = $request->category_id;
        $data_category['category_name'] = $request->category_name;
        $data_category['added_by'] = $user;

        Category::create($data_category);

        return redirect()->route('admin.categories.index')->with('success', 'Category added successfully');
    }

    public function edit_category($id)
    {
        // dd($id);
        $category = Category::find($id);
        return view('admin.edit_category', compact('category'));
    }

    public function edit_category_process(Request $request, $id)
    {
        $user = Auth::user()->name;
        
        $validator = Validator::make($request->all(),[
            'category_id' => 'required',
            'category_name' => 'required',
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();

        $data_category['category_id'] = $request->category_id;
        $data_category['category_name'] = $request->category_name;
        $data_category['added_by'] = $user; 

        Category::where('id', $id)->update($data_category);     

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');    
    }

    public function delete_category($id)
    {
        Category::find($id)->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }

    public function sales_data(Request $request)
    {
        $users = User::where('id', '!=', 1)
            ->orderBy('name')
            ->get();

        $sales = Sale::query()
            ->when($request->filled('sale_id'), function ($query) use ($request) {
                $query->where('sale_id', 'like', '%' . $request->sale_id . '%');
            })
            ->when($request->filled('cashier_id'), function ($query) use ($request) {
                $query->where('cashier_id', $request->cashier_id);
            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->get();

        return view('admin.sales_data', compact('sales', 'users'));
    }

    public function detail_sales_data($sale_id)
    {
        $saleHeader = Sale::where('sale_id', $sale_id)->first();
        $users = User::where('id', '!=', 1)->get();
        $sales = DetailSale::where('sale_id', $sale_id)->get();
        return view('admin.detail_sales_data', compact('sales', 'users', 'saleHeader'));
    }






    public function stock_movements(Request $request)
    {
        $stock_movements = RawMaterialStockMovement::query()
            ->with('rawMaterial')
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            ->latest()
            ->get();

        return view('admin.stock_movement_admin', compact('stock_movements'));
    }

    public function user_data()
    {
        $users = User::with('roles')->where('id', '!=', 1)->get();
        return view('admin.user_data', compact('users'));
    }

    public function add_user()
    {
        return view('admin.add_user');
    }

    public function add_user_process(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->givePermissionTo('cashier-dashboard');
        $user->assignRole('cashier');

        event(new Registered($user));

        return redirect()->route('dashboard_admin')->with('success', 'User added successfully');
    }
    
    public function edit_user($id)
    {
        $user = User::find($id);
        return view('admin.edit_user', compact('user'));
    }

    public function edit_user_process(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('user_data')->with('success', 'User updated successfully');
    }

    public function delete_user($id)
    {
        User::find($id)->delete();
        return redirect()->route('user_data')->with('success', 'User deleted successfully');
    }

    public function login_admin()
    {
        return view('admin.login_admin');
    }

    public function admin_profile()
    {
        $user = Auth::user();
        return view('admin.admin_profile', compact('user'));
    }

    public function update_admin_profile(Request $request)
    {
        // dd($request->all());
        request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            // 'notelp' => 'required|numeric',
            // 'kota' => 'required',
            // 'alamat' => 'required',
            // 'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // if ($request->hasFile('foto')) {
        //     $image = $request->file('foto');
        //     $name = Str::random(10) . '.' . $image->getClientOriginalExtension();
        //     $image->storeAs('assets/foto', $name, 'public');
        // }

        $user = User::where('id', Auth::user()->id)->first();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            // 'notelp' => $request->notelp,
            // 'kota' => $request->kota,
            // 'alamat' => $request->alamat,
            // 'foto' => $name,
        ]);

        return redirect()->route('admin_profile')->with('success', 'Update Profil Berhasil');
    }

    public function update_admin_password(Request $request)
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

        return redirect()->route('admin_profile')->with('success', 'Password Berhasil Diubah');
    }
    
}
