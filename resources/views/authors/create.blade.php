<x-layout title="New Author">
    <h2 class="mb-6 text-xl font-semibold tracking-tight">New Author</h2>

    <form id="authors-form" method="POST" action="{{ route('authors.store') }}">
        @csrf
        @include('authors.form', ['submitButtonText' => 'Add Author'])
    </form>
</x-layout>
