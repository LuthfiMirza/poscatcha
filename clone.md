# Panduan Clone & Setup POS CATcha

Panduan ini dibuat supaya project bisa langsung di-clone dan dijalankan dari **1 terminal** dengan cara copy-paste.

## Prasyarat

Pastikan sudah terinstall:

- PHP 8.2 atau lebih baru
- Composer
- Node.js + npm
- MySQL / MariaDB, contoh via XAMPP
- Git

## Setup Cepat: Copy & Paste di 1 Terminal

> Jika kamu pakai XAMPP di macOS, command ini akan clone project ke folder `htdocs`.

```bash
cd /Applications/XAMPP/xamppfiles/htdocs && \
rm -rf poscatcha && \
git clone https://github.com/LuthfiMirza/poscatcha.git poscatcha && \
cd poscatcha && \
composer install && \
npm install && \
php -r "file_exists('.env') || copy('.env.example', '.env');" 2>/dev/null || true && \
if [ ! -f .env ]; then cat > .env <<'ENV'
APP_NAME="POS CATcha"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://127.0.0.1:8002

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_db
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
ENV
fi && \
php artisan key:generate && \
php artisan storage:link && \
if command -v mysql >/dev/null 2>&1; then mysql -u root -e "CREATE DATABASE IF NOT EXISTS pos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"; else /Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS pos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"; fi && \
php artisan migrate --seed && \
npm run build && \
php artisan serve --host=127.0.0.1 --port=8002
```

Setelah command selesai, buka:

```text
http://127.0.0.1:8002
```

## Jika Database Belum Ada

Kalau muncul error database `Unknown database 'pos_db'`, buat database dulu:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS pos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Lalu jalankan ulang dari folder project:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/poscatcha && \
php artisan migrate --seed && \
npm run build && \
php artisan serve --host=127.0.0.1 --port=8002
```

## Login Aplikasi

Cek akun default dari seeder project di:

```text
database/seeders/UserSeeder.php
```

Jika akun belum ada, jalankan:

```bash
php artisan db:seed
```

## Menjalankan Project Setelah Setup Pertama

Untuk menjalankan ulang project di hari berikutnya:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/poscatcha && \
php artisan serve --host=127.0.0.1 --port=8002
```

Jika sedang development frontend, jalankan terminal kedua:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/poscatcha && \
npm run dev
```

## Perintah Maintenance

Clear cache Laravel:

```bash
php artisan optimize:clear
```

Jalankan migration terbaru:

```bash
php artisan migrate
```

Build asset production:

```bash
npm run build
```

Jalankan test:

```bash
php artisan test
```

## Catatan Penting

- Project memakai Laravel 11, Livewire 3, MySQL, dan Spatie Permission.
- Fitur **Admin Chatbot** tetap aktif lewat floating widget di halaman admin.
- Halaman **Audit Chatbot Logs** sengaja tidak ditampilkan.
- Fitur **Pending Sales / Hold Order** sudah tidak digunakan.
- Saat cashier menutup shift, sistem otomatis logout untuk mencegah kasir berikutnya memakai akun yang sama.
- Untuk deployment/demo publik, ubah `.env` menjadi `APP_DEBUG=false`.
