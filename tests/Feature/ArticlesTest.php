<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_articles_with_their_author(): void
    {
        $author = Author::factory()->create();
        $article = Article::factory()->for($author)->create();

        $this->get(route('articles.index'))
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee($author->name);
    }

    public function test_it_serves_the_list_as_json(): void
    {
        $article = Article::factory()->create();

        $this->getJson(route('articles.index'))
            ->assertOk()
            ->assertJsonFragment(['title' => $article->title]);
    }

    public function test_it_creates_an_article_with_valid_attributes(): void
    {
        $author = Author::factory()->create();
        $attributes = ['title' => 'Article Title', 'content' => 'The new text!', 'author_id' => $author->id];

        $this->post(route('articles.store'), $attributes)
            ->assertRedirect(route('articles.index'))
            ->assertSessionHas('flash_message', 'Article was stored with success');

        $this->assertDatabaseHas('articles', $attributes);
    }

    public function test_it_rejects_a_title_shorter_than_three_characters(): void
    {
        $author = Author::factory()->create();

        $this->postJson(route('articles.store'), ['title' => '12', 'content' => '', 'author_id' => $author->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'content']);

        $this->assertDatabaseEmpty('articles');
    }

    public function test_it_rejects_an_author_that_does_not_exist(): void
    {
        $this->postJson(route('articles.store'), ['title' => 'A title', 'content' => 'Text', 'author_id' => 404])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('author_id');
    }

    public function test_it_shows_an_article(): void
    {
        $article = Article::factory()->create();

        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSee($article->title)
            ->assertSee($article->content);
    }

    public function test_it_updates_an_article(): void
    {
        $article = Article::factory()->create();

        $this->patch(route('articles.update', $article), [
            'title' => 'A better title',
            'content' => 'The updated text!',
            'author_id' => $article->author_id,
        ])->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => 'A better title']);
    }

    public function test_it_rejects_an_update_with_empty_content(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('articles.update', $article), [
            'title' => 'Invalid article',
            'content' => '',
            'author_id' => $article->author_id,
        ])->assertUnprocessable()->assertJsonValidationErrors('content');

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => $article->title]);
    }

    public function test_it_deletes_an_article(): void
    {
        $article = Article::factory()->create();

        $this->deleteJson(route('articles.destroy', $article))->assertNoContent();

        $this->assertDatabaseEmpty('articles');
    }

    public function test_deleting_an_author_takes_their_articles_with_them(): void
    {
        $article = Article::factory()->create();

        $this->delete(route('authors.destroy', $article->author))->assertRedirect(route('authors.index'));

        $this->assertDatabaseEmpty('articles');
    }
}
