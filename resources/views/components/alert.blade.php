<div id="alert-box" @class([
    'mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800',
    'hidden' => $errors->isEmpty(),
])>
    <p class="font-semibold">Ops...</p>
    <ul class="mt-1 list-disc space-y-0.5 ps-5 text-sm">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>

@if (session('flash_message'))
    <div id="flash-message" class="mb-6 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sky-800">
        {{ session('flash_message') }}
    </div>
@endif
