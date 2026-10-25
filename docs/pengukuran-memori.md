\# Pengukuran Memori: get() vs lazyById()



Dataset: sekitar 52.500 row pada tabel `transactions`.



| Cara baca data | Peak memory (MB) |

|---|---:|

| `Transaction::lazyById()` | 102.0 |

| `Transaction::get()` | 102.0 |



\## Kesimpulan kelompok



Pengujian terhadap 52.500 data transaksi menunjukkan bahwa

kedua metode berhasil membaca seluruh data. Hasil pengukuran

awal mencatat peak memory sebesar 102,0 MB untuk masing-masing

metode. Hasil tersebut belum menunjukkan perbedaan penggunaan

memori yang diharapkan, sehingga pengukuran perlu dievaluasi

kembali.



Secara konsep, lazyById() mengambil data secara bertahap,

sedangkan get() memuat seluruh hasil query ke dalam memori.

Oleh karena itu, lazyById() berpotensi lebih hemat memori

untuk pemrosesan data dalam jumlah besar.

