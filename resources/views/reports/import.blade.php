@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="bg-white border rounded-md p-5 shadow-sm lg:col-span-1">
            <h2 class="text-sm font-semibold text-slate-800 mb-3">Upload File CSV</h2>

            <form
                method="POST"
                action="{{ route('import.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                <label class="block mb-3">
                    <span class="text-sm font-medium">File CSV</span>

                    <input
                        type="file"
                        name="csv"
                        accept=".csv"
                        class="mt-1 block w-full text-sm"
                        required
                    >

                    @error('csv')
                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </label>

                <p class="text-xs text-slate-500 mb-4">
                    Column yang dibutuhkan:
                    <code>name</code>,
                    <code>category_id</code>,
                    <code>price</code>.
                </p>

                <button
                    type="submit"
                    class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-800"
                >
                    Import
                </button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-800">
                    Daftar Baris Gagal Di-import ({{ $importErrors->total() }} error)
                </h2>

                @if ($importErrors->total() > 0)
                    <form method="POST" action="{{ route('import.errors.clear') }}" onsubmit="return confirm('Hapus seluruh riwayat baris gagal?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 hover:underline">
                            Bersihkan Riwayat
                        </button>
                    </form>
                @endif
            </div>

            @if ($importErrors->isEmpty())
                <div class="bg-white border border-dashed rounded-md p-6 text-center text-sm text-slate-500">
                    Belum ada baris yang tercatat gagal di-import.
                </div>
            @else
                <div class="bg-white border rounded-md overflow-hidden shadow-sm">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 border-b text-xs text-slate-600 uppercase">
                            <tr>
                                <th class="px-4 py-2.5 w-20">Baris</th>
                                <th class="px-4 py-2.5">Pesan Validasi / Error</th>
                                <th class="px-4 py-2.5">Data Mentah</th>
                                <th class="px-4 py-2.5 w-32">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($importErrors as $error)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-red-600 text-xs">
                                        Row #{{ $error->row_number }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-700">
                                        <span class="inline-block bg-red-50 text-red-700 border border-red-200 rounded px-2 py-0.5">
                                            {{ $error->error_message }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500 max-w-xs truncate" title="{{ $error->raw_data }}">
                                        {{ $error->raw_data ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                                        {{ $error->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $importErrors->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
