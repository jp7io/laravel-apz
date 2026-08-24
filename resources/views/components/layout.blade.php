@props(['title' => 'Laravel Apz'])

{{--
    An ajax request gets the bare fragment, so the same route serves both a full page and the
    body of the modal. Nothing server-side has to know which one the caller wanted.
--}}
@if (request()->ajax())
    {{ $slot }}
@else
    <!DOCTYPE html>
    <html lang="en" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex h-full flex-col bg-slate-50 text-slate-900 antialiased">
        <x-nav />

        <main id="page-content" class="mx-auto w-full max-w-5xl flex-1 px-4 py-10">
            {{ $slot }}
        </main>

        <x-modal />
    </body>
    </html>
@endif
