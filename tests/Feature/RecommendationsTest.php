<?php

namespace Tests\Feature;

use App\Mail\ArticleRecommendation;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RecommendationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_queues_the_recommendation(): void
    {
        Mail::fake();
        $article = Article::factory()->create();

        $this->post(route('articles.recommendations.store', $article), ['email' => 'friend@jp7.com.br'])
            ->assertRedirect(route('articles.index'))
            ->assertSessionHas('flash_message', 'Your recommendation was sent');

        Mail::assertQueued(
            ArticleRecommendation::class,
            fn (ArticleRecommendation $mail): bool => $mail->hasTo('friend@jp7.com.br')
                && $mail->article->is($article),
        );
    }

    public function test_it_rejects_an_invalid_email(): void
    {
        Mail::fake();
        $article = Article::factory()->create();

        $this->postJson(route('articles.recommendations.store', $article), ['email' => 'debug'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        Mail::assertNothingQueued();
    }

    public function test_the_email_links_back_to_the_article(): void
    {
        $article = Article::factory()->create();

        $rendered = (new ArticleRecommendation($article))->render();

        $this->assertStringContainsString(route('articles.show', $article), $rendered);
        $this->assertStringContainsString($article->title, $rendered);
    }
}
