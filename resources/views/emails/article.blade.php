<x-mail::message>
Dear friend,

Check this great article: [{{ $article->title }}]({{ route('articles.show', $article) }})

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
