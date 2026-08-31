<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'           => 'required|exists:customers,id',
            'rental_date'           => 'required|date',
            'duration_days'         => 'required|integer|min:1',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.product_size'  => 'required|in:XS,S,M,L,XL,XXL,3XL,4XL',
            'items.*.quantity'      => 'required|integer|min:1',
            'discount'              => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string',
            'payment_method'        => 'required|string|in:cash,qris,transfer',
            'guarantee_type'        => 'required|string|in:ktp,sim,deposit,custom',
            'guarantee_deposit'     => 'nullable|numeric|min:0',
            'guarantee_notes'       => 'nullable|string|max:500',
            'guarantee_id_photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            $aggregated = [];
            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                if ($productId <= 0 || $quantity <= 0) {
                    continue;
                }
                $aggregated[$productId] = ($aggregated[$productId] ?? 0) + $quantity;
            }

            if (empty($aggregated)) {
                return;
            }

            $productIds = array_keys($aggregated);
            $products = Product::whereIn('id', $productIds)->get(['id', 'name', 'stock_available']);

            foreach ($products as $product) {
                $requested = $aggregated[$product->id] ?? 0;
                if ($requested > (int) $product->stock_available) {
                    $validator->errors()->add(
                        'items',
                        "Stok produk {$product->name} tidak mencukupi (tersedia: {$product->stock_available}, diminta: {$requested})."
                    );
                }
            }

            $guaranteeType = $this->input('guarantee_type');

            if ($guaranteeType === 'deposit') {
                $deposit = $this->input('guarantee_deposit');
                if (!$deposit || (float) $deposit <= 0) {
                    $validator->errors()->add('guarantee_deposit', 'Jumlah deposit wajib diisi untuk jenis jaminan Deposit Uang.');
                }
            }

            if ($guaranteeType === 'ktp' || $guaranteeType === 'sim') {
                if (!$this->hasFile('guarantee_id_photo')) {
                    $validator->errors()->add('guarantee_id_photo', 'Foto identitas wajib diupload untuk jenis jaminan ' . ($guaranteeType === 'ktp' ? 'KTP' : 'SIM') . '.');
                }
            }

            if ($guaranteeType === 'custom') {
                $notes = trim((string) ($this->input('guarantee_notes') ?? ''));
                if ($notes === '') {
                    $validator->errors()->add('guarantee_notes', 'Deskripsi jaminan custom wajib diisi.');
                }
            }
        });
    }
}
