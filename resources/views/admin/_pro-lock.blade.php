{{-- Shown in place of a Pro-only control on the Free plan. --}}
<div class="alert pro-lock" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;margin:.5rem 0 .75rem;padding:.7rem .9rem;border:1px dashed var(--gold, #B9893B);border-radius:.6rem;background:rgba(185,137,59,.06)">
    <span style="display:inline-flex;align-items:center;gap:.35rem;font-weight:700;color:var(--gold, #B9893B);font-size:.8rem;letter-spacing:.04em;text-transform:uppercase">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
        {{ __('admin.pro.badge') }}
    </span>
    <span style="flex:1;min-width:12rem">{{ $text }}</span>
    <a href="{{ route('admin.billing') }}" class="btn btn-sm btn-primary">{{ __('admin.pro.upgrade') }}</a>
</div>
