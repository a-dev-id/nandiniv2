<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * @return array<int, mixed>
     */
    public static function rules(): array
    {
        if (! self::enabled()) {
            return ['nullable'];
        }

        return ['required', new self()];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::enabled()) {
            return;
        }

        if (! is_string($value) || trim($value) === '') {
            $fail('Please complete the reCAPTCHA check.');

            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => (string) config('services.recaptcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);
        } catch (\Throwable) {
            $fail('We could not verify the reCAPTCHA check. Please try again.');

            return;
        }

        if (! (bool) $response->json('success')) {
            $fail('The reCAPTCHA check failed. Please try again.');
        }
    }

    private static function enabled(): bool
    {
        return (bool) config('services.recaptcha.enabled')
            && filled(config('services.recaptcha.site_key'))
            && filled(config('services.recaptcha.secret_key'));
    }
}
