@php
    $bugReportErrors = $errors->hasBag('bugReport') ? $errors->bugReport : null;
@endphp

<div id="bug-report-modal" class="modal-backdrop" hidden role="dialog" aria-modal="true" aria-labelledby="bug-report-title">
    <div class="modal modal-lg" style="font-family: var(--font); color: var(--ink);">
        <div class="modal-head">
            <div>
                <h2 class="modal-title" id="bug-report-title">{{ __('customer.bug.title') }}</h2>
                <p class="card-sub">{{ __('customer.bug.sub') }}</p>
            </div>
            <button type="button" class="icon-btn" data-bug-report-dismiss aria-label="{{ __('customer.bug.close') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('bug-reports.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
            <input type="hidden" name="bk_ts" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) now()->timestamp) }}">
            <input type="hidden" name="bk_js" value="0" data-bug-js>
            <div class="modal-body">
                @if($bugReportErrors && $bugReportErrors->any())
                    <div class="alert alert-bad">
                        <ul>
                            @foreach($bugReportErrors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-grid">
                    <div class="field">
                        <label for="reporter_name">{{ __('customer.bug.name') }}</label>
                        <input class="input" type="text" id="reporter_name" name="reporter_name" value="{{ old('reporter_name') }}" required>
                    </div>
                    <div class="field">
                        <label for="reporter_email">{{ __('customer.bug.email') }}</label>
                        <input class="input" type="email" id="reporter_email" name="reporter_email" value="{{ old('reporter_email') }}" required>
                    </div>
                    <div class="field">
                        <label for="summary">{{ __('customer.bug.summary') }}</label>
                        <input class="input" type="text" id="summary" name="summary" value="{{ old('summary') }}" required maxlength="150">
                    </div>
                    <div class="field">
                        <label for="impact">{{ __('customer.bug.impact') }}</label>
                        <select class="select" id="impact" name="impact">
                            <option value="" @selected(old('impact') === '')>{{ __('customer.bug.impact_unknown') }}</option>
                            <option value="Blokuje rezerváciu" @selected(old('impact') === 'Blokuje rezerváciu')>{{ __('customer.bug.impact_blocking') }}</option>
                            <option value="Chyba v údajoch" @selected(old('impact') === 'Chyba v údajoch')>{{ __('customer.bug.impact_data') }}</option>
                            <option value="Dizajnová chyba" @selected(old('impact') === 'Dizajnová chyba')>{{ __('customer.bug.impact_design') }}</option>
                        </select>
                    </div>
                    <div class="field span-2">
                        <label for="description">{{ __('customer.bug.description') }}</label>
                        <textarea class="textarea" id="description" name="description" rows="4" required placeholder="{{ __('customer.bug.description_ph') }}">{{ old('description') }}</textarea>
                    </div>
                    <div class="field span-2">
                        <label for="steps">{{ __('customer.bug.steps') }}</label>
                        <textarea class="textarea" id="steps" name="steps" rows="3" style="min-height:4rem">{{ old('steps') }}</textarea>
                    </div>
                    <div class="field span-2">
                        <label for="attachments">{{ __('customer.bug.attachments') }}</label>
                        <input class="file" type="file" id="attachments" name="attachments[]" multiple accept="image/*,application/pdf">
                        <p class="hint">{{ __('customer.bug.attachments_hint') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" data-bug-report-dismiss>{{ __('customer.bug.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('customer.bug.send') }}</button>
            </div>
        </form>
    </div>
</div>
<script>
    (function () {
        const field = document.querySelector('[data-bug-js]');
        if (!field) return;
        let n = 0;
        ['mousemove', 'keydown', 'pointerdown', 'touchstart', 'focusin'].forEach(ev =>
            window.addEventListener(ev, () => { field.value = String(++n); }, { once: true, passive: true }));
    })();
</script>
