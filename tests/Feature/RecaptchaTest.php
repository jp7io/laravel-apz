<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** The rule gating a real form, as opposed to the rule in isolation. */
class RecaptchaTest extends TestCase
{
    use RefreshDatabase;

    private const AUTHOR = ['name' => 'Author1', 'email' => 'debug@jp7.com.br'];

    public function test_a_form_is_refused_when_google_rejects_the_response(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false])]);

        $this->postJson(route('authors.store'), self::AUTHOR + ['g-recaptcha-response' => 'a-response'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('g-recaptcha-response');

        $this->assertDatabaseEmpty('authors');
    }

    public function test_a_form_is_refused_when_the_box_was_not_ticked(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);

        $this->postJson(route('authors.store'), self::AUTHOR)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('g-recaptcha-response');
    }

    public function test_a_form_goes_through_when_google_accepts(): void
    {
        config(['services.recaptcha.secret_key' => 'secret']);
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true])]);

        $this->postJson(route('authors.store'), self::AUTHOR + ['g-recaptcha-response' => 'a-response'])
            ->assertCreated();

        $this->assertDatabaseHas('authors', self::AUTHOR);
    }

    public function test_the_widget_renders_only_when_a_site_key_is_configured(): void
    {
        $this->get(route('authors.create'))->assertDontSee('g-recaptcha', false);

        config(['services.recaptcha.site_key' => 'a-site-key']);

        $this->get(route('authors.create'))
            ->assertSee('g-recaptcha', false)
            ->assertSee('a-site-key', false);
    }
}
