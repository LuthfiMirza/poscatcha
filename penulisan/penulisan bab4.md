# BAB IV
# HASIL DAN PEMBAHASAN

## 4.1 Implementasi Sistem

Sistem Point of Sale CATCHA diimplementasikan menggunakan PHP 8.2 ke atas dengan framework Laravel 11, dipadukan dengan Livewire 3 untuk komponen transaksi kasir yang membutuhkan interaksi langsung. Basis data MySQL dijalankan melalui XAMPP, sementara aset frontend (CSS dan JavaScript) dibundel dengan Vite. Hak akses tiga peran pengguna, yaitu admin, kasir, dan pembeli, ditangani oleh Spatie Laravel Permission melalui middleware role dan middleware tambahan yang memeriksa status shift kasir sebelum halaman transaksi bisa diakses.

Pengujian dan pengambilan tangkapan layar pada bab ini dilakukan langsung terhadap aplikasi yang berjalan di lingkungan pengembangan lokal (`http://localhost`), memakai data produk, bahan baku, dan akun pengguna yang sudah tersimpan di basis data proyek. Dengan cara ini, tampilan dan hasil pengujian yang ditunjukkan pada bab ini benar-benar berasal dari aplikasi yang sudah berjalan, bukan rancangan di atas kertas.

Tiga akun dipakai untuk menguji tiga peran berbeda: admin (`admin@gmail.com`), kasir (`ariz@gmail.com`), dan pembeli (`test@gmail.com`). Urutan pembahasan pada bab ini mengikuti urutan tujuan penelitian pada Bab I, dimulai dari struktur navigasi dan rancangan sistem, dilanjutkan tampilan serta fungsi tiap peran, struktur tabel basis data, hingga hasil pengujian black box.

## 4.2 Struktur Navigasi

Struktur navigasi menggambarkan susunan menu yang bisa diakses tiap peran setelah login. Ketiga peran memakai struktur navigasi campuran: ada menu utama yang sifatnya hierarkis (dikelompokkan per kategori fungsi), tetapi pengguna juga bisa berpindah antar menu tanpa harus mengikuti urutan tertentu, sesuai kebutuhan saat itu.

### 4.2.1 Struktur Navigasi Admin

Admin memiliki lima kelompok menu utama setelah login: Dashboard, Master Data (Produk, Kategori, Bahan Baku, Supplier, Kasir/User), Transaksi (Sales Data, Restock, Stock Movement), Operasional (Shift Kasir), dan Laporan (Laporan Profit), ditambah menu Akun untuk profil admin dan chatbot admin. Pengelompokan ini terlihat langsung pada sidebar aplikasi dan memudahkan admin membedakan data master, riwayat transaksi, serta laporan tanpa harus mencari-cari di satu menu panjang.

**[Gambar 4.1 — Struktur Navigasi Admin]**

### 4.2.2 Struktur Navigasi Kasir

Kasir memiliki lima kelompok menu: Shift (buka dan tutup shift), Menu (daftar produk), Cashier (transaksi POS dan cetak struk), Online (antrian pesanan online, verifikasi/tolak QRIS, ubah status pesanan), dan Akun (profil kasir). Menu Shift ditempatkan paling atas karena kasir wajib membuka shift terlebih dahulu sebelum halaman transaksi (Cashier) dan daftar produk (Menu) bisa diakses; middleware `cashier.shift.active` akan menahan akses ke kedua halaman tersebut selama belum ada shift yang berstatus terbuka.

**[Gambar 4.2 — Struktur Navigasi Kasir]**

### 4.2.3 Struktur Navigasi Pembeli

Pembeli memiliki lima kelompok menu: Katalog (lihat menu dan detail produk), Keranjang (kelola item dan kustomisasi), Checkout (pilih pembayaran cash atau QRIS), Pesanan (detail, pantau status, bayar ulang, batalkan), dan Akun (profil pembeli). Halaman katalog tetap bisa diakses tanpa login, tetapi menambah ke keranjang dan checkout mensyaratkan pembeli login terlebih dahulu.

**[Gambar 4.3 — Struktur Navigasi Pembeli]**

## 4.3 Use Case Diagram

Use case diagram pada Gambar 4.4 menggambarkan interaksi tiga aktor (admin, kasir, pembeli) dengan sistem, diturunkan langsung dari kebutuhan fungsional yang sudah dirumuskan pada Bab I dan diterapkan sebagai route serta middleware pada kode program. Admin berinteraksi dengan use case yang bersifat pengelolaan data master dan pemantauan, kasir dengan use case operasional harian (shift, transaksi, pesanan online), dan pembeli dengan use case pemesanan (katalog, keranjang, checkout, pantau status).

Satu use case dipakai bersama oleh dua aktor: Verifikasi/Tolak Pembayaran QRIS bisa dijalankan oleh kasir maupun admin, sesuai middleware `role:cashier|admin` pada rute `online-orders`. Beberapa use case kasir memiliki hubungan include, misalnya Transaksi Penjualan Offline yang selalu menyertakan perhitungan kembalian dan pencatatan pergerakan stok, sementara Checkout pembeli memiliki hubungan extend dengan Verifikasi Pembayaran QRIS karena jalur ini hanya berjalan bila metode QRIS yang dipilih.

**[Gambar 4.4 — Use Case Diagram Sistem]**

## 4.4 Entity Relationship Diagram

Struktur basis data digambarkan pada entity relationship diagram di Gambar 4.5, mencakup entitas inti sistem: data master (`users`, `categories`, `products`, `raw_materials`, `suppliers`), tabel penghubung (`product_recipes`, `cashier_shifts`), data transaksi (`carts`, `purchases`, `purchase_items`, `sales`, `detail_sales`, `orders`, `order_items`), dan data riwayat (`order_status_histories`). Diagram ini disusun berdasarkan struktur migration dan model Eloquent yang sudah berjalan pada aplikasi, sehingga menggambarkan kondisi basis data yang benar-benar diimplementasikan.

Beberapa tabel pendukung tidak dimasukkan ke diagram utama agar tetap terbaca, yaitu `stock_movements` dan `raw_material_stock_movements` (masing-masing hanya berelasi satu arah ke `products` dan `raw_materials`), `buyer_carts` beserta `buyer_cart_items` (pola relasinya sama dengan `carts`), serta `admin_chatbot_logs` yang hanya berelasi ke `users`. Struktur lengkap tabel-tabel tersebut dijelaskan pada Sub-bab 4.6 dan Lampiran.

Relasi antar-entitas dirancang agar satu produk bisa memiliki banyak resep melalui `product_recipes`, masing-masing menunjuk ke satu bahan baku pada `raw_materials`. Tabel `orders` berelasi satu ke banyak dengan `order_items` dan `order_status_histories`, serta berelasi satu ke satu dengan `sales`; relasi satu ke satu ini baru terisi ketika pesanan online mencapai status completed. Tabel `sales` sendiri bisa berasal dari transaksi kasir langsung (kolom `shift_id`) atau dari pesanan online yang sudah selesai (kolom `order_id`), sehingga laporan penjualan bisa menghitung total dari kedua jalur transaksi dalam satu query.

**[Gambar 4.5 — Entity Relationship Diagram Sistem]**

## 4.5 Activity Diagram

### 4.5.1 Activity Diagram Transaksi Kasir Offline

Activity diagram pada Gambar 4.6 menggambarkan alur transaksi kasir secara offline: kasir membuka shift, memilih produk ke keranjang, mengubah jumlah item bila perlu, memilih metode pembayaran, sistem menghitung total dan kembalian, transaksi disimpan, stok berkurang, lalu struk dicetak. Keranjang transaksi tersimpan pada tabel `carts` berdasarkan kode kasir yang bertugas, sehingga kasir bisa berpindah halaman di tengah transaksi tanpa kehilangan produk yang sudah dipilih.

**[Gambar 4.6 — Activity Diagram Transaksi Kasir Offline]**

### 4.5.2 Activity Diagram Checkout dan Verifikasi QRIS

Activity diagram pada Gambar 4.7 menggambarkan alur pemesanan online beserta verifikasi QRIS. Pembeli memilih produk dan checkout, sistem membuat pesanan berstatus pending dengan status bayar unpaid (cash) atau waiting_verification (QRIS). Untuk QRIS, kasir atau admin memeriksa bukti pembayaran lalu memverifikasi atau menolaknya. Tepat pada langkah konfirmasi pesanan, bukan pada saat checkout maupun saat pesanan selesai, stok bahan baku dikurangi sesuai resep produk dalam pesanan. Pengujian pada Sub-bab 4.7.2 dan 4.7.3 menunjukkan urutan ini benar-benar terjadi seperti itu pada aplikasi yang berjalan.

**[Gambar 4.7 — Activity Diagram Checkout dan Verifikasi Pembayaran QRIS]**

## 4.6 Struktur Tabel Basis Data

Struktur tabel berikut memuat kolom-kolom utama pada tabel inti sistem, sebagai acuan langsung migration Laravel yang dijalankan pada tahap implementasi. Tabel selengkapnya (termasuk `carts`, `raw_material_stock_movements`, `admin_chatbot_logs`, dan tabel bawaan Spatie Permission) disertakan pada Lampiran.

**Tabel 4.1 Struktur Tabel `products`**

| Kolom | Tipe Data | Keterangan |
| --- | --- | --- |
| id | bigint | Primary key |
| product_id | varchar | Kode produk, unik |
| product_name | varchar(50) | Nama produk |
| product_category | varchar(6) | Foreign key ke `categories.category_id` |
| product_image | varchar(35) | Nama berkas gambar produk |
| product_price | integer | Harga jual |
| buy_price | decimal(10,2) | Harga beli/modal |
| product_profit | integer | Selisih harga jual dan modal |
| product_quantity | integer | Stok produk (dipakai bila produk tidak memiliki resep) |
| product_expired | varchar(10) | Tanggal kedaluwarsa |

**Tabel 4.2 Struktur Tabel `orders`**

| Kolom | Tipe Data | Keterangan |
| --- | --- | --- |
| id | bigint | Primary key |
| user_id | bigint | Foreign key ke `users.id` (pembeli) |
| order_code | varchar(40) | Kode pesanan, unik |
| status | varchar(20) | pending, confirmed, processing, completed, cancelled |
| payment_method | varchar(20) | cash, transfer, qris |
| payment_status | varchar(30) | unpaid, waiting_verification, paid, payment_rejected |
| total_price | integer unsigned | Total harga pesanan |
| confirmed_by / completed_by / cancelled_by | bigint, nullable | Foreign key ke `users.id`, mencatat aktor tiap aksi |
| stock_deducted_at | timestamp, nullable | Waktu stok dikurangi (diisi saat konfirmasi) |

**Tabel 4.3 Struktur Tabel `order_status_histories`**

| Kolom | Tipe Data | Keterangan |
| --- | --- | --- |
| id | bigint | Primary key |
| order_id | bigint | Foreign key ke `orders.id` |
| actor_id | bigint, nullable | Foreign key ke `users.id` |
| action | varchar(50) | Nama aksi, misalnya confirmed, payment_verified |
| from_status / to_status | varchar(30), nullable | Status pesanan sebelum dan sesudah aksi |
| from_payment_status / to_payment_status | varchar(30), nullable | Status pembayaran sebelum dan sesudah aksi |
| note | text, nullable | Catatan tambahan |

**Tabel 4.4 Struktur Tabel `product_recipes`**

| Kolom | Tipe Data | Keterangan |
| --- | --- | --- |
| id | bigint | Primary key |
| product_id | varchar | Foreign key ke `products.product_id` |
| raw_material_id | bigint | Foreign key ke `raw_materials.id` |
| quantity_required | decimal(12,2) | Jumlah bahan baku yang dibutuhkan per satu produk |

**Tabel 4.5 Struktur Tabel `sales`**

| Kolom | Tipe Data | Keterangan |
| --- | --- | --- |
| id | bigint | Primary key |
| sale_id | varchar(35) | Nomor invoice, unik |
| source | varchar | Sumber transaksi (kasir langsung/pesanan online) |
| order_id | bigint, nullable | Foreign key ke `orders.id`, terisi bila berasal dari pesanan online |
| shift_id | bigint, nullable | Foreign key ke `cashier_shifts.id`, terisi bila berasal dari transaksi kasir |
| cashier_id | varchar(20) | Kode kasir yang memproses transaksi |
| total | integer | Total transaksi |
| payment_method | varchar(1) | Kode metode pembayaran |
| pay / change | integer | Nominal dibayar dan kembalian |

**Tabel 4.6 Struktur Tabel `stock_movements`**

| Kolom | Tipe Data | Keterangan |
| --- | --- | --- |
| id | bigint | Primary key |
| product_id | varchar | Foreign key ke `products.product_id` |
| status | integer | 1 tambah produk, 2 update/penyesuaian, 3 hapus produk, 4 penjualan |
| source | varchar | Asal pergerakan stok, misalnya product, purchase, sale, online_order |
| reason | varchar | Alasan pergerakan stok |
| quantity_before / quantity_after | integer | Jumlah stok sebelum dan sesudah pergerakan |
| action_by | varchar | Nama pengguna yang melakukan aksi |

Beberapa kolom sengaja dibuat nullable, misalnya `order_id` dan `shift_id` pada tabel `sales`, untuk menampung dua sumber transaksi tanpa memaksakan satu struktur tabel yang sama untuk dua konteks berbeda. Kolom `status` pada `stock_movements` dipakai sebagai kode ringkas jenis pergerakan, sedangkan kolom `source` dan `reason` menjelaskan konteksnya secara lebih rinci; kombinasi ini terlihat langsung pada data nyata yang dibahas pada Sub-bab 4.7.3.

## 4.7 Tampilan dan Fungsi Sistem

Sub-bab ini menampilkan hasil implementasi sistem yang sudah berjalan, diambil langsung dari aplikasi (bukan rancangan/wireframe), disusun per peran pengguna: pembeli, kasir, dan admin.

### 4.7.1 Tampilan dan Fungsi Sisi Pembeli

Halaman katalog (Gambar 4.8) menampilkan daftar menu CATCHA beserta filter kategori (Semua, Add On, Matcha Menu, Thai Tea) dan kotak pencarian. Estimasi waktu pickup dan ringkasan promo ditampilkan di bagian atas halaman agar pembeli langsung mendapat gambaran sebelum memilih produk.

**[Gambar 4.8 — Halaman Katalog Pembeli]**

Halaman detail produk (Gambar 4.9) menampilkan opsi kustomisasi seperti tingkat es dan gula sebelum produk ditambahkan ke keranjang. Setelah produk ditambahkan, halaman keranjang (Gambar 4.10) menampilkan daftar item beserta catatan kustomisasi yang sudah dipilih dan subtotal per item.

**[Gambar 4.9 — Halaman Detail Produk]**

**[Gambar 4.10 — Halaman Keranjang Pembeli]**

Pada halaman checkout (Gambar 4.11), pembeli memilih metode pembayaran cash atau QRIS manual sebelum menekan tombol Buat Pesanan. Pengujian dilakukan dengan memilih metode QRIS pada satu item Add On Bottled sebanyak tiga buah (total Rp6.000). Hasilnya, sistem berhasil membuat pesanan dengan kode `ORD-20260714-005250-YQTK`, status pesanan Menunggu Konfirmasi, dan status bayar Menunggu Verifikasi, sekaligus menampilkan gambar QRIS beserta tombol Download dan Buka QRIS (Gambar 4.12). Halaman daftar pesanan (Gambar 4.13) kemudian menampilkan pesanan tersebut sesuai statusnya.

**[Gambar 4.11 — Halaman Checkout Pembeli]**

**[Gambar 4.12 — Pesanan Berhasil Dibuat dengan Pembayaran QRIS]**

**[Gambar 4.13 — Halaman Daftar Pesanan Pembeli]**

### 4.7.2 Tampilan dan Fungsi Sisi Kasir

Halaman daftar produk kasir (Gambar 4.14) menampilkan menu yang sama dengan katalog pembeli, tetapi dari sudut pandang kasir untuk memeriksa ketersediaan produk sebelum melayani pelanggan tatap muka. Halaman transaksi POS (Gambar 4.15) memperlihatkan kasir menambahkan produk Add On Bottled ke keranjang; sisi kanan halaman langsung memperbarui jumlah item, subtotal, dan total tanpa memuat ulang halaman, sesuai fungsi Livewire yang dipakai pada modul ini.

**[Gambar 4.14 — Halaman Daftar Produk Kasir]**

**[Gambar 4.15 — Transaksi POS: Produk Ditambahkan ke Keranjang]**

Halaman antrian pesanan online (Gambar 4.16) menampilkan pesanan `ORD-20260714-005250-YQTK` yang tadi dibuat oleh pembeli, lengkap dengan badge notifikasi pesanan baru pada ikon lonceng di pojok kanan atas. Membuka detail pesanan (Gambar 4.17) menampilkan rincian item, data pembeli, dan status pembayaran Menunggu Verifikasi, beserta tombol Verifikasi Pembayaran, Tolak Pembayaran, dan Konfirmasi Pesanan (dinonaktifkan selama pembayaran QRIS belum diverifikasi).

**[Gambar 4.16 — Antrian Pesanan Online Kasir]**

**[Gambar 4.17 — Detail Pesanan Online Sebelum Verifikasi]**

Setelah tombol Verifikasi Pembayaran ditekan, status bayar berubah menjadi Lunas dan tombol Konfirmasi Pesanan menjadi aktif (Gambar 4.18). Menekan Konfirmasi Pesanan memunculkan modal konfirmasi; setelah disetujui, sistem menampilkan pesan "Pesanan dikonfirmasi dan stok dikurangi", status pesanan berubah menjadi Diterima, dan kolom "Dikonfirmasi Oleh" terisi nama kasir yang bertugas (Gambar 4.19). Urutan ini konsisten dengan rancangan pada activity diagram Sub-bab 4.5.2: stok dikurangi tepat pada langkah konfirmasi, bukan pada saat pesanan dibuat.

**[Gambar 4.18 — Pembayaran QRIS Terverifikasi]**

**[Gambar 4.19 — Pesanan Online Terkonfirmasi, Stok Berkurang]**

Halaman profil kasir (Gambar 4.20) menampilkan form untuk mengubah data profil dan password kasir yang sedang login.

**[Gambar 4.20 — Halaman Profil Kasir]**

### 4.7.3 Tampilan dan Fungsi Sisi Admin

Dashboard admin (Gambar 4.21) menampilkan ringkasan operasional: total produk aktif, penjualan hari berjalan, jumlah bahan baku dengan stok di bawah minimum, produk yang akan kedaluwarsa dalam 30 hari, jumlah relasi supplier, dan jumlah shift kasir yang sedang aktif, dilengkapi tabel transaksi terbaru dan tombol pintas (quick actions) ke halaman yang sering dipakai.

**[Gambar 4.21 — Dashboard Admin]**

Halaman Produk (Gambar 4.22) menampilkan daftar seluruh produk beserta kolom harga beli, harga jual, keuntungan, jumlah bahan resep, dan tanggal kedaluwarsa. Form tambah produk (Gambar 4.23) diuji dengan mengirim form kosong; hasilnya browser menampilkan validasi "Please fill out this field" pada kolom yang wajib diisi dan form tidak diteruskan ke server, menandakan validasi input sudah berjalan sejak sisi klien.

**[Gambar 4.22 — Halaman Daftar Produk Admin]**

**[Gambar 4.23 — Validasi Form Tambah Produk]**

Halaman Kategori (Gambar 4.24) dan Bahan Baku (Gambar 4.25) menampilkan data master pendukung produk, sedangkan halaman Supplier (Gambar 4.26) dan Pembelian/Restock (Gambar 4.27) menampilkan data pemasok dan riwayat pembelian bahan baku.

**[Gambar 4.24 — Halaman Kategori]**

**[Gambar 4.25 — Halaman Bahan Baku]**

**[Gambar 4.26 — Halaman Supplier]**

**[Gambar 4.27 — Halaman Pembelian/Restock]**

Halaman Sales Data (Gambar 4.28) menampilkan riwayat transaksi penjualan, baik dari kasir langsung maupun dari pesanan online yang sudah selesai. Halaman Stock Movement (Gambar 4.29) menampilkan riwayat pergerakan stok bahan baku; pada pengujian ini, baris teratas menunjukkan pergerakan "Keluar" sebanyak 3,00 pcs untuk bahan Botol 250 ml dengan Transaction ID `ORD-20260714-005250-YQTK`, alasan "Online Order - Add On Bottled", dan Action By "ariz". Data ini cocok dengan pesanan online yang dikonfirmasi pada Sub-bab 4.7.2, dan membuktikan pengurangan stok berbasis resep benar-benar tercatat pada tabel riwayat.

**[Gambar 4.28 — Halaman Sales Data]**

**[Gambar 4.29 — Halaman Stock Movement]**

Halaman Laporan Profit (Gambar 4.30) dan Laporan Shift Kasir (Gambar 4.31) menampilkan ringkasan keuangan dan operasional yang bisa diunduh dalam format Excel maupun PDF. Halaman Kasir/User (Gambar 4.32) menampilkan daftar pengguna kasir yang bisa dikelola admin, dan halaman Profil Admin (Gambar 4.33) menampilkan form pengubahan data profil serta password admin.

**[Gambar 4.30 — Halaman Laporan Profit]**

**[Gambar 4.31 — Halaman Laporan Shift Kasir]**

**[Gambar 4.32 — Halaman Kasir/User]**

**[Gambar 4.33 — Halaman Profil Admin]**

## 4.8 Hasil Pengujian Black Box

Pengujian black box dilakukan langsung terhadap aplikasi yang berjalan, mengikuti rencana pengujian pada Bab III. Pengujian berfokus pada kesesuaian input dan output tiap fitur tanpa memeriksa kode program di baliknya. Tabel 4.7 merangkum skenario yang sudah diuji beserta hasilnya.

**Tabel 4.7 Hasil Pengujian Black Box**

| No. | Modul | Skenario Pengujian | Hasil yang Diharapkan | Hasil Aktual | Kesimpulan |
| --- | --- | --- | --- | --- | --- |
| 1 | Autentikasi | Login kasir dengan password salah | Login ditolak, pesan kredensial tidak sesuai ditampilkan | Sistem menampilkan "These credentials do not match our records.", login gagal | Valid |
| 2 | Otorisasi | Kasir mengakses `/admin/products` secara langsung lewat URL | Akses ditolak, bukan menampilkan data admin | Sistem mengembalikan HTTP 403 dengan pesan "User does not have the right roles." | Valid |
| 3 | Manajemen Produk | Admin mengirim form tambah produk tanpa mengisi data wajib | Validasi menolak, menampilkan pesan kolom wajib diisi | Form tidak terkirim, muncul validasi "Please fill out this field." pada kolom Product Name | Valid |
| 4 | Transaksi Kasir | Kasir menambahkan produk ke keranjang transaksi POS | Item bertambah ke keranjang, subtotal dan total otomatis terhitung | Keranjang menampilkan 2 item Add On Bottled, subtotal Rp4.000, total Rp4.000 | Valid |
| 5 | Pemesanan Online | Pembeli checkout dengan metode QRIS | Pesanan tersimpan berstatus pending, status bayar waiting_verification, gambar QRIS ditampilkan | Pesanan `ORD-20260714-005250-YQTK` dibuat, status Menunggu Konfirmasi, status bayar Menunggu Verifikasi, gambar QRIS tampil beserta tombol Download/Buka QRIS | Valid |
| 6 | Verifikasi Pembayaran | Kasir memverifikasi pembayaran QRIS pada pesanan pending | Status bayar berubah menjadi paid | Status bayar berubah menjadi Lunas, tombol Konfirmasi Pesanan menjadi aktif | Valid |
| 7 | Konfirmasi Pesanan | Kasir mengonfirmasi pesanan yang pembayarannya sudah lunas | Status pesanan menjadi confirmed, stok bahan baku berkurang sesuai resep | Muncul notifikasi "Pesanan dikonfirmasi dan stok dikurangi", status berubah menjadi Diterima, baris baru muncul pada Stock Movement | Valid |
| 8 | Pencatatan Stok | Menelusuri Stock Movement setelah konfirmasi pesanan online | Tercatat baris pergerakan stok dengan Transaction ID sama dengan kode pesanan | Baris teratas Stock Movement menunjukkan Transaction ID `ORD-20260714-005250-YQTK`, tipe Keluar, actor "ariz" | Valid |

Delapan skenario di atas mencakup jalur autentikasi, otorisasi lintas peran, validasi input, transaksi kasir, serta alur pemesanan online dari checkout sampai stok berkurang. Semua skenario menunjukkan hasil aktual yang sesuai dengan hasil yang diharapkan. Skenario lain di luar delapan ini, terutama pada modul pembelian bahan baku, laporan, dan pengelolaan kategori/supplier, disarankan diuji dengan pola pengujian yang sama sebelum sidang, mengingat keterbatasan waktu pengujian pada penyusunan bab ini.
