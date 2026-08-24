<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecommendationRequest;
use App\Mail\ArticleRecommendation;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class RecommendationsController extends Controller
{
    public function create(Article $article): View
    {
        return view('recommendations.create', compact('article'));
    }

    public function store(RecommendationRequest $request, Article $article): JsonResponse|RedirectResponse
    {
        Mail::to($request->string('email')->value())->queue(new ArticleRecommendation($article));
        session()->flash('flash_message', 'Your recommendation was sent');

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Your recommendation was sent']);
        }

        return to_route('articles.index');
    }
}
