# Pengukuran Memori: get() vs lazyById()

Dataset: sekitar 52.500 row pada tabel `transactions`.

| Cara baca data | Peak memory (MB) |
|---|---:|
| `Transaction::lazyById()` | 52.7 |
| `Transaction::get()` | 111.1 |

## Kesimpulan kelompok

Pengujian dilakukan pada 52.502 baris data transaksi:
1. `Transaction::lazyById()` mencatat penggunaan peak memory sebesar **52,7 MB**. Hal ini terjadi karena `lazyById()` mengambil data dari database secara bertahap per-chunk kecil menggunakan *LazyCollection* (PHP Generator), sehingga model Eloquent tidak ditumpuk sekaligus di memori RAM.
2. `Transaction::get()` mencatat penggunaan peak memory sebesar **111,1 MB** (lebih dari 2 kali lipat dibanding `lazyById()`). Hal ini disebabkan `get()` menginstansiasi dan memuat seluruh 52.502 model transaksi ke dalam memori secara bersamaan.
3. Seiring bertambahnya volume data di masa mendatang, `get()` berisiko menabrak `memory_limit` PHP (*Out of Memory*), sedangkan `lazyById()` menjaga konsumsi memori tetap stabil dan aman untuk dataset berukuran besar.
