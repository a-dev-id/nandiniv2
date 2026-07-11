<?php

namespace App\Http\Requests\Voucher;

use Illuminate\Foundation\Http\FormRequest;

class AddVoucherToCartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'purchase_for' => ['required', 'in:self,gift'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'recipient_email' => ['required', 'email', 'max:191'],
            'personal_message' => ['nullable', 'string', 'max:800'],
            'delivery_method' => ['required', 'in:email'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
