<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleRecommendation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Article $article) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Recommendation');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.article');
    }
}
