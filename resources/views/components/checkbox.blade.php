@php
    $id = $id ?? uniqid();
    $name = $name ?? '';
    $value = $value ?? '';
    $checked = $checked ?? false;
    $label = $label ?? '';
    $class = $class ?? '';
    $size = $size ?? 'normal'; // normal, small
    $variant = $variant ?? ''; // inline
@endphp

<div class="checkbox-wrapper-4 {{ $size === 'small' ? 'small' : '' }} {{ $variant }} {{ $class }}">
    <input class="inp-cbx" 
           id="{{ $id }}" 
           type="checkbox" 
           name="{{ $name }}" 
           value="{{ $value }}"
           {{ $checked ? 'checked' : '' }}
           {{ $attributes ?? '' }}
    />
    <label class="cbx" for="{{ $id }}">
        <span>
            <svg width="12px" height="10px">
                <use xlink:href="#check-4"></use>
            </svg>
        </span>
        <span>{{ $label }}</span>
    </label>
    
    @once
    <svg class="inline-svg">
        <symbol id="check-4" viewbox="0 0 12 10">
            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
        </symbol>
    </svg>
    @endonce
</div>