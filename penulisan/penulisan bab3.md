# BAB III
# METODE PENELITIAN

## 3.1 Tahapan Penelitian

Penelitian ini bertujuan membangun sistem Point of Sale (POS) untuk CATCHA yang menangani tiga alur utama: transaksi tatap muka di kasir, pengelolaan stok bahan baku berbasis resep produk, dan pemesanan online dengan verifikasi pembayaran QRIS. Sistem dikembangkan menggunakan metode Waterfall, dipilih karena kebutuhan ketiga alur tersebut sudah dapat dipetakan sejak awal dari permasalahan yang diuraikan pada Bab I, sehingga tahapan pengembangan dapat disusun berurutan tanpa perlu berulang kali mengubah rancangan besar sistem. Tahapan penelitian secara garis besar dapat dilihat pada Gambar 3.1.

**[Gambar 3.1 — Tahapan Metode Waterfall pada Pengembangan Sistem]**

Tahap pertama adalah analisis kebutuhan, yaitu menggali kebutuhan admin, kasir, dan pembeli melalui observasi langsung terhadap operasional CATCHA serta wawancara dengan pemilik dan kasir. Tahap kedua adalah desain sistem, mencakup perancangan basis data, use case, dan activity diagram, sebagaimana diuraikan pada Sub-bab 3.2. Tahap ketiga adalah implementasi, yaitu menerjemahkan rancangan menjadi kode program menggunakan Laravel 11, Livewire 3, dan MySQL, dibahas melalui tiga alur inti pada Sub-bab 3.3 sampai 3.5. Tahap keempat adalah pengujian memakai black box testing terhadap seluruh fitur yang sudah dibangun, dengan rincian skenario pada Sub-bab 3.6. Tahap kelima adalah pemeliharaan, mencakup perbaikan bug yang ditemukan selama pengujian maupun setelah sistem digunakan.

## 3.2 Perancangan Basis Data

Perancangan basis data dilakukan untuk memastikan tiga alur utama sistem (transaksi kasir, stok bahan baku, pemesanan online) tercatat dalam struktur data yang saling terhubung dan tidak saling menimpa. Rancangan basis data dituangkan dalam bentuk entity relationship diagram (ERD) yang ditunjukkan pada Gambar 3.2.

**[Gambar 3.2 — Entity Relationship Diagram Sistem POS CATCHA]**

Salah satu tabel penopang perhitungan modal produk adalah tabel `product_recipes`, yang menghubungkan setiap produk dengan bahan baku beserta takaran yang dibutuhkan per satu produk. Struktur tabel ini ditunjukkan pada Tabel 3.1.

Tabel 3.1 Struktur Tabel `product_recipes`

| Kolom | Tipe Data | Keterangan |
|---|---|---|
| id | bigint, primary key | Id baris resep |
| product_id | string, foreign key ke `products.product_id` | Produk yang memiliki resep ini |
| raw_material_id | bigint, foreign key ke `raw_materials.id` | Bahan baku yang dipakai |
| quantity_required | decimal(12,2) | Takaran bahan baku per satu produk |
| created_at, updated_at | timestamp | Waktu pencatatan |

Kombinasi `product_id` dan `raw_material_id` diberi constraint unique, sehingga satu produk tidak bisa memiliki dua baris resep untuk bahan baku yang sama; jika admin menambahkan bahan yang sudah ada, sistem menjumlahkan takarannya alih-alih membuat baris duplikat. Rancangan ini menjadi dasar bagi perhitungan modal produk otomatis yang dibahas pada Sub-bab 3.3.

## 3.3 Perhitungan Modal Produk dari Resep Bahan Baku

Salah satu kebutuhan utama CATCHA adalah mengetahui modal (harga pokok) setiap produk tanpa admin harus menghitungnya secara manual setiap kali harga bahan baku berubah. Sistem menghitung modal produk secara otomatis berdasarkan resep bahan baku yang diinput admin, dikalikan harga satuan bahan baku dari transaksi pembelian (purchase) terakhir.

Sebagai ilustrasi, Tabel 3.2 menunjukkan data resep produk "Ragdoll Bliss Oat Milk Cold Whisk" beserta harga satuan bahan baku yang dipakai, diambil langsung dari data yang tersimpan pada sistem.

Tabel 3.2 Contoh Perhitungan Modal Produk dari Resep

| Bahan Baku | Takaran per Produk | Harga Satuan Terakhir | Biaya |
|---|---|---|---|
| Bubuk Matcha | 5,00 gram | Rp300,00 / gram | Rp1.500,00 |
| Air | 40,00 ml | Rp1,00 / ml | Rp40,00 |
| Susu Oat (Oat Milk) | 180,00 ml | Rp45,00 / ml | Rp8.100,00 |
| Vanilla Syrup | 10,00 ml | Rp80,00 / ml | Rp800,00 |
| Cup | 1,00 pcs | Rp500,00 / pcs | Rp500,00 |
| Es Batu | 120,00 gram | Rp1,00 / gram | Rp120,00 |
| **Total Modal** | | | **Rp11.060,00** |

Nilai total modal pada Tabel 3.2 sama persis dengan nilai `buy_price` produk tersebut yang tersimpan di basis data, karena keduanya dihasilkan oleh proses perhitungan yang sama. Proses tersebut dirangkum dalam Algoritma 3.1.

Algoritma 3.1. Perhitungan Modal Produk dari Resep Bahan Baku

**Input**
Daftar resep produk (pasangan bahan baku dan takaran per produk)

**Proses**
1. Mengambil seluruh transaksi pembelian bahan baku (purchase item) yang tercatat, diurutkan dari yang paling baru.
2. Untuk setiap bahan baku, mengambil satu transaksi pembelian terakhirnya saja.
3. Menghitung harga satuan bahan baku tersebut, yaitu harga beli dibagi jumlah yang dibeli.
4. Untuk setiap baris resep produk, mengalikan takaran bahan baku dengan harga satuan bahan baku pada langkah 3.
5. Menjumlahkan seluruh hasil perkalian pada langkah 4 menjadi satu nilai total.
6. Jika suatu bahan baku belum pernah dibeli, harga satuannya dihitung sebagai 0.

**Output**
Nilai total modal produk (`buy_price`), yang kemudian dipakai untuk memperbarui kolom `buy_price` dan `product_profit` pada produk terkait.

Implementasi dari algoritma tersebut pada `app/Http/Controllers/ProductRecipeController.php` ditunjukkan sebagai berikut:

```php
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

    return (float) $recipes->map(function (float $quantityRequired, int|string $rawMaterialId) use ($materialCosts) {
        return $quantityRequired * (float) ($materialCosts[$rawMaterialId] ?? 0);
    })->sum();
}
```

Fungsi `latestMaterialUnitCosts()` mengambil seluruh baris `purchase_items` yang punya `raw_material_id`, diurutkan dari id terbesar (transaksi paling baru), kemudian `unique('raw_material_id')` menyisakan satu baris per bahan baku, yaitu baris transaksi pembelian terakhirnya. Setiap baris itu diubah menjadi harga satuan (`buy_price` dibagi `quantity`) melalui `mapWithKeys()`, menghasilkan pemetaan id bahan baku ke harga satuannya.

Fungsi `calculateRecipeCost()` menerima kumpulan resep (hasil pengelompokan per bahan baku), lalu untuk setiap baris resep mengalikan `quantity_required` dengan harga satuan bahan baku terkait, dan menjumlahkan seluruh hasilnya dengan `sum()`. Bahan baku yang belum pernah dibeli (`$materialCosts[$rawMaterialId] ?? 0`) dihitung berbiaya Rp0, dan sistem menampilkan peringatan pada antarmuka admin agar modal produk tersebut tidak dianggap final sebelum bahan baku itu direstock.

## 3.4 Transaksi Kasir dan Pengurangan Stok Bahan Baku

Setiap transaksi kasir harus mengurangi stok bahan baku sesuai resep produk yang terjual, dan proses ini harus tetap konsisten meski beberapa transaksi terjadi hampir bersamaan pada bahan baku yang sama. Alur transaksi kasir ditunjukkan pada Gambar 3.3.

**[Gambar 3.3 — Activity Diagram Transaksi Kasir]**

Sebagai ilustrasi, Tabel 3.3 menunjukkan hasil satu transaksi kasir (INV-20260713-0001) beserta rincian produk yang terjual, diambil dari data transaksi yang tersimpan pada sistem.

Tabel 3.3 Contoh Rincian Transaksi Kasir

| No | Produk | Qty | Harga Jual | Sub Total | Profit |
|---|---|---|---|---|---|
| 1 | Calico Swirl Coconut Matcha Cold Whisk | 33 | Rp40.000,00 | Rp1.320.000,00 | Rp808.500,00 |
| 2 | Siamese Sunset MegaPaw Bottle 1000 ml | 20 | Rp76.000,00 | Rp1.520.000,00 | Rp1.056.000,00 |
| | | | **Total** | **Rp2.840.000,00** | |

Proses pengurangan stok bahan baku pada setiap transaksi dirangkum dalam Algoritma 3.2.

Algoritma 3.2. Pengurangan Stok Bahan Baku pada Transaksi Kasir

**Input**
Daftar keranjang transaksi kasir (produk dan jumlah yang dibeli)

**Proses**
1. Mengunci baris shift kasir yang sedang aktif agar tidak berubah selama transaksi diproses.
2. Mencatat data penjualan (sale) beserta total, metode pembayaran, dan kembalian.
3. Untuk setiap produk pada keranjang, mengunci baris produk beserta resepnya.
4. Jika produk belum punya resep, transaksi dibatalkan dan sistem menampilkan pesan kesalahan.
5. Mencatat rincian penjualan (detail sale) beserta modal dan profit per produk.
6. Untuk setiap bahan baku pada resep produk, mengunci baris bahan baku tersebut.
7. Menghitung stok bahan baku sesudah dikurangi jumlah yang terpakai.
8. Jika stok sesudah dikurangi bernilai negatif, seluruh transaksi dibatalkan dan sistem menampilkan pesan stok tidak cukup.
9. Menyimpan stok baru dan mencatat riwayat pergerakan stok (stock movement).
10. Mengosongkan keranjang kasir setelah seluruh langkah di atas berhasil.

**Output**
Data penjualan tersimpan, stok bahan baku berkurang sesuai resep, dan riwayat pergerakan stok tercatat; atau seluruh proses dibatalkan (rollback) apabila salah satu pemeriksaan pada langkah 4 atau 8 gagal.

Implementasi dari algoritma tersebut pada `app/Livewire/SellingProduct.php` ditunjukkan sebagai berikut:

```php
public function sellProduct()
{
    $sale_id = DB::transaction(function () {
        $saleId = Sale::generateInvoiceNumber();
        $activeShift = CashierShift::query()->open()
            ->where('cashier_id', $this->cashier_id)
            ->lockForUpdate()->firstOrFail();

        Sale::create([...]);

        foreach ($carts as $cart) {
            $product = Product::where('product_id', $cart->product_id)
                ->with('recipes.rawMaterial')->lockForUpdate()->firstOrFail();

            if ($product->recipes->isEmpty()) {
                throw ValidationException::withMessages([
                    'recipe' => 'Produk "'.$cart->product_name.'" belum punya resep bahan baku.',
                ]);
            }

            DetailSale::create([...]);

            foreach ($product->recipes as $recipe) {
                $material = RawMaterial::whereKey($recipe->raw_material_id)->lockForUpdate()->firstOrFail();
                $requiredQuantity = (float) $recipe->quantity_required * (int) $cart->quantity;
                $quantityAfter = (float) $material->stock - $requiredQuantity;

                if ($quantityAfter < 0) {
                    throw ValidationException::withMessages([
                        'stock' => 'Stok bahan "'.$material->name.'" tidak cukup untuk '.$cart->product_name.'.',
                    ]);
                }

                $material->stock = $quantityAfter;
                $material->save();

                RawMaterialStockMovement::create([...]);
            }
        }

        Cart::where('cashier_id', $this->cashier_id)->delete();
        return $saleId;
    });
}
```

Seluruh proses dibungkus `DB::transaction()`, sehingga jika terjadi kegagalan pada langkah mana pun (produk belum punya resep, atau stok bahan baku tidak cukup), seluruh perubahan yang sudah terjadi di dalam transaksi tersebut ikut dibatalkan (rollback), termasuk data penjualan yang sudah sempat dibuat. Pemanggilan `lockForUpdate()` pada baris shift, produk, dan bahan baku mengunci baris tersebut di level basis data selama transaksi berlangsung, sehingga apabila dua kasir menjual produk dengan bahan baku yang sama secara hampir bersamaan, pengurangan stok tetap diproses satu per satu dan tidak menghasilkan angka stok yang salah akibat race condition.

## 3.5 Pemesanan Online dan Verifikasi Pembayaran QRIS

Pembeli dapat memesan produk secara online dan membayar melalui metode cash atau QRIS manual. Karena verifikasi QRIS dilakukan secara manual oleh kasir (tanpa API payment gateway), sistem perlu menahan pengurangan stok bahan baku sampai pembayaran benar-benar diverifikasi, agar stok tidak berkurang untuk pesanan yang ternyata belum dibayar. Alur ini ditunjukkan pada Gambar 3.4.

**[Gambar 3.4 — Activity Diagram Pemesanan Online dan Verifikasi QRIS]**

Sebagai ilustrasi, Tabel 3.4 menunjukkan satu pesanan online (ORD-20260714-005250-YQTK) yang sudah diverifikasi dan dikonfirmasi, diambil dari data yang tersimpan pada sistem.

Tabel 3.4 Contoh Data Pesanan Online

| Order Code | Item | Qty | Harga | Status | Status Pembayaran |
|---|---|---|---|---|---|
| ORD-20260714-005250-YQTK | Add On Bottled | 3 | Rp2.000,00 | confirmed | paid |

Proses checkout dan konfirmasi pesanan dirangkum dalam Algoritma 3.3.

Algoritma 3.3. Checkout dan Konfirmasi Pesanan Online

**Input**
Keranjang belanja pembeli (`BuyerCart`) dan metode pembayaran yang dipilih

**Proses**
1. Mengunci baris keranjang pembeli beserta isinya.
2. Untuk setiap item pada keranjang, mengunci baris produk beserta resepnya dan memeriksa ketersediaan stok bahan baku.
3. Jika stok bahan baku tidak cukup untuk salah satu item, checkout dibatalkan dan sistem menampilkan pesan kesalahan.
4. Membuat data pesanan (order) dengan status pending; status pembayaran diisi "unpaid" untuk cash atau "waiting_verification" untuk QRIS.
5. Mencatat rincian item pesanan dan mengosongkan keranjang pembeli.
6. Kasir memverifikasi bukti pembayaran QRIS secara manual; jika sesuai, status pembayaran diubah menjadi "paid".
7. Setelah status pembayaran "paid" (atau metode cash), kasir mengonfirmasi pesanan: sistem mengunci baris produk dan bahan baku pada resep, lalu mengurangi stok bahan baku sesuai jumlah pesanan.
8. Jika pesanan dibatalkan setelah stok sempat dikurangi, sistem mengembalikan (restore) stok bahan baku yang sudah terpakai.

**Output**
Pesanan tersimpan dengan status dan status pembayaran yang konsisten; stok bahan baku hanya berkurang setelah pembayaran terverifikasi dan pesanan dikonfirmasi.

Implementasi dari langkah checkout dan konfirmasi pada `app/Services/OnlineOrdering/OrderCheckoutService.php` dan `app/Services/OnlineOrdering/OrderWorkflowService.php` ditunjukkan sebagai berikut:

```php
public function checkout(User $buyer, string $paymentMethod, ?string $note = null): Order
{
    return DB::transaction(function () use ($buyer, $paymentMethod, $note) {
        $cart = BuyerCart::query()->where('user_id', $buyer->id)
            ->with('items.product')->lockForUpdate()->first();

        foreach ($cart->items as $cartItem) {
            $product = Product::query()->with('recipes.rawMaterial')
                ->where('product_id', $cartItem->product_id)->lockForUpdate()->firstOrFail();

            if (! $product->hasAvailableStock((int) $cartItem->quantity)) {
                throw ValidationException::withMessages([
                    'stock' => 'Stok bahan untuk '.$product->product_name.' tidak cukup untuk checkout.',
                ]);
            }
            // ...hitung subtotal, simpan order + order item
        }

        $cart->items()->delete();
        return $order->load('items');
    });
}

public function confirm(Order $order, User $actor): Order
{
    return DB::transaction(function () use ($order, $actor) {
        $order = Order::whereKey($order->id)->with('items')->lockForUpdate()->firstOrFail();

        if ($order->payment_method === Order::PAYMENT_QRIS
            && $order->payment_status !== Order::PAYMENT_STATUS_PAID) {
            throw ValidationException::withMessages([
                'payment_status' => 'Verifikasi pembayaran QRIS terlebih dahulu sebelum konfirmasi pesanan.',
            ]);
        }

        foreach ($order->items->sortBy('product_id') as $item) {
            $product = Product::where('product_id', $item->product_id)
                ->with('recipes.rawMaterial')->lockForUpdate()->firstOrFail();

            $this->deductRecipeMaterials($product, (int) $item->quantity, $order->order_code, $actor);
        }

        $order->update([
            'status' => Order::STATUS_CONFIRMED,
            'stock_deducted_at' => now(),
        ]);

        return $order->fresh(['items', 'buyer']);
    });
}
```

Pada `checkout()`, pemeriksaan `hasAvailableStock()` dilakukan sebelum data pesanan dibuat, sehingga pembeli tidak bisa checkout produk yang bahan bakunya tidak cukup, tetapi stok belum dikurangi pada tahap ini. Pada `confirm()`, sistem terlebih dulu memastikan pesanan berstatus QRIS sudah "paid" sebelum melanjutkan; jika belum, `ValidationException` dilempar dan stok tidak disentuh sama sekali. Pengurangan stok baru terjadi di dalam `deductRecipeMaterials()`, yang polanya sama dengan pengurangan stok pada transaksi kasir di Sub-bab 3.4: mengunci baris bahan baku, menghitung stok sesudah dikurangi, dan membatalkan seluruh proses apabila hasilnya negatif. Kolom `stock_deducted_at` dipakai sebagai penanda agar stok pesanan yang sama tidak bisa dikurangi dua kali, dan menjadi acuan bagi proses pembatalan pesanan untuk menentukan apakah stok perlu dikembalikan.

## 3.6 Metode Pengujian Sistem

Pengujian sistem pada penelitian ini menggunakan metode black box testing, yaitu pengujian yang berfokus pada kesesuaian input dan output setiap fitur tanpa memeriksa struktur kode program di baliknya. Metode ini dipilih karena tujuan pengujian adalah memastikan setiap fitur berjalan sesuai kebutuhan, bukan mengevaluasi efisiensi algoritma di dalamnya.

Pengujian dilakukan per modul, dimulai dari modul autentikasi dan hak akses (login admin, kasir, pembeli, serta memastikan satu peran tidak bisa mengakses halaman milik peran lain), dilanjutkan modul pengelolaan produk, resep, dan bahan baku (tambah, ubah, hapus, termasuk validasi input yang salah), lalu modul transaksi kasir dan pengurangan stok sebagaimana dibahas pada Sub-bab 3.4.

Pengujian berikutnya mencakup modul pemesanan online dan verifikasi QRIS sebagaimana dibahas pada Sub-bab 3.5, meliputi checkout, verifikasi dan penolakan pembayaran, perubahan status pesanan, serta pengurangan dan pengembalian stok bahan baku pada titik yang tepat. Pengujian turut mencakup modul pembelian bahan baku dari supplier serta modul laporan (penjualan, laba, shift kasir, dan pergerakan stok) untuk memastikan angka yang ditampilkan sesuai dengan data transaksi yang tersimpan.

Setiap skenario pengujian dicatat dalam format tabel yang berisi kolom skenario pengujian, hasil yang diharapkan, hasil yang didapat, dan kesimpulan (valid/tidak valid), sebagaimana ditampilkan pada Bab IV.
