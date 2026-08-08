<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SocialProfile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        $validUrl = filter_var($value, FILTER_VALIDATE_URL)
            && in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);
        $validUsername = preg_match('/^@?[\pL\pN._-]+$/u', (string) $value) === 1;

        if (! $validUrl && ! $validUsername) {
            $fail('Enter a valid profile URL or @username.');
        }
    }
}
