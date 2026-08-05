<?php

namespace App\Http\Requests;

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
            'guarantee_id_number'   => 'nullable|string|max:50',
            'guarantee_notes'       => 'nullable|string|max:500',
            'guarantee_id_photo'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
