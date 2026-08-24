<?php

namespace Tests\Unit;

use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RecaptchaRuleTest extends TestCase
{
    public function test_it_passes_when_no_secret_key_is_configured(): void
    {
        config(['services.recaptcha.secret_key' => null]);
        Http::fake();

        $this->assertTrue($this->validate(null));
        Http::assertNothingSent();
    }

    public function test_it_fails_when_configured_and_the_box_was_not_ticked(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);

        $this->assertFalse($this->validate(null));
    }

    public function test_it_passes_when_google_accepts_the_response(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true])]);

        $this->assertTrue($this->validate('a-response'));
    }

    public function test_it_fails_when_google_rejects_the_response(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false])]);

        $this->assertFalse($this->validate('a-response'));
    }

    private function validate(?string $value): bool
    {
        return Validator::make(
            ['g-recaptcha-response' => $value],
            ['g-recaptcha-response' => [new Recaptcha]],
        )->passes();
    }
}
