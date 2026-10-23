<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

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

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

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

# Jobsheet 7 
## Skenario Uji Manual Berdasarkan Peran

### 1. Admin

**Tujuan:** Memastikan admin dapat mengakses halaman produk dan kategori.

**Langkah pengujian:**
1. Login menggunakan email `admin@pos.test` dan kata sandi `password`.
2. Buka halaman `/products`.
3. Buka halaman `/categories`.

**Hasil yang diharapkan:**
- Login berhasil.
- Halaman produk dan kategori dapat dibuka tanpa error 403.
- Menu Produk dan Kategori tampil pada navigasi.

### 2. Kasir

**Tujuan:** Memastikan kasir dapat menggunakan halaman kasir, tetapi tidak dapat mengakses halaman admin.

**Langkah pengujian:**
1. Login menggunakan email `kasir@pos.test` dan kata sandi `password`.
2. Buka halaman `/pos`.
3. Buka halaman `/products`.
4. Buka halaman `/categories`.

**Hasil yang diharapkan:**
- Login berhasil.
- Halaman kasir dapat dibuka.
- Halaman produk dan kategori menampilkan error 403.
- Menu Produk dan Kategori tidak tampil pada navigasi.

### 3. Manager

**Tujuan:** Memastikan manager dapat mengakses transaksi, tetapi tidak dapat mengakses halaman produk.

**Langkah pengujian:**
1. Login menggunakan akun dengan peran `manager`.
2. Buka halaman `/transactions`.
3. Buka halaman `/products`.

**Hasil yang diharapkan:**
- Login berhasil.
- Halaman transaksi dapat dibuka.
- Halaman produk menampilkan error 403.

### 4. Tamu (Belum Login)

**Tujuan:** Memastikan tamu tidak dapat mengakses halaman yang memerlukan autentikasi.

**Langkah pengujian:**
1. Logout dari aplikasi.
2. Buka halaman `/products`.
3. Buka halaman `/pos`.
4. Buka halaman `/login`.

**Hasil yang diharapkan:**
- Halaman produk dan kasir mengarahkan pengguna ke halaman login.
- Halaman login dapat dibuka oleh tamu.
- Tamu tidak dapat mengakses halaman yang memerlukan autentikasi sebelum login.