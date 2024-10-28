<?php

namespace App\Rules\Panel\Transmission;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DecimalRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (str_contains($value, '.')) {
            $decimal = explode('.', $value);
            if (strlen($decimal[1]) > 1)
                $fail('شمامجاز هستید تایک رقم اعشار پیش بروید');
        }
    }
}
