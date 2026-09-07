@extends('layouts.app')

@php
    use App\Models\BusinessSetting;
    use Illuminate\Support\Facades\Storage;

    $businessName = BusinessSetting::get('business_name', config('app.name', 'Rezervácie'));
    $businessAddress = BusinessSetting::get('business_address');
    $supportEmail = BusinessSetting::get('support_email');
    $supportPhone = BusinessSetting::get('support_phone');
    $openingHours = BusinessSetting::get('opening_hours_text');
    $heroPath = BusinessSetting::get('hero_background_image_path');
    $heroUrl = $heroPath ? Storage::url($heroPath) : null;
    $heroOverlay = BusinessSetting::get('hero_overlay_color', '#1f1a14');
    $heroOpacity = max(0, min(1, (float) BusinessSetting::get('hero_overlay_opacity', 0.55)));
    $showServiceImages = (bool) BusinessSetting::get('show_service_images', true);
    $showWorkerAvatars = (bool) BusinessSetting::get('show_worker_avatars', true);

    $heroHex = ltrim($heroOverlay ?: '#1f1a14', '#');
    if (strlen($heroHex) === 3) { $heroHex = $heroHex[0].$heroHex[0].$heroHex[1].$heroHex[1].$heroHex[2].$heroHex[2]; }
    $heroInt = hexdec(strlen($heroHex) === 6 ? $heroHex : '1f1a14');
    $heroRgba = sprintf('rgba(%d, %d, %d, %.2f)', ($heroInt >> 16) & 255, ($heroInt >> 8) & 255, $heroInt & 255, $heroOpacity);
    $pageStyle = $heroUrl ? "background-image: linear-gradient({$heroRgba}, {$heroRgba}), url('{$heroUrl}');" : '';

    $showLocationStep = $cities->count() > 1;
    $singleCity = $cities->count() === 1 ? $cities->first() : null;
    $jsLocale = ['sk' => 'sk-SK', 'cs' => 'cs-CZ', 'en' => 'en-GB'][app()->getLocale()] ?? 'sk-SK';
    $monthNames = array_map(fn ($month) => __('widget.month_'.$month), ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december']);
@endphp

@section('content')
@unless($embed)
@include('partials.topbar')
@endunless
<div class="bk-page{{ $heroUrl ? ' has-photo' : '' }}" style="{{ $pageStyle }}">
    <div class="bk-shell">

        @unless($onlineBookingEnabled)
            <div class="bk-alert bk-alert-error" style="margin-bottom:1rem">
                {{ __('widget.no_categories') }}
                @if($supportPhone) <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" style="font-weight:700;color:inherit">{{ $supportPhone }}</a> @endif
            </div>
        @endunless

        <div class="bk-layout">
            <div class="bk-main">
                @if($errors->any() && !$errors->hasBag('bugReport'))
                    <div class="bk-alert bk-alert-error" role="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('success'))
                    <div class="bk-alert bk-alert-success" role="status">{{ session('success') }}</div>
                @endif
                <div class="bk-alert bk-alert-error" id="bk-error" role="alert" hidden></div>

                {{-- Location --}}
                <section class="bk-step" id="step-location" data-step="location" @unless($showLocationStep) hidden @endunless>
                    <div class="bk-step-head">
                        <span class="bk-badge" data-badge><span>1</span></span>
                        <div style="flex:1;min-width:0">
                            <h2 class="bk-step-title">{{ __('widget.select_location') }}</h2>
                            <p class="bk-step-summary" data-summary></p>
                        </div>
                        <button type="button" class="bk-step-change" data-change hidden>{{ __('widget.change') }}</button>
                    </div>
                    <div class="bk-step-body">
                        <div class="bk-grid-cards" id="locations">
                            @foreach($cities as $city)
                                <button type="button" class="bk-option" data-id="{{ $city->id }}" data-name="{{ $city->name }}">
                                    <span class="bk-option-title">{{ $city->name }}</span>
                                    @if($city->address)<span class="bk-option-sub">{{ $city->address }}</span>@endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Category --}}
                <section class="bk-step is-hidden" id="step-category" data-step="category">
                    <div class="bk-step-head">
                        <span class="bk-badge" data-badge><span>2</span></span>
                        <div style="flex:1;min-width:0">
                            <h2 class="bk-step-title">{{ __('widget.select_category') }}</h2>
                            <p class="bk-step-summary" data-summary></p>
                        </div>
                        <button type="button" class="bk-step-change" data-change hidden>{{ __('widget.change') }}</button>
                    </div>
                    <div class="bk-step-body"><div class="bk-grid-cards" id="categories"></div></div>
                </section>

                {{-- Service --}}
                <section class="bk-step is-hidden" id="step-service" data-step="service">
                    <div class="bk-step-head">
                        <span class="bk-badge" data-badge><span>3</span></span>
                        <div style="flex:1;min-width:0">
                            <h2 class="bk-step-title">{{ __('widget.select_service') }}</h2>
                            <p class="bk-step-summary" data-summary></p>
                        </div>
                        <button type="button" class="bk-step-change" data-change hidden>{{ __('widget.change') }}</button>
                    </div>
                    <div class="bk-step-body"><div class="bk-grid-services" id="services"></div></div>
                </section>

                {{-- Worker --}}
                <section class="bk-step is-hidden" id="step-worker" data-step="worker">
                    <div class="bk-step-head">
                        <span class="bk-badge" data-badge><span>4</span></span>
                        <div style="flex:1;min-width:0">
                            <h2 class="bk-step-title">{{ __('widget.select_worker') }}</h2>
                            <p class="bk-step-summary" data-summary></p>
                        </div>
                        <button type="button" class="bk-step-change" data-change hidden>{{ __('widget.change') }}</button>
                    </div>
                    <div class="bk-step-body"><div class="bk-grid-workers" id="workers"></div></div>
                </section>

                {{-- Date & time --}}
                <section class="bk-step is-hidden" id="step-datetime" data-step="datetime">
                    <div class="bk-step-head">
                        <span class="bk-badge" data-badge><span>5</span></span>
                        <div style="flex:1;min-width:0">
                            <h2 class="bk-step-title">{{ __('widget.select_datetime') }}</h2>
                            <p class="bk-step-summary" data-summary></p>
                        </div>
                        <button type="button" class="bk-step-change" data-change hidden>{{ __('widget.change') }}</button>
                    </div>
                    <div class="bk-step-body">
                        <div class="bk-datetime">
                            <div class="bk-cal" id="calendar" role="application" aria-label="{{ __('widget.select_date') }}">
                                <div class="bk-cal-head">
                                    <button type="button" class="bk-cal-nav" id="cal-prev" aria-label="{{ __('common.previous') }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6"/></svg>
                                    </button>
                                    <div class="bk-cal-month" id="cal-title" aria-live="polite"></div>
                                    <button type="button" class="bk-cal-nav" id="cal-next" aria-label="{{ __('common.next') }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
                                    </button>
                                </div>
                                <div class="bk-cal-weekdays" aria-hidden="true">
                                    <span>{{ __('widget.weekday_monday') }}</span><span>{{ __('widget.weekday_tuesday') }}</span><span>{{ __('widget.weekday_wednesday') }}</span><span>{{ __('widget.weekday_thursday') }}</span><span>{{ __('widget.weekday_friday') }}</span><span>{{ __('widget.weekday_saturday') }}</span><span>{{ __('widget.weekday_sunday') }}</span>
                                </div>
                                <div class="bk-cal-grid" id="cal-grid" role="grid"></div>
                                <div class="bk-cal-foot"><span><i></i>{{ __('widget.today') }}</span></div>
                            </div>
                            <div class="bk-times-col">
                                <div class="bk-times-head">
                                    <span class="bk-times-label" id="times-title">{{ __('widget.select_time') }}</span>
                                    <span class="bk-times-count" id="times-count"></span>
                                </div>
                                <div id="times"></div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Details --}}
                <section class="bk-step is-hidden" id="step-details" data-step="details">
                    <div class="bk-step-head">
                        <span class="bk-badge" data-badge><span>6</span></span>
                        <div style="flex:1;min-width:0">
                            <h2 class="bk-step-title">{{ __('widget.your_contact_info') }}</h2>
                        </div>
                    </div>
                    <div class="bk-step-body">
                        <form id="booking-form" method="POST" action="{{ route('booking.store') }}" class="bk-form" novalidate>
                            @csrf
                            <input type="hidden" name="service_id" id="f-service">
                            <input type="hidden" name="worker_id" id="f-worker">
                            <input type="hidden" name="city_id" id="f-city" value="{{ $singleCity?->id }}">
                            <input type="hidden" name="date" id="f-date">
                            <input type="hidden" name="time" id="f-time">
                            <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
                            <input type="hidden" name="bk_ts" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) now()->timestamp) }}">
                            <input type="hidden" name="bk_js" id="bk_js" value="0">

                            <div class="bk-field bk-col-span">
                                <label for="name">{{ __('widget.name_label') }}</label>
                                <input type="text" id="name" name="name" required autocomplete="name" value="{{ old('name') }}">
                            </div>
                            <div class="bk-field">
                                <label for="email">{{ __('widget.email_label') }}</label>
                                <input type="email" id="email" name="email" required autocomplete="email" inputmode="email" value="{{ old('email') }}">
                            </div>
                            <div class="bk-field">
                                <label for="phone">{{ __('widget.phone_label') }}</label>
                                <input type="tel" id="phone" name="phone" required autocomplete="tel" inputmode="tel" value="{{ old('phone') }}">
                            </div>
                            <div class="bk-field bk-col-span">
                                <label for="notes">{{ __('widget.notes_label') }}</label>
                                <textarea id="notes" name="notes" rows="3" placeholder="{{ __('widget.notes_placeholder') }}">{{ old('notes') }}</textarea>
                            </div>
                            <label class="bk-consent bk-col-span">
                                <input type="checkbox" id="gdpr" name="gdpr" value="1" required>
                                <span>{{ __('widget.gdpr_consent') }} <a href="{{ route('privacy') }}" target="_blank" rel="noopener">{{ __('widget.privacy_policy') }}</a> · <a href="{{ route('terms') }}" target="_blank" rel="noopener">{{ __('widget.terms') }}</a></span>
                            </label>
                        </form>
                    </div>
                </section>
            </div>

            <aside class="bk-summary">
                <div class="bk-summary-card">
                    <h3 class="bk-summary-title">{{ __('widget.booking_summary_title') }}</h3>
                    <p class="bk-summary-sub">{{ __('widget.summary_subtitle') }}</p>

                    <div class="bk-summary-rows">
                        @if($showLocationStep)
                            <div class="bk-summary-row" data-row="location">
                                <span class="bk-dot"></span>
                                <span><span class="bk-summary-key">{{ __('widget.summary_location') }}</span><span class="bk-summary-val">{{ __('widget.not_selected') }}</span></span>
                            </div>
                        @endif
                        <div class="bk-summary-row" data-row="service">
                            <span class="bk-dot"></span>
                            <span><span class="bk-summary-key">{{ __('widget.summary_service') }}</span><span class="bk-summary-val">{{ __('widget.not_selected') }}</span></span>
                        </div>
                        <div class="bk-summary-row" data-row="worker">
                            <span class="bk-dot"></span>
                            <span><span class="bk-summary-key">{{ __('widget.summary_worker') }}</span><span class="bk-summary-val">{{ __('widget.not_selected') }}</span></span>
                        </div>
                        <div class="bk-summary-row" data-row="date">
                            <span class="bk-dot"></span>
                            <span><span class="bk-summary-key">{{ __('widget.summary_date') }}</span><span class="bk-summary-val">{{ __('widget.not_selected') }}</span></span>
                        </div>
                        <div class="bk-summary-row" data-row="time">
                            <span class="bk-dot"></span>
                            <span><span class="bk-summary-key">{{ __('widget.summary_time') }}</span><span class="bk-summary-val">{{ __('widget.not_selected') }}</span></span>
                        </div>
                    </div>

                    <div class="bk-summary-totals">
                        <div class="bk-summary-line"><span>{{ __('widget.summary_duration') }}</span><strong id="sum-duration">—</strong></div>
                        <div class="bk-summary-line bk-summary-total"><span>{{ __('widget.total') }}</span><span class="bk-summary-price" id="sum-price">—</span></div>
                    </div>

                    <div class="bk-hold is-hidden" id="hold" role="status">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 1.5"/></svg>
                        <span>{{ __('widget.slot_held_label') }} <strong id="hold-timer">{{ $holdMinutes }}:00</strong></span>
                    </div>

                    <button type="submit" form="booking-form" id="submit" class="bk-submit" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ __('widget.confirm_booking') }}</span>
                    </button>
                    <p class="bk-summary-hint" id="hint">{{ __('widget.complete_booking_hint') }}</p>
                    <p class="bk-secure">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        {{ __('widget.secure_connection') }}
                    </p>
                </div>
            </aside>
        </div>

        <footer class="bk-footer" @if($embed) hidden @endif>
            <div>
                <h4>{{ $businessName }}</h4>
                @foreach($cities as $city)
                    <p>{{ $city->address ?: $city->name }}</p>
                @endforeach
                @if($cities->isEmpty() && $businessAddress)<p>{{ $businessAddress }}</p>@endif
                <p style="margin-top:.5rem">&copy; {{ date('Y') }} {{ $businessName }}</p>
            </div>
            <div>
                <h4>{{ __('widget.contact') }}</h4>
                @if($supportPhone)<p><a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}">{{ $supportPhone }}</a></p>@endif
                @if($supportEmail)<p><a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>@endif
                @if($openingHours)<p style="margin-top:.5rem"><strong style="font-weight:600">{{ __('widget.opening_hours') }}</strong><br>{!! nl2br(e($openingHours)) !!}</p>@endif
            </div>
            <div>
                <h4>{{ __('widget.support') }}</h4>
                <p>{{ __('widget.support_text') }}</p>
                <div class="bk-footer-actions">
                    <button type="button" data-bug-report-trigger>{{ __('widget.report_bug') }}</button>
                    <a href="{{ route('privacy') }}">{{ __('widget.privacy_policy') }}</a>
                    <a href="{{ route('terms') }}">{{ __('widget.terms') }}</a>
                    @guest<a href="{{ route('login') }}">{{ __('widget.staff_login') }}</a>@endguest
                    @unless(\App\Support\PlanLimits::currentAllows('custom_branding'))<a href="{{ \App\Support\Locales::route('home') }}" rel="noopener">{{ __('widget.powered_by') }}</a>@endunless
                </div>
            </div>
        </footer>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const CFG = {
        csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
        locale: @json($jsLocale),
        holdMinutes: @json($holdMinutes),
        showLocation: @json($showLocationStep),
        singleCity: @json($singleCity ? ['id' => $singleCity->id, 'name' => $singleCity->name] : null),
        showServiceImages: @json($showServiceImages),
        showWorkerAvatars: @json($showWorkerAvatars),
        bookingEnabled: @json($onlineBookingEnabled),
        urls: {
            categories: @json(url('/web/categories')),
            services: @json(url('/web/procedures')),
            workers: @json(url('/web/workers')),
            dates: @json(url('/web/dates')),
            times: @json(url('/web/times')),
            hold: @json(route('web.hold')),
            book: @json(route('booking.store')),
        },
        t: {
            notSelected: @json(__('widget.not_selected')),
            loading: @json(__('widget.loading')),
            submitting: @json(__('widget.submitting')),
            confirm: @json(__('widget.confirm_booking')),
            noCategories: @json(__('widget.no_categories')),
            noServices: @json(__('widget.no_services')),
            noWorkers: @json(__('widget.no_worker_available')),
            anyWorker: @json(__('widget.any_worker')),
            anyWorkerSub: @json(__('widget.any_worker_sub')),
            assignedWorker: @json(__('widget.assigned_worker')),
            noDates: @json(__('widget.no_dates')),
            noTimes: @json(__('widget.no_times')),
            pickDate: @json(__('widget.select_available_date')),
            freeSlots: @json(__('widget.free_slots')),
            morning: @json(__('widget.morning')),
            afternoon: @json(__('widget.afternoon')),
            evening: @json(__('widget.evening')),
            slotTaken: @json(__('widget.slot_taken')),
            slotExpired: @json(__('widget.slot_hold_expired')),
            failed: @json(__('widget.booking_failed')),
            minutes: @json(__('widget.minutes_short')),
            today: @json(__('widget.today')),
            tomorrow: @json(__('widget.tomorrow')),
            months: @json($monthNames),
        },
    };

    const $ = (id) => document.getElementById(id);
    const STEPS = ['location', 'category', 'service', 'worker', 'datetime', 'details'];
    const state = { location: CFG.singleCity, category: null, service: null, worker: null, date: null, time: null };
    const cache = { dates: [] };
    let holdTimer = null;
    let toastWrap = null;
    const cal = { year: new Date().getFullYear(), month: new Date().getMonth(), focus: null };

    // ---- behavioural anti-bot signal --------------------------------------
    (function () {
        let n = 0;
        const field = $('bk_js');
        ['mousemove', 'keydown', 'pointerdown', 'touchstart', 'focusin'].forEach(ev =>
            window.addEventListener(ev, () => { n++; if (field) field.value = String(n); }, { once: true, passive: true }));
    })();

    // ---- helpers ----------------------------------------------------------
    const h = (tag, attrs = {}, children = []) => {
        const node = document.createElement(tag);
        for (const [key, value] of Object.entries(attrs)) {
            if (value === null || value === undefined || value === false) continue;
            if (key === 'class') node.className = value;
            else if (key === 'text') node.textContent = value;
            else if (key.startsWith('on')) node.addEventListener(key.slice(2), value);
            else if (key === 'style') node.style.cssText = value;
            else node.setAttribute(key, value === true ? '' : value);
        }
        [].concat(children).forEach(child => { if (child) node.appendChild(typeof child === 'string' ? document.createTextNode(child) : child); });
        return node;
    };
    const pad = (n) => String(n).padStart(2, '0');
    const iso = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    const fromIso = (s) => { const [y, m, d] = s.split('-').map(Number); return new Date(y, m - 1, d); };
    const fmtDate = (s) => {
        if (!s) return null;
        const d = fromIso(s);
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const diff = Math.round((d - today) / 86400000);
        const long = d.toLocaleDateString(CFG.locale, { weekday: 'short', day: 'numeric', month: 'long' });
        return diff === 0 ? `${CFG.t.today}, ${long}` : diff === 1 ? `${CFG.t.tomorrow}, ${long}` : long;
    };
    const hm = (t) => (t || '').slice(0, 5);
    const fetchJson = async (url, options = {}) => {
        const res = await fetch(url, { ...options, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', ...(options.headers || {}) } });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) { const err = new Error(data.message || `HTTP ${res.status}`); err.status = res.status; err.data = data; throw err; }
        return data;
    };
    const skeleton = (n = 3) => h('div', { class: 'bk-loading', 'aria-busy': 'true' }, Array.from({ length: n }, () => h('div', { class: 'bk-skeleton' })));
    const empty = (text) => h('div', { class: 'bk-empty', text });

    function toast(message, type = 'info') {
        if (!toastWrap) { toastWrap = h('div', { class: 'bk-toast-wrap' }); document.body.appendChild(toastWrap); }
        const el = h('div', { class: `bk-toast ${type}`, text: message, role: 'status' });
        toastWrap.appendChild(el);
        requestAnimationFrame(() => el.classList.add('show'));
        setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 250); }, 3800);
    }

    // ---- steps ------------------------------------------------------------
    const stepEl = (key) => $(`step-${key}`);
    function renumber() {
        let n = 0;
        STEPS.forEach(key => {
            const el = stepEl(key);
            if (el.hidden) return;
            n += 1;
            el.querySelector('[data-badge] span').textContent = n;
        });
    }
    function openStep(key, scroll = true) {
        const el = stepEl(key);
        const wasHidden = el.classList.contains('is-hidden');
        el.classList.remove('is-hidden', 'is-collapsed');
        el.querySelector('[data-badge]').classList.remove('is-done');
        const change = el.querySelector('[data-change]');
        if (change) change.hidden = true;
        if (wasHidden) {
            el.classList.remove('bk-reveal'); void el.offsetWidth; el.classList.add('bk-reveal');
            if (scroll) setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 40);
        }
    }
    function collapse(key, summary) {
        const el = stepEl(key);
        el.classList.add('is-collapsed');
        el.querySelector('[data-badge]').classList.add('is-done');
        el.querySelector('[data-summary]').textContent = summary;
        const change = el.querySelector('[data-change]');
        if (change) change.hidden = false;
    }
    function resetFrom(key) {
        const idx = STEPS.indexOf(key);
        STEPS.slice(idx).forEach(k => {
            if (k === 'location' && !CFG.showLocation) return;
            if (k in state) state[k] = null;
            if (k === 'datetime') { state.date = null; state.time = null; clearHold(); }
            const el = stepEl(k);
            if (k !== key) el.classList.add('is-hidden');
            el.classList.remove('is-collapsed');
            el.querySelector('[data-badge]').classList.remove('is-done');
            const change = el.querySelector('[data-change]');
            if (change) change.hidden = true;
            el.querySelectorAll('.bk-option.is-selected').forEach(o => o.classList.remove('is-selected'));
        });
        updateSummary();
    }
    document.querySelectorAll('[data-change]').forEach(btn => btn.addEventListener('click', () => {
        const key = btn.closest('[data-step]').dataset.step;
        resetFrom(key);
        openStep(key);
        if (key === 'datetime') { renderCalendar(); renderTimes(); }
    }));

    // ---- summary ----------------------------------------------------------
    function setRow(key, value) {
        const row = document.querySelector(`.bk-summary-row[data-row="${key}"]`);
        if (!row) return;
        row.classList.toggle('is-filled', !!value);
        row.querySelector('.bk-summary-val').textContent = value || CFG.t.notSelected;
    }
    function updateSummary() {
        setRow('location', state.location?.name);
        setRow('service', state.service?.name);
        setRow('worker', state.worker?.name);
        setRow('date', fmtDate(state.date));
        setRow('time', state.time ? `${hm(state.time.start)} – ${hm(state.time.end)}` : null);
        $('sum-duration').textContent = state.service ? `${state.service.duration} ${CFG.t.minutes}` : '—';
        $('sum-price').textContent = state.service?.price_label || '—';
        $('f-city').value = state.location?.id || '';
        $('f-service').value = state.service?.id || '';
        $('f-worker').value = state.worker?.id || '';
        $('f-date').value = state.date || '';
        $('f-time').value = state.time ? hm(state.time.start) : '';
        const ready = CFG.bookingEnabled && state.service && state.worker && state.date && state.time;
        $('submit').disabled = !ready;
        $('hint').classList.toggle('is-hidden', !!ready);
    }

    // ---- option lists -----------------------------------------------------
    function option(item, onPick, content, extraClass = '') {
        return h('button', { type: 'button', class: `bk-option ${extraClass}`.trim(), 'data-id': item.id, onclick: (e) => {
            e.currentTarget.parentElement.querySelectorAll('.bk-option').forEach(o => o.classList.remove('is-selected'));
            e.currentTarget.classList.add('is-selected');
            onPick(item);
        } }, content);
    }

    function bindLocations() {
        $('locations').querySelectorAll('.bk-option').forEach(btn => btn.addEventListener('click', () => {
            $('locations').querySelectorAll('.bk-option').forEach(o => o.classList.remove('is-selected'));
            btn.classList.add('is-selected');
            resetFrom('category');
            state.location = { id: Number(btn.dataset.id), name: btn.dataset.name };
            collapse('location', btn.dataset.name);
            updateSummary();
            loadCategories();
        }));
    }

    async function loadCategories() {
        openStep('category');
        const box = $('categories');
        box.replaceChildren(skeleton(3));
        try {
            const list = await fetchJson(state.location ? `${CFG.urls.categories}/${state.location.id}` : CFG.urls.categories);
            box.replaceChildren();
            if (!list.length) { box.appendChild(empty(CFG.t.noCategories)); return; }
            list.forEach(cat => box.appendChild(option(cat, pickCategory, [
                h('span', { class: 'bk-option-title', text: cat.name }),
                cat.description ? h('span', { class: 'bk-option-sub', text: cat.description }) : null,
            ])));
            if (list.length === 1) box.firstElementChild.click();
        } catch (e) { box.replaceChildren(empty(CFG.t.noCategories)); }
    }
    function pickCategory(cat) {
        resetFrom('service');
        state.category = cat;
        collapse('category', cat.name);
        updateSummary();
        loadServices();
    }

    async function loadServices() {
        openStep('service');
        const box = $('services');
        box.replaceChildren(skeleton(4));
        try {
            const list = await fetchJson(`${CFG.urls.services}/${state.category.id}`);
            box.replaceChildren();
            if (!list.length) { box.appendChild(empty(CFG.t.noServices)); return; }
            list.forEach(svc => {
                const thumb = CFG.showServiceImages && svc.image_url;
                box.appendChild(option(svc, pickService, h('span', { class: `bk-service ${thumb ? '' : 'no-thumb'}` }, [
                    thumb ? h('img', { class: 'bk-service-thumb', src: svc.image_url, alt: '' }) : null,
                    h('span', {}, [
                        h('span', { class: 'bk-service-name', text: svc.name }),
                        svc.description ? h('span', { class: 'bk-service-desc', text: svc.description }) : null,
                    ]),
                    h('span', { class: 'bk-service-meta' }, [
                        h('span', { class: 'bk-service-price', text: svc.price_label }),
                        h('span', { class: 'bk-service-dur', text: `${svc.duration} ${CFG.t.minutes}` }),
                    ]),
                ])));
            });
        } catch (e) { box.replaceChildren(empty(CFG.t.noServices)); }
    }
    function pickService(svc) {
        resetFrom('worker');
        state.service = svc;
        collapse('service', `${svc.name} · ${svc.price_label}`);
        updateSummary();
        loadWorkers();
    }

    async function loadWorkers() {
        openStep('worker');
        const box = $('workers');
        box.replaceChildren(skeleton(2));
        try {
            const params = new URLSearchParams({ service_id: state.service.id });
            if (state.location) params.set('city_id', state.location.id);
            const list = await fetchJson(`${CFG.urls.workers}/${state.category.id}?${params}`);
            box.replaceChildren();
            if (!list.length) { box.appendChild(empty(CFG.t.noWorkers)); return; }
            const all = list.length > 1 ? [{ id: 'any', name: CFG.t.anyWorker, isAny: true }, ...list] : list;
            all.forEach(w => box.appendChild(option(w, pickWorker, h('span', { class: 'bk-worker' }, [
                CFG.showWorkerAvatars ? h('span', { class: `bk-worker-avatar ${w.isAny ? 'is-any' : ''}`.trim(), style: w.calendar_color ? `--worker-color:${w.calendar_color}` : '' },
                    w.isAny ? h('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M4 7h13l-3-3m3 3-3 3M20 17H7l3 3m-3-3 3-3' })]) :
                    (w.avatar_url ? h('img', { src: w.avatar_url, alt: '' }) : (w.name || '?').trim().charAt(0).toUpperCase())) : null,
                h('span', {}, [
                    h('span', { class: 'bk-worker-name', text: w.name }),
                    (w.isAny ? CFG.t.anyWorkerSub : w.city) ? h('span', { class: 'bk-worker-sub', text: w.isAny ? CFG.t.anyWorkerSub : w.city }) : null,
                ]),
            ]))));
        } catch (e) { box.replaceChildren(empty(CFG.t.noWorkers)); }
    }
    function pickWorker(w) {
        resetFrom('datetime');
        state.worker = w;
        collapse('worker', w.name);
        updateSummary();
        loadDates();
    }

    function workerParams() {
        const params = new URLSearchParams({ service_id: state.service.id });
        if (state.category) params.set('category_id', state.category.id);
        if (state.location) params.set('city_id', state.location.id);
        return params;
    }

    // ---- calendar ---------------------------------------------------------
    async function loadDates() {
        openStep('datetime');
        cache.dates = [];
        $('cal-grid').replaceChildren(skeleton(1));
        renderTimes();
        try {
            const data = await fetchJson(`${CFG.urls.dates}/${state.worker.id}?${workerParams()}`);
            cache.dates = Array.isArray(data) ? data : (data.availableDates || []);
        } catch (e) { cache.dates = []; }
        const first = cache.dates.slice().sort()[0];
        const base = first ? fromIso(first) : new Date();
        cal.year = base.getFullYear(); cal.month = base.getMonth(); cal.focus = first || null;
        renderCalendar();
        if (!cache.dates.length) $('times').replaceChildren(h('div', { class: 'bk-times-empty', text: CFG.t.noDates }));
    }

    function renderCalendar() {
        const grid = $('cal-grid');
        const set = new Set(cache.dates);
        const first = new Date(cal.year, cal.month, 1);
        const offset = (first.getDay() + 6) % 7;
        const start = new Date(cal.year, cal.month, 1 - offset);
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const todayKey = iso(today);
        const sorted = cache.dates.slice().sort();
        const lastAvail = sorted.length ? fromIso(sorted[sorted.length - 1]) : new Date(today.getFullYear(), today.getMonth() + 2, 1);

        $('cal-title').textContent = `${CFG.t.months[cal.month]} ${cal.year}`;
        $('cal-prev').disabled = new Date(cal.year, cal.month, 1) <= new Date(today.getFullYear(), today.getMonth(), 1);
        $('cal-next').disabled = new Date(cal.year, cal.month + 1, 1) > lastAvail;

        const rows = [];
        const weeks = Math.ceil((offset + new Date(cal.year, cal.month + 1, 0).getDate()) / 7);
        const focusKey = cal.focus && set.has(cal.focus) && fromIso(cal.focus).getMonth() === cal.month ? cal.focus
            : sorted.find(d => { const x = fromIso(d); return x.getFullYear() === cal.year && x.getMonth() === cal.month; }) || null;
        cal.focus = focusKey;

        for (let w = 0; w < weeks; w++) {
            const row = h('div', { role: 'row', style: 'display:contents' });
            for (let i = 0; i < 7; i++) {
                const d = new Date(start.getFullYear(), start.getMonth(), start.getDate() + w * 7 + i);
                const key = iso(d);
                const outside = d.getMonth() !== cal.month;
                const available = set.has(key) && !outside;
                const cls = ['bk-day', outside && 'is-outside', available && 'is-available', key === todayKey && 'is-today', key === state.date && 'is-selected'].filter(Boolean).join(' ');
                const btn = h('button', {
                    type: 'button', class: cls, role: 'gridcell', 'data-date': key,
                    disabled: !available,
                    tabindex: available && key === focusKey ? '0' : '-1',
                    'aria-label': d.toLocaleDateString(CFG.locale, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }),
                    'aria-selected': key === state.date ? 'true' : 'false',
                    text: String(d.getDate()),
                    onclick: () => pickDate(key),
                    onkeydown: (e) => calKeys(e, key),
                });
                row.appendChild(btn);
            }
            rows.push(row);
        }
        grid.replaceChildren(...rows);
    }
    function calKeys(e, key) {
        const moves = { ArrowRight: 1, ArrowLeft: -1, ArrowDown: 7, ArrowUp: -7 };
        if (!(e.key in moves)) return;
        e.preventDefault();
        const d = fromIso(key);
        for (let step = 1; step <= 42; step++) {
            const next = new Date(d.getFullYear(), d.getMonth(), d.getDate() + moves[e.key] * step);
            const nk = iso(next);
            if (cache.dates.includes(nk)) {
                cal.focus = nk;
                if (next.getMonth() !== cal.month || next.getFullYear() !== cal.year) { cal.year = next.getFullYear(); cal.month = next.getMonth(); renderCalendar(); }
                $('cal-grid').querySelector(`[data-date="${nk}"]`)?.focus();
                return;
            }
        }
    }
    $('cal-prev').addEventListener('click', () => { cal.month -= 1; if (cal.month < 0) { cal.month = 11; cal.year -= 1; } renderCalendar(); });
    $('cal-next').addEventListener('click', () => { cal.month += 1; if (cal.month > 11) { cal.month = 0; cal.year += 1; } renderCalendar(); });

    function pickDate(key) {
        state.date = key; state.time = null; cal.focus = key;
        clearHold();
        stepEl('details').classList.add('is-hidden');
        renderCalendar();
        updateSummary();
        renderTimes();
    }

    // ---- times ------------------------------------------------------------
    async function renderTimes() {
        const box = $('times');
        $('times-count').textContent = '';
        $('times-title').textContent = state.date ? fmtDate(state.date) : @json(__('widget.select_time'));
        if (!state.date) { box.replaceChildren(h('div', { class: 'bk-times-empty', text: CFG.t.pickDate })); return; }
        box.replaceChildren(skeleton(2));
        try {
            const data = await fetchJson(`${CFG.urls.times}/${state.worker.id}/${state.date}?${workerParams()}`);
            const slots = data.timeSlots || [];
            box.replaceChildren();
            if (!slots.length) { box.appendChild(h('div', { class: 'bk-times-empty', text: CFG.t.noTimes })); return; }
            $('times-count').textContent = CFG.t.freeSlots.replace(':count', slots.length);
            const groups = [[CFG.t.morning, s => +s.start_time.slice(0, 2) < 12], [CFG.t.afternoon, s => { const hr = +s.start_time.slice(0, 2); return hr >= 12 && hr < 17; }], [CFG.t.evening, s => +s.start_time.slice(0, 2) >= 17]];
            groups.forEach(([label, test]) => {
                const items = slots.filter(test);
                if (!items.length) return;
                box.appendChild(h('div', { class: 'bk-times-group' }, [
                    h('div', { class: 'bk-times-group-label', text: label }),
                    h('div', { class: 'bk-times' }, items.map(s => h('button', {
                        type: 'button', class: `time-btn ${state.time?.start === s.start_time ? 'is-selected' : ''}`.trim(),
                        text: s.display || hm(s.start_time),
                        onclick: (e) => pickTime(e.currentTarget, s),
                    }))),
                ]));
            });
        } catch (e) { box.replaceChildren(h('div', { class: 'bk-times-empty', text: CFG.t.noTimes })); }
    }

    async function pickTime(btn, slot) {
        if (btn.classList.contains('is-busy')) return;
        btn.classList.add('is-busy');
        // "No preference": the times endpoint already picked a specific worker for this slot.
        if (state.worker?.isAny && slot.worker_id) {
            state.worker = { id: slot.worker_id, name: slot.worker_name || state.worker.name, isAny: true };
            setRow('worker', `${CFG.t.assignedWorker}: ${slot.worker_name}`);
        }
        const ok = await placeHold(slot.start_time);
        btn.classList.remove('is-busy');
        if (!ok) return;
        $('times').querySelectorAll('.time-btn.is-selected').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
        state.time = { start: slot.start_time, end: slot.end_time };
        collapse('datetime', `${fmtDate(state.date)}, ${hm(slot.start_time)} – ${hm(slot.end_time)}`);
        updateSummary();
        openStep('details');
        setTimeout(() => $('name').focus({ preventScroll: true }), 300);
    }

    // ---- slot hold --------------------------------------------------------
    async function placeHold(startTime) {
        try {
            // The hold endpoint sits behind the same anti-bot checks as the booking itself.
            const form = $('booking-form');
            const data = await fetchJson(CFG.urls.hold, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CFG.csrf },
                body: JSON.stringify({
                    worker_id: state.worker.id, service_id: state.service.id, date: state.date, start_time: hm(startTime),
                    bk_ts: form.elements.bk_ts?.value, bk_js: form.elements.bk_js?.value, website: form.elements.website?.value || '',
                }),
            });
            startCountdown(data.expires_at);
            return true;
        } catch (e) {
            if (e.status === 409) { toast(e.data?.message || CFG.t.slotTaken, 'error'); renderTimes(); return false; }
            console.warn('Slot hold skipped:', e.message); // never block a real booking because the hold failed
            return true;
        }
    }
    function clearHold() {
        if (holdTimer) { clearInterval(holdTimer); holdTimer = null; }
        $('hold').classList.add('is-hidden');
    }
    function startCountdown(expiresIso) {
        clearHold();
        if (!expiresIso) return;
        const end = new Date(expiresIso).getTime();
        const el = $('hold'), out = $('hold-timer');
        el.classList.remove('is-hidden');
        const tick = () => {
            const left = Math.max(0, Math.round((end - Date.now()) / 1000));
            out.textContent = `${Math.floor(left / 60)}:${pad(left % 60)}`;
            if (left <= 0) {
                clearHold();
                state.time = null;
                stepEl('details').classList.add('is-hidden');
                resetFrom('datetime'); openStep('datetime', false); renderCalendar(); renderTimes();
                toast(CFG.t.slotExpired, 'error');
            }
        };
        tick();
        holdTimer = setInterval(tick, 1000);
    }

    // ---- submit -----------------------------------------------------------
    $('booking-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.currentTarget;
        const err = $('bk-error');
        err.hidden = true;
        if (!form.reportValidity()) return;
        const submit = $('submit');
        submit.disabled = true;
        submit.querySelector('span').textContent = CFG.t.submitting;
        try {
            const data = await fetchJson(CFG.urls.book, { method: 'POST', headers: { 'X-CSRF-TOKEN': CFG.csrf }, body: new FormData(form) });
            clearHold();
            window.location.assign(data.redirect || @json(route('home')));
        } catch (ex) {
            const messages = ex.data?.errors ? Object.values(ex.data.errors).flat() : [ex.data?.message || CFG.t.failed];
            err.replaceChildren(h('ul', {}, messages.map(m => h('li', { text: m }))));
            err.hidden = false;
            err.scrollIntoView({ behavior: 'smooth', block: 'center' });
            submit.disabled = false;
            submit.querySelector('span').textContent = CFG.t.confirm;
            if (ex.status === 422 && /term|slot|čas|termín/i.test(messages.join(' '))) { renderTimes(); }
        }
    });

    // ---- boot -------------------------------------------------------------
    renumber();
    updateSummary();
    if (CFG.showLocation) { bindLocations(); openStep('location', false); }
    else { loadCategories(); }
})();
</script>
@endpush
