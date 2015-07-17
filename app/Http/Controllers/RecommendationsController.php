<?php

namespace App\Http\Controllers;

use Request;
use App\Http\Requests\RecommendationRequest;
use App\Http\Controllers\Controller;
use App\Article;
use App\Mailers\ArticleMailer;

class RecommendationsController extends Controller
{
    public function store(RecommendationRequest $request, Article $article, ArticleMailer $mailer)
    {
        $mailer->recommendTo($request->input('email'), $article);

        return ['Your recommendation was sent.'];
    }
}
