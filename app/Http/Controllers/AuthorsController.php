<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Models\Author;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorsController extends Controller
{
    /** @return View|Collection<int, Author> */
    public function index(Request $request): View|Collection
    {
        $authors = Author::orderBy('name')->get();

        if ($request->wantsJson()) {
            return $authors;
        }

        return view('authors.index', compact('authors'));
    }

    public function create(): View
    {
        return view('authors.create', ['author' => new Author]);
    }

    public function store(AuthorRequest $request): Author|RedirectResponse
    {
        $author = Author::create($request->validated());
        session()->flash('flash_message', 'Author was stored with success');

        if ($request->wantsJson()) {
            return $author;
        }

        return to_route('authors.index');
    }

    public function show(Request $request, Author $author): View|Author
    {
        if ($request->wantsJson()) {
            return $author;
        }

        return view('authors.show', compact('author'));
    }

    public function edit(Author $author): View
    {
        return view('authors.edit', compact('author'));
    }

    public function update(AuthorRequest $request, Author $author): Author|RedirectResponse
    {
        $author->update($request->validated());
        session()->flash('flash_message', 'Author was updated with success');

        if ($request->wantsJson()) {
            return $author;
        }

        return to_route('authors.index');
    }

    public function destroy(Request $request, Author $author): Response|RedirectResponse
    {
        $author->delete();
        session()->flash('flash_message', 'Author was removed with success');

        if ($request->wantsJson()) {
            return response()->noContent();
        }

        return to_route('authors.index');
    }
}
