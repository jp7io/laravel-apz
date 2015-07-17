<?php

namespace App\Http\Controllers;

use Request;
use App\Http\Requests\AuthorRequest;
use App\Http\Controllers\Controller;
use App\Author;

class AuthorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $authors = Author::all();

        if (Request::wantsJson()) {
            return $authors;
        } else {
            return view('authors.index', compact('authors'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('authors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(AuthorRequest $request)
    {
        $author = Author::create($request->all());

        if (Request::wantsJson()) {
            return $author;
        } else {
            return redirect('authors');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Author $author)
    {
        if (Request::wantsJson()) {
            return $author;
        } else {
            return view('authors.show', compact('author'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(AuthorRequest $request, Author $author)
    {
        $author->update($request->all());

        if (Request::wantsJson()) {
            return $author;
        } else {
            return redirect('authors');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Author $author)
    {
        $deleted = $author->delete();

        if (Request::wantsJson()) {
            return (string) $deleted;
        } else {
            return redirect('authors');
        }
    }
}
