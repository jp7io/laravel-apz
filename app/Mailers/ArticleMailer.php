<?php

namespace App\Mailers;

use Mail;
use App\Article;

class ArticleMailer
{
    public function recommendTo($email, Article $article)
    {
        Mail::send('emails.article', ['article' => $article], function ($message) use ($email) {
            $message->to($email)->subject('Recommendation');
        });
    }
}
