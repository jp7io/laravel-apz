<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthorsController;
use App\Http\Controllers\RecommendationsController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::resource('articles', ArticlesController::class);
Route::resource('articles.recommendations', RecommendationsController::class)->only(['create', 'store']);
Route::resource('authors', AuthorsController::class);
