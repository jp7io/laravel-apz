<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

/**
 * Verifies a reCAPTCHA v2 response against Google's siteverify endpoint.
 *
 * With no secret key configured the rule passes: a checkout of this repo has no keys, and
 * failing closed would make every form in the tutorial unsubmittable before step one.
 */
class Recaptcha implements ValidationRule
{
    /** Run even when the field is absent: omitting it entirely is how a bot would skip the check. */
    public bool $implicit = true;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secret = config('services.recaptcha.secret_key');

        if (blank($secret)) {
            return;
        }

        if (blank($value)) {
            $fail('Please confirm you are not a robot.');

            return;
        }

        $response = Http::asForm()
            ->timeout(5)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

        if (! $response->successful() || $response->json('success') !== true) {
            $fail('The reCAPTCHA verification failed. Please try again.');
        }
    }
}
