<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // bawaan generator false -> semua request ditolak (403)
    }

    public function rules(): array
    {
        return [
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty'        => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            if (! is_array($items)) {
                return;
            }

            $quantities = [];
            foreach ($items as $item) {
                if (isset($item['product_id'], $item['qty'])) {
                    $productId = $item['product_id'];
                    $quantities[$productId] = ($quantities[$productId] ?? 0) + (int) $item['qty'];
                }
            }

            foreach ($quantities as $productId => $qty) {
                $product = Product::find($productId);
                if ($product && $qty > $product->stock) {
                    $validator->errors()->add(
                        'items',
                        "Stok produk '{$product->name}' tidak mencukupi (tersedia: {$product->stock}, diminta: {$qty})."
                    );
                }
            }
        });
    }
}