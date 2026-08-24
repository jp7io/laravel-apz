<x-layout title="Edit Article">
    <h2 class="mb-6 text-xl font-semibold tracking-tight">Edit Article</h2>

    <form id="articles-form" method="POST" action="{{ route('articles.update', $article) }}">
        @csrf
        @method('PATCH')
        @include('articles.form', ['submitButtonText' => 'Edit Article'])
    </form>
</x-layout>
