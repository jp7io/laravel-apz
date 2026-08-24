<x-layout title="Edit Author">
    <h2 class="mb-6 text-xl font-semibold tracking-tight">Edit Author</h2>

    <form id="authors-form" method="POST" action="{{ route('authors.update', $author) }}">
        @csrf
        @method('PATCH')
        @include('authors.form', ['submitButtonText' => 'Edit Author'])
    </form>
</x-layout>
