@if (filled(config('services.recaptcha.site_key')))
    <div class="mb-5">
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </div>
@endif
