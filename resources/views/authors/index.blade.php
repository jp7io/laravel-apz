<x-layout title="Authors">
    <x-alert />

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight">Authors</h1>
        <x-button href="{{ route('authors.create') }}">New Author</x-button>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">E-mail</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($authors as $author)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('authors.show', $author) }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ $author->name }}</a>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $author->email }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <x-button href="{{ route('authors.edit', $author) }}" variant="secondary">Edit</x-button>
                                <form method="POST" action="{{ route('authors.destroy', $author) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger">Delete</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500">No authors yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
