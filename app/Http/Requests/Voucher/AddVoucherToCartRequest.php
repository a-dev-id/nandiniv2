<?php

namespace App\Http\Requests\Voucher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddVoucherToCartRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'purchase_for' => ['required', 'in:self,gift'],
            'recipient_name' => [Rule::requiredIf(fn (): bool => $this->input('purchase_for') === 'gift'), 'nullable', 'string', 'max:120'],
            'recipient_email' => [Rule::requiredIf(fn (): bool => $this->input('purchase_for') === 'gift' && $this->input('delivery_method') === 'email'), 'nullable', 'email', 'max:191'],
            'personal_message' => ['nullable', 'string', 'max:800'],
            'gift_from' => ['nullable', 'string', 'max:120'],
            'delivery_method' => ['required', 'in:email,print_at_resort'],
            'hotel_note' => ['nullable', 'string', 'max:1000'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
