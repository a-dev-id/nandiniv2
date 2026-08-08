<?php

namespace App\Http\Requests;

use App\Enums\AffiliatePaymentMethod;
use App\Enums\AffiliatePreferredCurrency;
use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAffiliatePaymentProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $affiliate = $this->user('affiliate');

        return $affiliate?->isApproved() === true
            && $affiliate->hasPermissionTo(Permission::AFFILIATE_PAYMENT_PROFILE_UPDATE_OWN);
    }

    public function rules(): array
    {
        $wise = $this->input('payment_method') === AffiliatePaymentMethod::Wise->value;
        $bank = $this->input('payment_method') === AffiliatePaymentMethod::BankTransfer->value;

        return [
            'payment_method' => ['required', Rule::enum(AffiliatePaymentMethod::class)],
            'preferred_currency' => ['required', Rule::enum(AffiliatePreferredCurrency::class)],
            'account_holder_name' => ['required', 'string', 'max:191'],
            'wise_email' => [Rule::requiredIf($wise), 'nullable', 'email:rfc', 'max:191'],
            'bank_name' => [Rule::requiredIf($bank), 'nullable', 'string', 'max:191'],
            'bank_account_name' => [Rule::requiredIf($bank), 'nullable', 'string', 'max:191'],
            'bank_account_number' => [Rule::requiredIf($bank), 'nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9 .\/-]+$/'],
            'bank_country' => [Rule::requiredIf($bank), 'nullable', 'string', 'max:100'],
            'swift_bic' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9-]+$/'],
        ];
    }
}
