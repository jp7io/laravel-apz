<x-layout :title="$author->name">
    <div class="rounded-xl border border-slate-200 bg-white px-6 py-8 shadow-sm">
        <h1 class="text-2xl font-semibold tracking-tight">{{ $author->name }}</h1>
        <p class="mt-1 text-slate-600">{{ $author->email }}</p>
    </div>

    <a href="{{ route('authors.index') }}" class="mt-6 inline-block text-sm text-indigo-600 hover:text-indigo-500">Back to authors</a>
</x-layout>
