<?php

namespace App\Rules;

use App\Services\Site\Pwa\PWAService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ComCookieRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var PWAService $pwaService */
        $pwaService = app(PWAService::class);
        $externalId = request()->cookie($value);
        if (!$externalId || !$pwaService->getClient($externalId)) {
            $fail('Invalid external ID');
        }
    }
}
