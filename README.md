# Sistem Penginapan Pesantren Mahasiswa

Aplikasi web untuk mengelola sistem penginapan di Pesantren Mahasiswa KH. Mas Mansur.

## 🎯 Fitur Utama

- **Dashboard Admin**: Statistik kamar tersedia, kamar kosong, dan jumlah pengunjung
- **Manajemen Kamar**: CRUD data kamar penginapan
- **Manajemen Pengunjung**: CRUD data pengunjung/tamu
- **Booking Corporate**: Form pemesanan untuk grup/korporat
- **Autentikasi**: Login admin dengan session

## 🛠️ Teknologi

- **Backend**: Laravel 11.x
- **Frontend**: Blade Templates + Bootstrap 5.3.3
- **Database**: MySQL
- **Server**: MAMP (macOS)

## 🚀 Quick Start

**Login Credentials:**
- Email: `admin@example.com`
- Password: `password123`

**Database sudah di-seed dengan:**
- 1 Admin user
- 6 Sample kamar (mix single, double, suite)
- 3 Sample pengunjung

## 📋 Instalasi Lengkap

```bash
# 1. Masuk ke direktori MAMP
cd /Applications/MAMP

# 2. Clone repository (jika dari git)
git clone <repository-url> penginapan
cd penginapan

# 3. Install dependencies
composer install

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_DATABASE=penginapan
# DB_USERNAME=root
# DB_PASSWORD=root

# 6. Buat database
mysql -u root -p -e "CREATE DATABASE penginapan;"

# 7. Migrasi dan seed
php artisan migrate:fresh --seed

# 8. Jalankan server
php artisan serve
# Akses: http://localhost:8000
```

## 📁 Struktur Fitur

### 🏠 Dashboard (`/admin/dashboard`)
- Kartu statistik kamar tersedia (%)
- Kartu statistik kamar kosong (%)  
- Kartu jumlah pengunjung (angka)
- Background dengan blob shapes

### 🛏️ Data Kamar (`/admin/kamar`)
- List semua kamar dengan nomor, jenis, gedung, harga, fasilitas, status
- Hapus kamar (dengan konfirmasi)
- Tombol "Kembali" ke dashboard

### 👥 Data Pengunjung (`/admin/pengunjung`)
- List pengunjung dengan nama, no identitas, jenis tamu, check-in/out, nomor kamar
- Hapus pengunjung (dengan konfirmasi)
- Tombol "Kembali" ke dashboard

### 🔐 Login (`/login`)
- Two-panel layout (ilustrasi + form)
- Email dan password validation
- Redirect ke dashboard setelah sukses

## 🎨 Desain UI

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
