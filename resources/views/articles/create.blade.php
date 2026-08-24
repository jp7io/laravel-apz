<x-layout title="New Article">
    <h2 class="mb-6 text-xl font-semibold tracking-tight">New Article</h2>

    <form id="articles-form" method="POST" action="{{ route('articles.store') }}">
        @csrf
        @include('articles.form', ['submitButtonText' => 'Add Article'])
    </form>
</x-layout>
