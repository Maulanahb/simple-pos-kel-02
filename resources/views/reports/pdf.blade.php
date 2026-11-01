<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 14px;
            margin-top: 24px;
            margin-bottom: 6px;
        }

        p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #999;
            padding: 5px 8px;
            text-align: left;
        }

        th {
            background-color: #eee;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .text-muted {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>

    <p>Periode {{ $from }} s.d. {{ $to }}</p>

    @if (!empty($category))
        <p>Kategori: <strong>{{ $category->name }}</strong></p>
    @endif

    <p>
        Total penjualan:
        <strong>Rp {{ number_format($totalPenjualan) }}</strong>
        dari {{ $jumlahTransaksi }} transaksi
    </p>

    <h2>10 Produk Terlaris</h2>
    <table>
        <thead>
            <tr>
                <th class="center" style="width: 35px;">No.</th>
                <th>Nama Produk</th>
                <th class="right" style="width: 100px;">Jumlah Terjual</th>
                <th class="right" style="width: 130px;">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produkTerlaris as $index => $produk)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $produk->name }}</td>
                    <td class="right">{{ number_format($produk->total_terjual) }}</td>
                    <td class="right">Rp {{ number_format($produk->total_pendapatan) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center text-muted">
                        Belum ada data penjualan produk pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>50 Transaksi Terbaru pada Periode Ini</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 45px;">ID</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th class="right" style="width: 120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $t->user->name }}</td>
                    <td class="right">
                        Rp {{ number_format($t->total) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center text-muted">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>