@php
    use App\Helpers\LanguageHelper;
    
    $showSwitcher = LanguageHelper::shouldShowSwitcher();
    $currentLocale = LanguageHelper::getCurrentLocale();
    $currentLocaleInfo = LanguageHelper::getCurrentLocaleInfo();
    $availableLanguages = LanguageHelper::getAvailableLanguagesWithInfo();
    $position = LanguageHelper::getSwitcherPosition();
    
    // Get current URL to preserve tenant prefix
    $currentUrl = url()->current();
    $languageSwitchUrl = url('/language/switch');
@endphp

@if($showSwitcher && count($availableLanguages) > 1)
<div class="language-switcher relative inline-block text-left" 
     x-data="{ open: false }" 
     @click.outside="open = false"
     data-position="{{ $position }}">
    
    {{-- Trigger button --}}
    <button type="button" 
            @click="open = !open"
            class="language-trigger inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
            aria-expanded="true" 
            aria-haspopup="true">
        <span class="text-base">{{ $currentLocaleInfo['flag'] }}</span>
        <span class="hidden sm:inline">{{ $currentLocaleInfo['native'] }}</span>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" 
             :class="{ 'rotate-180': open }"
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    {{-- Dropdown menu --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="language-dropdown absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         role="menu" 
         aria-orientation="vertical" 
         tabindex="-1"
         style="display: none;">
        
        <div class="py-1" role="none">
            @foreach($availableLanguages as $locale => $info)
                <button type="button"
                        onclick="switchLanguage('{{ $locale }}')"
                        class="language-option w-full text-left px-4 py-2 text-sm flex items-center gap-3 transition-colors duration-150
                               {{ $currentLocale === $locale ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700 hover:bg-gray-100' }}"
                        role="menuitem" 
                        tabindex="-1">
                    <span class="text-base">{{ $info['flag'] }}</span>
                    <span>{{ $info['native'] }}</span>
                    @if($currentLocale === $locale)
                        <svg class="ml-auto w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>

<style>
.language-switcher {
    --primary-50: #f0fdf4;
    --primary-500: #22c55e;
    --primary-600: #16a34a;
    --primary-700: #15803d;
}

.language-switcher .language-trigger:focus {
    --tw-ring-color: var(--primary-500);
}

.language-switcher .language-option.bg-primary-50 {
    background-color: var(--primary-50);
}

.language-switcher .language-option.text-primary-700 {
    color: var(--primary-700);
}

.language-switcher .text-primary-600 {
    color: var(--primary-600);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .language-switcher .language-trigger {
        background-color: #374151;
        border-color: #4b5563;
        color: #e5e7eb;
    }
    
    .language-switcher .language-trigger:hover {
        background-color: #4b5563;
    }
    
    .language-switcher .language-dropdown {
        background-color: #374151;
        border: 1px solid #4b5563;
    }
    
    .language-switcher .language-option {
        color: #e5e7eb;
    }
    
    .language-switcher .language-option:hover {
        background-color: #4b5563;
    }
}
</style>

<script>
function switchLanguage(locale) {
    // Create a form and submit it to ensure proper session handling
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ url('/language/switch') }}';
    
    const localeInput = document.createElement('input');
    localeInput.type = 'hidden';
    localeInput.name = 'locale';
    localeInput.value = locale;
    form.appendChild(localeInput);
    
    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    form.appendChild(tokenInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endif

