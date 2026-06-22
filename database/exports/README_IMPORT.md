# Cara Pull Project + Import Database POS CATcha

File database terbaru:

```bash
database/exports/pos_db_latest.sql
```

## 1. Pull kode terbaru

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/Skripsi/POS
git pull origin main
```

Kalau pertama kali clone:

```bash
git clone https://github.com/LuthfiMirza/poscatcha.git POS
cd POS
```

## 2. Install dependency

```bash
composer install
npm install
```

## 3. Siapkan `.env`

Kalau belum ada `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Pastikan isi database seperti ini:

```env
APP_TIMEZONE=Asia/Jakarta
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_db
DB_USERNAME=root
DB_PASSWORD=
```

## 4. Buat database kosong

Buka phpMyAdmin, buat database:

```sql
CREATE DATABASE IF NOT EXISTS pos_db;
```

Atau lewat terminal Mac XAMPP:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS pos_db;"
```

## 5. Import database

Mac XAMPP:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root pos_db < database/exports/pos_db_latest.sql
```

Windows XAMPP, paling mudah lewat phpMyAdmin:

1. Buka `http://localhost/phpmyadmin`
2. Pilih database `pos_db`
3. Klik tab `Import`
4. Pilih file `database/exports/pos_db_latest.sql`
5. Klik `Go`

## 6. Jalankan setup Laravel

```bash
php artisan storage:link
php artisan optimize:clear
```

## 7. Jalankan aplikasi

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

## Akun Login

Admin:

```text
URL: /admin/login
Email: admin@gmail.com
Password: 12345678
```

Kasir:

```text
URL: /
Email: ariz@gmail.com
Password: 12345678
```

Kasir lain:

```text
URL: /
Email: koko@gmail.com
Password: 12345678
```

## Catatan Penting

- Jangan jalankan `php artisan migrate:fresh` setelah import, karena itu akan menghapus data contoh.
- Kalau setelah import gambar tidak muncul, pastikan folder `public/assets/product` ikut ter-pull.
- Kalau jam tidak sesuai, pastikan `.env` berisi `APP_TIMEZONE=Asia/Jakarta` lalu jalankan `php artisan optimize:clear`.
