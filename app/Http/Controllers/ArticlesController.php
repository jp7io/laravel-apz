<?php

namespace App\Http\Controllers;

use Request;
use App\Http\Requests\ArticleRequest;
use App\Article;
use App\Author;

class ArticlesController extends Controller
{
    public function index()
    {
        $articles = Article::all();

        if (Request::wantsJson()) {
            return $articles;
        } else {
            return view('articles.index', compact('articles'));
        }
    }

    public function create()
    {
        $article = new Article;
        $authors = Author::lists('name', 'id')->all();

        return view('articles.create', compact('article', 'authors'));
    }

    public function store(ArticleRequest $request)
    {
        $article = Article::create($request->all());
        session()->flash('flash_message', 'Article was stored with success');

        if (Request::wantsJson()) {
            return $article;
        } else {
            return redirect('articles');
        }
    }

    public function show(Article $article)
    {
        if (Request::wantsJson()) {
            return $article;
        } else {
            return view('articles.show', compact('article'));
        }
    }

    public function edit(Article $article)
    {
        $authors = Author::lists('name', 'id')->all();

        return view('articles.edit', compact('article', 'authors'));
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article->update($request->all());
        session()->flash('flash_message', 'Article was updated with success');

        if (Request::wantsJson()) {
            return $article;
        } else {
            return redirect('articles');
        }
    }

    public function destroy(Article $article)
    {
        $deleted = $article->delete();
        session()->flash('flash_message', 'Article was removed with success');

        if (Request::wantsJson()) {
            return (string) $deleted;
        } else {
            return redirect('articles');
        }
    }
}
