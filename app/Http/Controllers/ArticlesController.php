<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Author;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticlesController extends Controller
{
    /** @return View|Collection<int, Article> */
    public function index(Request $request): View|Collection
    {
        $articles = Article::with('author')->get();

        if ($request->wantsJson()) {
            return $articles;
        }

        return view('articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('articles.create', [
            'article' => new Article,
            'authors' => Author::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(ArticleRequest $request): Article|RedirectResponse
    {
        $article = Article::create($request->validated());
        session()->flash('flash_message', 'Article was stored with success');

        if ($request->wantsJson()) {
            return $article;
        }

        return to_route('articles.index');
    }

    public function show(Request $request, Article $article): View|Article
    {
        if ($request->wantsJson()) {
            return $article;
        }

        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        return view('articles.edit', [
            'article' => $article,
            'authors' => Author::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(ArticleRequest $request, Article $article): Article|RedirectResponse
    {
        $article->update($request->validated());
        session()->flash('flash_message', 'Article was updated with success');

        if ($request->wantsJson()) {
            return $article;
        }

        return to_route('articles.index');
    }

    public function destroy(Request $request, Article $article): Response|RedirectResponse
    {
        $article->delete();
        session()->flash('flash_message', 'Article was removed with success');

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return to_route('articles.index');
    }
}
