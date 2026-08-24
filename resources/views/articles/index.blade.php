<x-layout title="Articles">
    <x-alert />

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Articles</h1>
        <x-button href="{{ route('articles.create') }}" data-remote>New Article</x-button>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Title</th>
                    <th class="px-4 py-3 font-medium">Author</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($articles as $article)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('articles.show', $article) }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ $article->title }}</a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $article->author->name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <x-button href="{{ route('articles.edit', $article) }}" variant="secondary">Edit</x-button>
                                <x-button href="{{ route('articles.recommendations.create', $article) }}" variant="secondary">Recommend</x-button>
                                <form method="POST" action="{{ route('articles.destroy', $article) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger">Delete</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">No articles yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
