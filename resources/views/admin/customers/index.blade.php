@extends('layouts.admin')

@section('header_title', __('ui.zakaznici'))

@push('styles')
<style>
    .drawer-backdrop { position: fixed; inset: 0; z-index: 60; background: rgba(31, 26, 20, .35); }
    .drawer { position: fixed; top: 0; right: 0; bottom: 0; z-index: 61; width: min(30rem, 100vw); background: var(--card); border-left: 1px solid var(--line); box-shadow: var(--shadow-pop); display: flex; flex-direction: column; }
    .drawer-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1.25rem; border-bottom: 1px solid var(--line-2); }
    .drawer-body { flex: 1; overflow-y: auto; padding: 1.25rem; }
    .drawer-foot { padding: 1rem 1.25rem; border-top: 1px solid var(--line-2); display: flex; gap: .5rem; flex-wrap: wrap; }
    .drawer-enter-active, .drawer-leave-active { transition: transform .22s ease; }
    .drawer-enter-from, .drawer-leave-to { transform: translateX(100%); }
</style>
@endpush

@php
    $statusBadge = fn (string $status) => match ($status) {
        'pending' => ['badge-warn', __('ui.status_pending')],
        'confirmed' => ['badge-ok', __('ui.status_confirmed')],
        'completed' => ['badge-muted', __('ui.status_completed')],
        'cancelled' => ['badge-bad', __('ui.status_cancelled')],
        default => ['badge-muted', $status],
    };
@endphp

@section('content')
<div x-data="customersPage()" @keydown.escape.window="closeDrawer(); composer = false">
    <div class="page-head">
        <div>
            <h2 class="page-title">{{ __('ui.zakaznici') }}</h2>
            <p class="page-sub">{{ __('ui.kazdy_kto_si_u_vas_rezervoval') }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.customers.export') }}" class="btn btn-secondary">{{ __('ui.export_csv') }}</a>
            <button type="button" class="btn btn-primary" :disabled="selected.length === 0" @click="composer = true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/></svg>
                <span x-text="selected.length ? `${@js(__('ui.napisat_e_mail'))} (${selected.length})` : @js(__('ui.napisat_e_mail'))"></span>
            </button>
        </div>
    </div>

    <div class="grid-3" style="margin-bottom:1.25rem">
        <div class="stat"><p class="stat-label">{{ __('ui.zakaznikov_spolu') }}</p><p class="stat-value">{{ $stats['total'] }}</p></div>
        <div class="stat"><p class="stat-label">{{ __('ui.vracaju_sa') }}</p><p class="stat-value">{{ $stats['repeat'] }}</p><p class="stat-note">{{ __('ui.viac_ako_jedna_rezervacia') }}</p></div>
        <div class="stat"><p class="stat-label">{{ __('ui.novi_za_30_dni') }}</p><p class="stat-value">{{ $stats['recent'] }}</p></div>
    </div>

    @if($customers->isEmpty())
        <div class="card"><div class="empty"><h3>{{ __('ui.zatial_ziadni_zakaznici') }}</h3><p>{{ __('ui.zoznam_sa_naplni_s_prvou_rezervaciou') }}</p></div></div>
    @else
        <div class="toolbar">
            <label class="search">
                <span class="sr-only">{{ __('ui.hladat_zakaznika') }}</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                <input class="input input-sm" type="search" placeholder="{{ __('ui.meno_e_mail_alebo_telefon') }}" x-model="q" style="min-width:18rem">
            </label>
            <label class="check" style="margin-left:auto">
                <input type="checkbox" @change="toggleAll($event.target.checked)" :checked="selected.length && selected.length === visibleEmails().length">
                <span>{{ __('ui.vybrat_zobrazenych') }}</span>
            </label>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:2.5rem"></th>
                        <th>{{ __('ui.zakaznik') }}</th>
                        <th>{{ __('ui.telefon') }}</th>
                        <th class="is-num">{{ __('ui.rezervacie') }}</th>
                        <th>{{ __('ui.posledna_navsteva') }}</th>
                        <th style="text-align:right">{{ __('ui.akcie') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        @php
                            $last = $customer->last_booking;
                            $search = mb_strtolower(($customer->customer_name ?? '').' '.$customer->customer_email.' '.($customer->customer_phone ?? ''));
                        @endphp
                        <tr x-show="!q || $el.dataset.search.includes(q.toLowerCase())" data-search="{{ $search }}" data-email="{{ $customer->customer_email }}">
                            <td><input type="checkbox" :value="@js($customer->customer_email)" x-model="selected" style="width:1rem;height:1rem;accent-color:var(--gold)" aria-label="{{ __('ui.select_name', ['name' => $customer->customer_name]) }}"></td>
                            <td>
                                <button type="button" class="link" style="text-align:left;background:none;border:0;padding:0;font:inherit;font-weight:600;color:var(--ink)" @click="openDrawer(@js($customer->customer_email))">{{ $customer->customer_name ?: 'Bez mena' }}</button>
                                <div class="hint">{{ $customer->customer_email }}</div>
                            </td>
                            <td>@if($customer->customer_phone)<a class="link" style="font-weight:500" href="tel:{{ preg_replace('/\s+/', '', $customer->customer_phone) }}">{{ $customer->customer_phone }}</a>@else –@endif</td>
                            <td class="is-num">{{ $customer->bookings_count }}</td>
                            <td>
                                @if($last)
                                    @php [$badge, $label] = $statusBadge($last->status); @endphp
                                    <div>{{ \Carbon\Carbon::parse($last->date)->format('j. n. Y') }} <span class="hint" style="display:inline">{{ substr($last->start_time, 0, 5) }}</span></div>
                                    <div class="hint">{{ $last->service?->name }} · <span class="badge {{ $badge }}" style="padding:.05rem .45rem">{{ $label }}</span></div>
                                @else
                                    –
                                @endif
                            </td>
                            <td>
                                <div class="row-actions">
                                    <button type="button" class="btn btn-secondary" @click="openDrawer(@js($customer->customer_email))">{{ __('ui.historia') }}</button>
                                    <a href="{{ route('admin.blacklist.create', ['email' => $customer->customer_email, 'phone' => $customer->customer_phone, 'name' => $customer->customer_name]) }}" class="btn btn-ghost" style="color:var(--bad)">{{ __('ui.blokovat') }}</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Customer drawer --}}
    <template x-teleport="body">
        <div>
            <div x-show="drawer" x-transition.opacity class="drawer-backdrop" @click="closeDrawer()" x-cloak></div>
            <aside x-show="drawer" x-transition:enter="drawer-enter-active" x-transition:enter-start="drawer-enter-from" x-transition:leave="drawer-leave-active" x-transition:leave-end="drawer-leave-to" class="drawer" role="dialog" aria-modal="true" x-cloak>
                <div class="drawer-head">
                    <div style="min-width:0">
                        <h3 class="modal-title" x-text="detail?.summary?.name || @js(__('ui.zakaznik'))"></h3>
                        <p class="card-sub" x-text="detail?.summary?.email"></p>
                        <p class="card-sub" x-text="detail?.summary?.phone"></p>
                    </div>
                    <button type="button" class="icon-btn" aria-label="{{ __('ui.zavriet') }}" @click="closeDrawer()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
                <div class="drawer-body">
                    <template x-if="loading"><p class="hint">{{ __('ui.nacitavam') }}</p></template>
                    <template x-if="!loading && detail">
                        <div>
                            <template x-if="blacklist?.is_blacklisted">
                                <div class="alert alert-bad">
                                    <div>
                                        <strong>{{ __('ui.zakaznik_je_blokovany') }}</strong>
                                        <template x-for="entry in blacklist.entries" :key="entry.id">
                                            <div x-text="`${entry.severity_label} · ${entry.reason_category_label}${entry.reason ? ' · ' + entry.reason : ''}`"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <div class="grid-3" style="gap:.6rem;margin-bottom:1.25rem">
                                <div class="stat" style="padding:.75rem .9rem"><p class="stat-label">{{ __('ui.rezervacie') }}</p><p class="stat-value" style="font-size:1.25rem" x-text="detail.summary.total_bookings"></p></div>
                                <div class="stat" style="padding:.75rem .9rem"><p class="stat-label">{{ __('ui.dokoncene') }}</p><p class="stat-value" style="font-size:1.25rem" x-text="detail.bookings.filter(b => b.status === 'completed').length"></p></div>
                                <div class="stat" style="padding:.75rem .9rem"><p class="stat-label">{{ __('ui.zrusene') }}</p><p class="stat-value" style="font-size:1.25rem" x-text="detail.bookings.filter(b => b.status === 'cancelled').length"></p></div>
                            </div>
                            <h4 class="section-title">{{ __('ui.historia_rezervacii') }}</h4>
                            <div class="list">
                                <template x-for="booking in detail.bookings" :key="booking.id">
                                    <div class="list-item">
                                        <div class="list-main">
                                            <p class="list-title" x-text="booking.service?.name || @js(__('ui.sluzba'))"></p>
                                            <p class="list-sub" x-text="`${fmtDate(booking.date)} ${booking.start_time.slice(0,5)} – ${booking.end_time.slice(0,5)} · ${booking.worker?.name || ''}`"></p>
                                            <p class="list-sub" x-show="booking.notes" x-text="booking.notes"></p>
                                        </div>
                                        <span class="badge" :class="statusClass(booking.status)" x-text="statusLabel(booking.status)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="drawer-foot">
                    <button type="button" class="btn btn-primary btn-sm" @click="selected = [detail.summary.email]; composer = true; drawer = false">{{ __('ui.napisat_e_mail') }}</button>
                    <a :href="blacklistUrl()" class="btn btn-secondary btn-sm">{{ __('ui.spravovat_blokovanie') }}</a>
                    <form method="POST" :action="@js(url()->route('admin.customers.index')) + '/' + encodeURIComponent(detail?.summary?.email || '')" style="margin-left:auto" onsubmit="return confirm({{ Js::from(__('ui.confirm_delete_customer')) }})">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--bad)">{{ __('ui.delete_customer_data') }}</button>
                    </form>
                </div>
            </aside>
        </div>
    </template>

    {{-- E-mail composer --}}
    <template x-teleport="body">
        <div x-show="composer" class="modal-backdrop" @click.self="composer = false" x-cloak>
            <div class="modal modal-lg" role="dialog" aria-modal="true">
                <form method="POST" action="{{ route('admin.customers.email') }}">
                    @csrf
                    <div class="modal-head">
                        <div>
                            <h3 class="modal-title">{{ __('ui.novy_e_mail') }}</h3>
                            <p class="card-sub" x-text="`${@js(__('ui.recipients'))} ${selected.length}. ${@js(__('ui.sent_from_address'))} ${@js(\App\Models\BusinessSetting::get('support_email', config('mail.from.address')))}.`"></p>
                        </div>
                        <button type="button" class="icon-btn" aria-label="{{ __('ui.zavriet') }}" @click="composer = false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <template x-for="email in selected" :key="email"><input type="hidden" name="recipients[]" :value="email"></template>
                        <div class="flex flex-wrap gap-1" style="margin-bottom:1rem">
                            <template x-for="email in selected" :key="'chip-' + email">
                                <span class="chip">
                                    <span x-text="email"></span>
                                    <button type="button" @click="selected = selected.filter(e => e !== email)" aria-label="{{ __('ui.odobrat') }}" style="border:0;background:none;color:var(--muted);cursor:pointer;line-height:1">×</button>
                                </span>
                            </template>
                        </div>
                        <div class="field" style="margin-bottom:1rem">
                            <label for="subject">{{ __('ui.predmet') }}</label>
                            <input class="input" type="text" id="subject" name="subject" required maxlength="255" value="{{ old('subject') }}">
                        </div>
                        <div class="field">
                            <label for="body">{{ __('ui.sprava') }}</label>
                            <textarea class="textarea" id="body" name="body" rows="8" required placeholder="{{ __('ui.dobry_den_10_10') }}">{{ old('body') }}</textarea>
                            <p class="hint">{{ __('ui.text_sa_odosle_tak_ako_ho') }}</p>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn btn-secondary" @click="composer = false">{{ __('ui.zrusit') }}</button>
                        <button type="submit" class="btn btn-primary" :disabled="selected.length === 0">{{ __('ui.odoslat') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('customersPage', () => ({
            q: '',
            selected: [],
            composer: @json($errors->has('recipients') || $errors->has('subject') || $errors->has('body')),
            drawer: false,
            loading: false,
            detail: null,
            blacklist: null,
            visibleEmails() {
                return [...document.querySelectorAll('tbody tr[data-email]')].filter(row => row.offsetParent !== null).map(row => row.dataset.email);
            },
            toggleAll(checked) {
                this.selected = checked ? this.visibleEmails() : [];
            },
            async openDrawer(email) {
                this.drawer = true; this.loading = true; this.detail = null; this.blacklist = null;
                try {
                    const [detail, blacklist] = await Promise.all([
                        fetch(@json(route('admin.customers.show', ['customer' => '__EMAIL__'])).replace('__EMAIL__', encodeURIComponent(email)) + '?limit=50', { headers: { Accept: 'application/json' } }).then(r => r.json()),
                        fetch(@json(route('admin.blacklist.customer-status', ['email' => '__EMAIL__'])).replace('__EMAIL__', encodeURIComponent(email)), { headers: { Accept: 'application/json' } }).then(r => r.json()).catch(() => null),
                    ]);
                    this.detail = detail; this.blacklist = blacklist;
                } finally {
                    this.loading = false;
                }
            },
            closeDrawer() { this.drawer = false; },
            blacklistUrl() {
                if (!this.detail) return @json(route('admin.blacklist.index'));
                if (this.blacklist?.is_blacklisted && this.blacklist.entries[0]) return @json(route('admin.blacklist.edit', ['blacklist' => '__ID__'])).replace('__ID__', this.blacklist.entries[0].id);
                const params = new URLSearchParams({ email: this.detail.summary.email || '', phone: this.detail.summary.phone || '', name: this.detail.summary.name || '' });
                return @json(route('admin.blacklist.create')) + '?' + params.toString();
            },
            fmtDate(iso) { const [y, m, d] = String(iso).slice(0, 10).split('-'); return `${Number(d)}. ${Number(m)}. ${y}`; },
            statusLabel(s) { return ({ pending: @js(__('ui.status_pending')), confirmed: @js(__('ui.status_confirmed')), completed: @js(__('ui.status_completed')), cancelled: @js(__('ui.status_cancelled')), deleted: @js(__('ui.status_deleted')) })[s] || s; },
            statusClass(s) { return ({ pending: 'badge-warn', confirmed: 'badge-ok', completed: 'badge-muted', cancelled: 'badge-bad' })[s] || 'badge-muted'; },
        }));
    });
</script>
@endpush
