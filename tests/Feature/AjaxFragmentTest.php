<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The one route serves a full page and the body of the modal; only the request headers differ. */
class AjaxFragmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_normal_request_gets_the_whole_document(): void
    {
        Author::factory()->create();

        $this->get(route('articles.create'))
            ->assertOk()
            ->assertSee('<!DOCTYPE html>', false)
            ->assertSee('id="articles-form"', false);
    }

    public function test_an_ajax_request_gets_only_the_fragment(): void
    {
        Author::factory()->create();

        $this->get(route('articles.create'), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertSee('id="articles-form"', false)
            ->assertDontSee('<!DOCTYPE html>', false);
    }
}
