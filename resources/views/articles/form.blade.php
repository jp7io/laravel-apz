<x-alert />

<x-form.field name="title" label="Title">
    <x-form.input name="title" :value="$article->title" />
</x-form.field>

<x-form.field name="content" label="Content">
    <x-form.textarea name="content" :value="$article->content" />
</x-form.field>

<x-form.field name="author_id" label="Author">
    <x-form.select name="author_id" :options="$authors" :selected="$article->author_id" />
</x-form.field>

<x-form.recaptcha />

<x-button>{{ $submitButtonText }}</x-button>
