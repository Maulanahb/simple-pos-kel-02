<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi Sistem - Simple POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 flex flex-col justify-between p-4 md:p-8">
    <div class="max-w-3xl mx-auto w-full my-auto py-8">
        <!-- Card Utama -->
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
            <!-- Header Section -->
            <div class="bg-slate-900 text-white p-6 md:p-8 text-center relative">
                <span class="inline-block bg-blue-500/20 text-blue-300 text-xs font-semibold px-3 py-1 rounded-full border border-blue-400/30 mb-3">
                    Halaman Publik / Tamu
                </span>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight">Simple POS Kafe</h1>
                <p class="text-slate-300 text-sm md:text-base mt-2 max-w-xl mx-auto">
                    Aplikasi kasir dan manajemen penjualan berbasis web dengan kontrol hak akses bertingkat (Role-Based Access Control).
                </p>
            </div>

            <!-- Content Section -->
            <div class="p-6 md:p-8 space-y-6">
                <!-- Tentang Sistem -->
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 mb-2 flex items-center gap-2">
                        <span>📌</span> Tentang Aplikasi
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Halaman ini dapat diakses oleh tamu (guest) yang belum melakukan login. Pengguna yang sudah masuk ke sistem secara otomatis akan dialihkan langsung ke halaman utama kasir (<code class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200">/pos</code>).
                    </p>
                </div>

                <!-- Fitur Utama -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="text-xl mb-2">🛒</div>
                        <h3 class="font-semibold text-slate-900 text-sm">Kasir & POS</h3>
                        <p class="text-xs text-slate-500 mt-1">Pencatatan transaksi cepat dengan keranjang dinamis dan validasi stok produk otomatis.</p>
                    </div>

                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="text-xl mb-2">📦</div>
                        <h3 class="font-semibold text-slate-900 text-sm">Produk & Kategori</h3>
                        <p class="text-xs text-slate-500 mt-1">Pengelolaan katalog produk, penyesuaian harga, serta manajemen stok barang (khusus Admin).</p>
                    </div>

                    <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="text-xl mb-2">📑</div>
                        <h3 class="font-semibold text-slate-900 text-sm">Riwayat Transaksi</h3>
                        <p class="text-xs text-slate-500 mt-1">Pencatatan rekap invoice penjualan dan detail item pesanan yang dapat dipantau tim kafe.</p>
                    </div>
                </div>

                <!-- Hak Akses Role -->
                <div class="pt-2">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3 flex items-center gap-2">
                        <span>👥</span> Peran Pengguna (RBAC)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                        <div class="p-3 rounded-lg border border-indigo-200 bg-indigo-50/50">
                            <span class="font-bold text-indigo-900 block text-sm">Admin</span>
                            <p class="text-indigo-700 mt-1">Akses penuh ke semua modul: Kasir, Riwayat Transaksi, Manajemen Produk & Kategori.</p>
                        </div>
                        <div class="p-3 rounded-lg border border-emerald-200 bg-emerald-50/50">
                            <span class="font-bold text-emerald-900 block text-sm">Kasir</span>
                            <p class="text-emerald-700 mt-1">Akses ke modul pembuatan transaksi kasir (/pos) dan melihat riwayat transaksi.</p>
                        </div>
                        <div class="p-3 rounded-lg border border-amber-200 bg-amber-50/50">
                            <span class="font-bold text-amber-900 block text-sm">Manager</span>
                            <p class="text-amber-700 mt-1">Akses monitoring rekap riwayat transaksi (/transactions) tanpa akses manajemen produk.</p>
                        </div>
                    </div>
                </div>

                <!-- Akun Uji Coba -->
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs text-slate-600">
                    <p class="font-medium text-slate-700 mb-2">Akun Demo yang Tersedia:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 font-mono">
                        <div>
                            <span class="text-slate-400">Admin:</span><br>
                            <span class="text-slate-800">admin@pos.test</span>
                        </div>
                        <div>
                            <span class="text-slate-400">Kasir:</span><br>
                            <span class="text-slate-800">kasir@pos.test</span>
                        </div>
                        <div>
                            <span class="text-slate-400">Manager:</span><br>
                            <span class="text-slate-800">manager@pos.test</span>
                        </div>
                    </div>
                    <p class="text-slate-400 mt-2 font-sans">Kata sandi default semua akun: <code class="bg-white px-1 py-0.5 rounded border border-slate-300">password</code></p>
                </div>

                <!-- Call to Action -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100">
                    <p class="text-xs text-slate-500">
                        Sudah memiliki akun? Silakan masuk untuk mulai bertransaksi.
                    </p>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Masuk ke Aplikasi &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-xs text-slate-400 pb-2">
        Simple POS &copy; {{ date('Y') }} &bull; Praktikum Pemrograman Web Lanjut (Pertemuan 7)
    </div>
</body>
</html>
