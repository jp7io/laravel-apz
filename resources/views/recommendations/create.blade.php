<x-layout title="Recommend an article">
    <x-alert />

    <h2 class="mb-6 text-xl font-semibold tracking-tight">Recommend "{{ $article->title }}" by e-mail</h2>

    <form id="recommendations-form" method="POST" action="{{ route('articles.recommendations.store', $article) }}">
        @csrf

        <x-form.field name="email" label="E-mail">
            <x-form.input name="email" type="email" />
        </x-form.field>

        <x-form.recaptcha />

        <x-button>Send Recommendation</x-button>
    </form>
</x-layout>
