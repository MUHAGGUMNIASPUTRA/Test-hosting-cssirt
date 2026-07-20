<?php

// Tujuan: Validasi Cloudflare Turnstile token via siteverify endpoint.
// Caller: LoginRequest, IncidentController::store/search.
// Side Effects: Outbound HTTP POST ke Cloudflare siteverify API.

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail(trans('auth.turnstile_failed'));

            return;
        }

        try {
            $response = Http::asForm()->post(
                'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]
            );

            if (! $response->json('success')) {
                $fail(trans('auth.turnstile_failed'));
            }
        } catch (\Exception $e) {
            Log::warning('turnstile.verify_failed', [
                'event' => 'turnstile.verify_failed',
                'error' => $e->getMessage(),
            ]);
            $fail(trans('auth.turnstile_failed'));
        }
    }
}
