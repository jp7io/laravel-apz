<x-alert />

<x-form.field name="name" label="Name">
    <x-form.input name="name" :value="$author->name" />
</x-form.field>

<x-form.field name="email" label="E-mail">
    <x-form.input name="email" type="email" :value="$author->email" />
</x-form.field>

<x-form.recaptcha />

<x-button>{{ $submitButtonText }}</x-button>
