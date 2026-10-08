@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <h1 class="text-xl font-bold text-slate-900">Transaksi Kasir</h1>
    <span class="text-xs text-slate-500 bg-white px-2.5 py-1 rounded border border-gray-200">Mode POS</span>
</div>

<form method="POST" action="{{ route('transactions.store') }}" x-data="{
    cart: [],
    addToCart(id, name, price) {
        let existing = this.cart.find(item => item.id === id);
        if (existing) {
            existing.qty++;
        } else {
            this.cart.push({ id, name, price, qty: 1 });
        }
    },
    removeFromCart(id) {
        this.cart = this.cart.filter(item => item.id !== id);
    },
    subtotal() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }
}">
    @csrf

    <div class="flex flex-col md:flex-row gap-6 items-start">
        {{-- Kolom Kiri: Tabel Katalog Produk --}}
        <div class="w-full md:w-7/12 lg:w-2/3">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-700">Pilih Produk</span>
                <span class="text-xs text-slate-400">Klik baris atau tombol + untuk memasukkan ke keranjang</span>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs sm:text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-gray-200 text-slate-600">
                        <tr>
                            <th class="py-2.5 px-3 sm:px-4 font-semibold">Nama Produk</th>
                            <th class="py-2.5 px-2 font-semibold text-center w-20">Stok</th>
                            <th class="py-2.5 px-3 sm:px-4 font-semibold text-right w-28">Harga</th>
                            <th class="py-2.5 px-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($products as $product)
                            <tr class="hover:bg-blue-50/60 cursor-pointer transition select-none"
                                @click="addToCart({{ $product->id }}, @js($product->name), {{ $product->price }})">
                                <td class="py-2.5 px-3 sm:px-4">
                                    <span class="font-medium text-slate-800">{{ $product->name }}</span>
                                </td>
                                <td class="py-2.5 px-2 text-center">
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-slate-600 font-medium">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 sm:px-4 text-right font-semibold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($product->price) }}
                                </td>
                                <td class="py-2.5 px-3 text-center" @click.stop>
                                    <button type="button"
                                            @click="addToCart({{ $product->id }}, @js($product->name), {{ $product->price }})"
                                            class="bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 hover:border-transparent px-2.5 py-1 rounded text-xs font-medium transition">
                                        + Tambah
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($products, 'links'))
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Tabel Keranjang Kasir (Sticky) --}}
        <div class="w-full md:w-5/12 lg:w-1/3 md:sticky md:top-4">
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="pb-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800 text-sm">Keranjang Transaksi</h2>
                    <span class="text-xs text-slate-500 bg-gray-100 px-2 py-0.5 rounded-full" x-show="cart.length > 0" x-text="cart.length + ' item'"></span>
                </div>

                {{-- Notifikasi Error & Sukses Langsung di Dalam Panel Kasir --}}
                @if (session('success'))
                    <div class="mt-3 bg-green-50 text-green-700 p-3 rounded-md border border-green-200 text-xs flex items-center gap-2">
                        <span class="font-bold">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @error('items')
                    <div class="mt-3 bg-red-50 text-red-700 p-3 rounded-md border border-red-200 text-xs flex items-center gap-2">
                        <span class="font-bold">⚠️</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                {{-- Status Keranjang Kosong --}}
                <div x-show="cart.length === 0" class="py-8 text-center text-slate-400 text-sm">
                    <p class="font-medium text-slate-500">Keranjang Masih Kosong</p>
                    <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Tambah" pada tabel produk di samping.</p>
                </div>

                {{-- Tabel Item di Keranjang --}}
                <div x-show="cart.length > 0" class="my-3 overflow-x-auto max-h-64 overflow-y-auto border border-gray-100 rounded-md">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 border-b border-gray-200 text-slate-600 sticky top-0">
                            <tr>
                                <th class="py-2 px-2.5 font-semibold">Item</th>
                                <th class="py-2 px-1 font-semibold text-center w-20">Qty</th>
                                <th class="py-2 px-2.5 font-semibold text-right">Subtotal</th>
                                <th class="py-2 px-1 text-center w-7"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-for="(item, index) in cart" :key="item.id">
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2 px-2.5">
                                        <p class="font-medium text-slate-800 leading-snug" x-text="item.name"></p>
                                        <p class="text-[11px] text-slate-400" x-text="'@ Rp ' + Number(item.price).toLocaleString('id-ID')"></p>
                                        <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.id">
                                        <input type="hidden" :name="'items[' + index + '][qty]'" :value="item.qty">
                                    </td>
                                    <td class="py-2 px-1 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center border border-gray-200 rounded bg-white">
                                            <button type="button" @click="item.qty > 1 ? item.qty-- : removeFromCart(item.id)" class="px-1.5 py-0.5 hover:bg-gray-100 text-slate-600 font-bold leading-none">-</button>
                                            <span class="px-1.5 font-semibold text-slate-800" x-text="item.qty"></span>
                                            <button type="button" @click="item.qty++" class="px-1.5 py-0.5 hover:bg-gray-100 text-slate-600 font-bold leading-none">+</button>
                                        </div>
                                    </td>
                                    <td class="py-2 px-2.5 text-right font-semibold text-slate-900 whitespace-nowrap" x-text="'Rp ' + Number(item.price * item.qty).toLocaleString('id-ID')"></td>
                                    <td class="py-2 px-1 text-center">
                                        <button type="button" @click="removeFromCart(item.id)" class="text-slate-400 hover:text-red-600 font-bold text-sm leading-none" title="Hapus">×</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Rincian Total & Tombol Bayar --}}
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs sm:text-sm font-medium text-slate-600">Total Tagihan:</span>
                        <span class="text-base sm:text-lg font-bold text-slate-900">Rp <span x-text="Number(subtotal()).toLocaleString('id-ID')"></span></span>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-md transition text-sm flex items-center justify-center gap-2">
                        <span>Bayar Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection