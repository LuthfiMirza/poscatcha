# POS Laravel

Sistem Point of Sale (POS) berbasis Laravel untuk pengelolaan produk, kategori, transaksi kasir, pending order, cetak struk, monitoring pergerakan stok, dan chatbot admin berbasis query database.

## Ringkasan

Project ini dibuat dengan:

- PHP 8.2+
- Laravel 11
- Livewire 3
- MySQL
- Spatie Laravel Permission
- Vite

Terdapat dua role utama:

- `admin`: mengelola produk, kategori, user kasir, data penjualan, stock movement, profil admin, dan chatbot admin.
- `cashier`: melihat daftar produk, membuat transaksi, menyimpan pending order, melanjutkan pending order, mencetak struk, dan mengelola profil kasir.

## Fitur Utama

### Admin

- Dashboard produk dan kategori
- Tambah, edit, dan hapus produk
- Upload gambar produk
- Tambah, edit, dan hapus kategori
- Melihat data penjualan dan detail transaksi
- Melihat riwayat stock movement
- Kelola user kasir
- Ubah profil dan password admin
- Chatbot admin read-only untuk query data dari database

### Cashier

- Login khusus kasir
- Daftar produk
- Transaksi penjualan dengan Livewire
- Ubah quantity item di cart
- Simpan transaksi sebagai pending order
- Lanjutkan transaksi dari pending order
- Checkout dengan metode pembayaran `Cash`, `Transfer`, atau `QRIS`
- Hitung kembalian otomatis
- Cetak struk setelah checkout
- Ubah profil dan password kasir

## Modul dan Alur Data

### Produk dan stok

Data produk disimpan di tabel `products`. Setiap perubahan stok atau aksi terkait produk juga dicatat ke tabel `stock_movements`.

Status stock movement yang dipakai saat ini:

- `1`: tambah produk
- `2`: update produk / penyesuaian stok
- `3`: hapus produk
- `4`: penjualan produk

### Transaksi kasir

Alur transaksi aktif:

1. Kasir memilih produk ke cart.
2. Quantity bisa diubah langsung di halaman transaksi.
3. Transaksi bisa disimpan ke `pending_carts` dan `detail_pending_carts`.
4. Saat checkout, data disimpan ke `sales` dan `detail_sales`.
5. Stok produk dikurangi, lalu aktivitasnya dicatat ke `stock_movements`.
6. Sistem membuka halaman print receipt otomatis.

### Chatbot admin

Chatbot admin bersifat read-only dan tidak memakai API AI eksternal. Jawaban dibentuk dari query database berdasarkan intent parser internal.

Contoh pertanyaan yang saat ini didukung:

- `cek stok gula`
- `produk stok menipis`
- `produk terlaris bulan ini`
- `ringkasan penjualan minggu ini`
- `produk akan expired 30 hari`
- `sales per kasir bulan ini`
- `stok masuk keluar bulan ini`
- `riwayat stock movement bubuk matcha`

## Route Akses

- Login kasir: `/`
- Login admin: `/admin/login`
- Dashboard admin: `/dashboard_admin`
- Daftar produk kasir: `/list_product`
- Transaksi kasir: `/selling_product`
- Pending order: `/pending_selling_product`

Semua route utama admin dan kasir memakai middleware `auth`, `verified`, dan `role`.

## Instalasi

### 1. Install dependency

```bash
composer install
npm install
```

### 2. Siapkan file environment

Repo ini saat ini tidak menyertakan `.env.example`, jadi buat file `.env` secara manual sesuai environment lokal Anda.

Minimal sesuaikan bagian berikut:

- `APP_NAME`
- `APP_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `MAIL_*` bila ingin memakai email verification via mail server

Lalu generate app key:

```bash
php artisan key:generate
```

### 3. Migrasi database

```bash
php artisan migrate
```

### 4. Seed role, permission, dan user awal

Saat ini `DatabaseSeeder` belum otomatis memanggil seeder project, jadi jalankan manual:

```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=UserSeeder
```

### 5. Verifikasi email akun awal

Karena route utama memakai middleware `verified`, akun hasil `UserSeeder` perlu memiliki nilai `email_verified_at`.

Seeder user saat ini belum mengisi field tersebut, jadi untuk development lokal paling praktis adalah update manual di database, misalnya:

```sql
UPDATE users
SET email_verified_at = NOW()
WHERE email IN ('admin@gmail.com', 'cashier@gmail.com');
```

### 6. Link storage publik

Gambar produk dibaca dari `storage/assets/product`, jadi jalankan:

```bash
php artisan storage:link
```

### 7. Jalankan aplikasi

Untuk development:

```bash
php artisan serve
npm run dev
```

Atau build asset terlebih dahulu:

```bash
npm run build
```

## Akun Default

Setelah menjalankan `RolePermissionSeeder` dan `UserSeeder`, tersedia akun berikut:

- Admin
  - Email: `admin@gmail.com`
  - Password: `12345678`
- Cashier
  - Email: `cashier@gmail.com`
  - Password: `12345678`

Catatan: akun di atas tetap perlu dianggap belum aktif penuh sampai `email_verified_at` terisi.

## Struktur Tabel Utama

- `users`
- `categories`
- `products`
- `stock_movements`
- `carts`
- `pending_carts`
- `detail_pending_carts`
- `sales`
- `detail_sales`
- tabel role dan permission dari Spatie

## Catatan Penting

- README ini disusun berdasarkan implementasi yang ada saat ini, bukan asumsi flow ideal.
- Route registrasi bawaan Laravel Breeze masih tersedia, tetapi flow registrasi belum sepenuhnya disesuaikan dengan skema role `admin/cashier`.
- `RegisteredUserController` masih mengarah ke route `dashboard`, sementara route tersebut belum didefinisikan khusus untuk project ini.
- Test bawaan Laravel masih ada, tetapi pada environment ini `php artisan test` gagal bila koneksi database MySQL tidak tersedia atau dibatasi.
- Mail driver default Laravel biasanya `log`, jadi bila ingin verifikasi email lewat email sungguhan, konfigurasi `MAIL_*` perlu disesuaikan.

## Perintah Berguna

```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=UserSeeder
php artisan storage:link
php artisan serve
npm run dev
```
