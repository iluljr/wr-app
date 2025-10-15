# 📦 Aplikasi Generate WR
Aplikasi untuk Create Weakly Report Pekerjaan

# 🏗 Arsitektur Singkat

-   Arsitektur: Monolith Laravel 10 (web-based, mobile-responsive).
-   Bahasa/Runtime: PHP 8.2
-   DB: PostgreSQL (ERD & daftar tabel tersedia).

🌍 Domain & Lingkungan

Production
-

## 📋 Prasyarat

-   PHP 8.2.2 dengan ekstensi PDO (pgsql), mbstring, openssl, json, curl, zip
-   Composer 2.x
-   Node.js,
-   PostgreSQL
-   Akses Github : `https://github.com/iluljr/wr-app`
-   Docker & Docker Compose (mandatori di lingkungan perusahaan).

## 🚀 Instalasi

## Server DEV

1. Persiapan Clone project

    - 1.1 Buat folder project yang digunakan sebagai tempat menyimpan folder project website ( didalam /var/www/...)
    - 1.2 Atur konfigurasi akses server menuju folder project
    - 1.3 Clone repository (HTTPS only): `https://github.com/iluljr/wr-app`

2. Atur Konfigurasi Env

    - 2.1 Buat file environment dengan extention .env
    - 2.2 Atur environment untuk koneksi database, alamat mail host, dan konfigurasi lain yang dibutuhkan (Bisa copy-kan dari .env.example)
    - 2.3 pindahin wr_template.xlsx yang di depan ke folder storage/app/private/templates

3. Konfigurasi dan Jalankan Laravel
    - 3.1 Masuk ke wr_app
    - 3.2 `npm i`
    - 3.3 `composer update`
    - 3.4 `php artisan key:generate`
    - 3.5 `php artisan migrate` ( Migrate Database )
    - 3.6 `php artisan serve`
    - 3.7 `npm run dev`
    - 3.8 `php artisan queue:work`

## 🏃 Konfigurasi

1. Pastikan allow connection untuk database PostgreSQL ke Aplikasi.
2. Pastikan ekstensi PDO (pgsql), mbstring, openssl, json, curl, zip sudah di aktifkan di php.ini
