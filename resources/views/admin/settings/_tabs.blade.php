<nav class="tabs" aria-label="{{ __('ui.nastavenia') }}">
    <a href="{{ route('admin.settings') }}" class="tab {{ request()->routeIs('admin.settings') ? 'is-active' : '' }}">{{ __('ui.firma_a_vzhlad') }}</a>
    <a href="{{ route('admin.business-settings') }}" class="tab {{ request()->routeIs('admin.business-settings*') ? 'is-active' : '' }}">{{ __('ui.rezervacie') }}</a>
    <a href="{{ route('admin.email-templates.index') }}" class="tab {{ request()->routeIs('admin.email-templates.*') ? 'is-active' : '' }}">{{ __('ui.e_maily') }}</a>
</nav>
