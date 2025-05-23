<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InEnum implements ValidationRule
{
    public function __construct(private string $enum)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->enum::cases() as $case) {
            if ($case->value === $value) {
                return;
            }
        }

        $fail('The :attribute is not in the enum.');
    }
}
