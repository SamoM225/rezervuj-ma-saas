<div class="bk-cookie" id="cookie-notice" role="region" aria-label="{{ __('widget.cookies_title') }}" hidden>
    <div class="bk-cookie-text">
        <strong>{{ __('widget.cookies_title') }}</strong>
        {{ __('widget.cookies_text') }}
        <a href="{{ route('privacy') }}">{{ __('widget.privacy_policy') }}</a>
    </div>
    <button type="button" class="bk-cookie-btn" data-cookie-accept>{{ __('widget.cookies_ok') }}</button>
</div>
<script>
    (function () {
        const el = document.getElementById('cookie-notice');
        if (!el) return;
        let seen = false;
        try { seen = localStorage.getItem('cookie-notice-v1') === '1'; } catch (e) {}
        if (seen) return;
        el.hidden = false;
        el.querySelector('[data-cookie-accept]').addEventListener('click', function () {
            try { localStorage.setItem('cookie-notice-v1', '1'); } catch (e) {}
            el.hidden = true;
        });
    })();
</script>
