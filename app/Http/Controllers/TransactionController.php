<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    
    {

    $products = Product::where('stock', '>', 0)->paginate(12);

    return view('pos.create', ['products' => $products]);

    }

    public function store(StoreTransactionRequest $request)
    {
    $validated = $request->validated();

    // 1. Gabungkan item dengan product_id yang sama
    $mergedItems = [];
    foreach ($validated['items'] as $item) {
        $productId = $item['product_id'];

        if (isset($mergedItems[$productId])) {
            // Tambahkan qty jika produk sudah ada di keranjang
            $mergedItems[$productId]['qty'] += $item['qty'];
        } else {
            // Tambahkan sebagai baris baru jika produk belum ada
            $mergedItems[$productId] = $item;
        }
    }

    DB::transaction(function () use ($mergedItems) {
        $transaction = Transaction::create([
            'user_id' => 1,
            'total'   => 0,
        ]);

        $total = 0;

        // 2. Simpan item yang sudah digabungkan
        foreach ($mergedItems as $item) {
            $product  = Product::findOrFail($item['product_id']);
            $subtotal = $product->price * $item['qty'];
            $total   += $subtotal;

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $product->id,
                'qty'            => $item['qty'],
                'subtotal'       => $subtotal,
            ]);
        }

        $transaction->update(['total' => $total]);
    });

    return redirect()
        ->route('pos.create')
        ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function index()
    {
    $transactions = Transaction::with(['details.product', 'user'])
        ->latest()
        ->paginate(15);

    return view('transactions.index', compact('transactions'));
    }

    public function show(string $id)
    {
        return "Detail transaksi #{$id}";
    }
}