{{-- m-auto is what centres it: Tailwind's preflight zeroes the margin the UA stylesheet uses. --}}
<dialog id="modal" class="m-auto w-full max-w-xl rounded-xl border border-slate-200 p-0 backdrop:bg-slate-900/40">
    <button
        type="button"
        data-close-modal
        aria-label="Close"
        class="absolute end-3 top-3 rounded-lg px-2 py-1 text-xl leading-none text-slate-400 hover:bg-slate-100 hover:text-slate-700"
    >&times;</button>

    <div id="modal-body" class="p-6"></div>
</dialog>
