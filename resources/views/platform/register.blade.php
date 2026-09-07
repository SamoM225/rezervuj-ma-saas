@extends('layouts.platform')

@section('title', __('auth.register.title'))

@section('content')
<div class="auth-card wide">
    <div class="auth-brand">
        <div class="sidebar-mark" aria-hidden="true">R</div>
        <div>
            <h1 class="auth-title">{{ __('auth.register.title') }}</h1>
            <p class="auth-sub">{{ __('auth.register.subtitle') }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-bad" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 8v5m0 3h.01M12 3l9 16H3z"/></svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <form method="POST" action="{{ \App\Support\Locales::route('register.store') }}" class="space-y-4" novalidate>
        @csrf
        <div class="field">
            <label for="business_name">{{ __('auth.register.business_name') }}</label>
            <input class="input" id="business_name" name="business_name" type="text" required maxlength="80" value="{{ old('business_name') }}" autofocus data-slug-source>
        </div>
        <div class="field">
            <label for="slug">{{ __('auth.register.slug') }}</label>
            <input class="input mono" id="slug" name="slug" type="text" required minlength="3" maxlength="50" pattern="[a-z0-9](?:[a-z0-9-]{1,48}[a-z0-9])?" value="{{ old('slug') }}" data-slug-target>
            {{-- The preview span is injected after escaping, so the translated text itself stays escaped. --}}
            <p class="hint">{!! str_replace('§SLUG§', '<span data-slug-preview>'.e(old('slug', 'moja-prevadzka')).'</span>', e(__('auth.register.slug_hint', ['slug' => '§SLUG§']))) !!}</p>
        </div>
        <div class="grid-2">
            <div class="field">
                <label for="category">{{ __('auth.register.category') }}</label>
                <select class="input" id="category" name="category" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(old('category', 'beauty') === $category)>{{ __("tenancy.categories.{$category}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="locale">{{ __('auth.register.locale') }}</label>
                <select class="input" id="locale" name="locale" required>
                    @foreach ($locales as $locale)
                        <option value="{{ $locale }}" @selected(old('locale', app()->getLocale()) === $locale)>{{ \App\Helpers\LanguageHelper::$locales[$locale]['native'] ?? $locale }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="country">{{ __('auth.register.country') }}</label>
                <select class="input" id="country" name="country" required>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}" @selected(old('country', 'SK') === $country)>{{ __("tenancy.countries.{$country}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="city">{{ __('auth.register.city') }}</label>
                <input class="input" id="city" name="city" type="text" maxlength="80" value="{{ old('city') }}">
            </div>
        </div>
        <div class="field">
            <label for="email">{{ __('auth.register.email') }}</label>
            <input class="input" id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}">
        </div>
        <div class="grid-2">
            <div class="field">
                <label for="password">{{ __('auth.register.password') }}</label>
                <input class="input" id="password" name="password" type="password" autocomplete="new-password" required minlength="10">
                <p class="hint">{{ __('auth.register.password_hint') }}</p>
            </div>
            <div class="field">
                <label for="password_confirmation">{{ __('auth.register.password_confirmation') }}</label>
                <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="10">
            </div>
        </div>

        <fieldset style="border:0;padding:0;margin:.5rem 0 0">
            <legend class="auth-sub" style="font-weight:600;margin-bottom:.25rem">{{ __('auth.register.legal_heading') }}</legend>
            <label class="check-legal"><input type="checkbox" name="accept_terms" value="1" required @checked(old('accept_terms'))>
                <span>{!! __('auth.register.accept_terms', ['terms' => '<a class="link" href="'.\App\Support\Locales::route('legal.show', ['doc' => 'terms']).'" target="_blank" rel="noopener">'.e(__('auth.register.terms_link')).'</a>']) !!}</span></label>
            <label class="check-legal"><input type="checkbox" name="accept_dpa" value="1" required @checked(old('accept_dpa'))>
                <span>{!! __('auth.register.accept_dpa', ['dpa' => '<a class="link" href="'.\App\Support\Locales::route('legal.show', ['doc' => 'dpa']).'" target="_blank" rel="noopener">'.e(__('auth.register.dpa_link')).'</a>']) !!}</span></label>
            <label class="check-legal"><input type="checkbox" name="accept_controller" value="1" required @checked(old('accept_controller'))><span>{{ __('auth.register.accept_controller') }}</span></label>
            <label class="check-legal"><input type="checkbox" name="accept_accuracy" value="1" required @checked(old('accept_accuracy'))><span>{{ __('auth.register.accept_accuracy') }}</span></label>
            <label class="check-legal"><input type="checkbox" name="accept_age" value="1" required @checked(old('accept_age'))><span>{{ __('auth.register.accept_age') }}</span></label>
            <label class="check-legal"><input type="checkbox" name="accept_marketing" value="1" @checked(old('accept_marketing'))><span>{{ __('auth.register.accept_marketing') }}</span></label>
        </fieldset>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%">{{ __('auth.register.submit') }}</button>
    </form>

    <p class="hint" style="margin-top:1.25rem;text-align:center">{{ __('auth.register.have_account') }}</p>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var src = document.querySelector('[data-slug-source]'), dst = document.querySelector('[data-slug-target]'), prev = document.querySelector('[data-slug-preview]');
    if (!src || !dst) return;
    var touched = dst.value !== '';
    dst.addEventListener('input', function () { touched = dst.value !== ''; if (prev) prev.textContent = dst.value || 'moja-prevadzka'; });
    src.addEventListener('input', function () {
        if (touched) return;
        var s = src.value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 50);
        dst.value = s; if (prev) prev.textContent = s || 'moja-prevadzka';
    });
})();
</script>
@endpush
