<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;
use App\Rules\SocialProfile;
use App\Services\Affiliate\AffiliateProfileNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StoreAffiliateRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ! auth('affiliate')->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge(app(AffiliateProfileNormalizer::class)->normalize($this->all()));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:affiliates,email'],
            'phone_whatsapp' => ['required', 'string', 'max:50', 'regex:/^[+0-9\s()\-]+$/'],
            'instagram' => ['nullable', 'string', 'max:255', new SocialProfile],
            'facebook' => ['nullable', 'string', 'max:255', new SocialProfile],
            'tiktok' => ['nullable', 'string', 'max:255', new SocialProfile],
            'x' => ['nullable', 'string', 'max:255', new SocialProfile],
            'threads' => ['nullable', 'string', 'max:255', new SocialProfile],
            'password' => ['required', 'confirmed', Password::min(8)],
            'g-recaptcha-response' => Recaptcha::rules(),
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! app(AffiliateProfileNormalizer::class)->hasSocialProfile($this->all())) {
                $validator->errors()->add('social_profiles', 'Add at least one social-media profile.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'An account already uses this email. Please log in or contact Nandini for help.',
            'phone_whatsapp.regex' => 'Enter a valid international phone or WhatsApp number.',
        ];
    }
}
