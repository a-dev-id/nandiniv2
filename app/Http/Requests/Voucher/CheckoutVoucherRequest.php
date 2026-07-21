<?php

namespace App\Http\Requests\Voucher;

use App\Support\InquiryOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutVoucherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'purchaser_first_name' => ['required', 'string', 'max:100'],
            'purchaser_last_name' => ['required', 'string', 'max:100'],
            'purchaser_email' => ['required', 'email', 'max:191'],
            'purchaser_phone' => ['nullable', 'string', 'max:40'],
            'billing_country_code' => ['required', 'string', 'size:2', Rule::in(array_keys(InquiryOptions::countryCodes()))],
        ];
    }
}
