<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_authors(): void
    {
        $author = Author::factory()->create();

        $this->get(route('authors.index'))
            ->assertOk()
            ->assertSee($author->name);
    }

    public function test_it_serves_the_list_as_json(): void
    {
        $author = Author::factory()->create();

        $this->getJson(route('authors.index'))
            ->assertOk()
            ->assertJsonFragment(['name' => $author->name, 'email' => $author->email]);
    }

    public function test_it_creates_an_author_with_valid_attributes(): void
    {
        $attributes = ['name' => 'Author1', 'email' => 'debug@jp7.com.br'];

        $this->postJson(route('authors.store'), $attributes)->assertCreated();

        $this->assertDatabaseHas('authors', $attributes);
    }

    public function test_it_rejects_an_author_with_an_invalid_email(): void
    {
        $attributes = ['name' => 'Author2', 'email' => 'debug'];

        $this->postJson(route('authors.store'), $attributes)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseEmpty('authors');
    }

    public function test_it_updates_an_author_with_valid_attributes(): void
    {
        $author = Author::factory()->create();
        $attributes = ['name' => 'Updated Author1', 'email' => 'debug+updated@jp7.com.br'];

        $this->patchJson(route('authors.update', $author), $attributes)->assertOk();

        $this->assertDatabaseHas('authors', $attributes + ['id' => $author->id]);
    }

    public function test_it_rejects_an_update_with_an_invalid_email(): void
    {
        $author = Author::factory()->create();

        $this->patchJson(route('authors.update', $author), ['name' => 'Updated Author2', 'email' => 'debug'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseHas('authors', ['id' => $author->id, 'name' => $author->name]);
    }

    public function test_it_deletes_an_author(): void
    {
        $author = Author::factory()->create();

        $this->delete(route('authors.destroy', $author))->assertRedirect(route('authors.index'));

        $this->assertDatabaseEmpty('authors');
    }
}
