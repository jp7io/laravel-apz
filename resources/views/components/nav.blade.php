<header class="border-b border-slate-200 bg-white">
    <nav class="mx-auto flex w-full max-w-5xl flex-wrap items-center gap-x-6 gap-y-2 px-4 py-4">
        <a href="{{ url('/') }}" class="text-lg font-semibold tracking-tight">Laravel Apz</a>

        <a href="{{ route('articles.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Articles</a>
        <a href="{{ route('authors.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Authors</a>

        <x-weather class="ms-auto" />
    </nav>
</header>
