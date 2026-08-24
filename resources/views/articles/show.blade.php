<x-layout :title="$article->title">
    <article class="rounded-xl border border-slate-200 bg-white px-6 py-8 shadow-sm">
        <h1 class="text-2xl font-semibold tracking-tight">{{ $article->title }}</h1>
        <p class="mt-1 text-sm text-slate-500">by {{ $article->author->name }}</p>
        <div class="mt-6 leading-relaxed text-slate-700">{{ $article->content }}</div>
    </article>

    <a href="{{ route('articles.index') }}" class="mt-6 inline-block text-sm text-indigo-600 hover:text-indigo-500">Back to articles</a>
</x-layout>
