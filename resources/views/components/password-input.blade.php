@props([
    'name' => null,
    'id' => null,
    'placeholder' => null,
    'autocomplete' => 'current-password',
    'required' => false,
])

<div class="relative">
    <input
        type="password"
        @if($name) name="{{ $name }}" @endif
        @if($id) id="{{ $id }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        autocomplete="{{ $autocomplete }}"
        @if($required) required @endif
        style="padding-right: 2.5rem;"
        {{ $attributes->merge(['class' => 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 [&::-ms-reveal]:hidden [&::-ms-clear]:hidden']) }}
    >
    <button type="button"
            data-password-toggle
            tabindex="-1"
            aria-label="Show password"
            title="Show password"
            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none cursor-pointer">
        {{-- Eye open (shown when password is hidden) --}}
        <svg class="eye-open-icon h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        {{-- Eye closed / off (shown when password is visible) --}}
        <svg class="eye-closed-icon hidden h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
            <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
            <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
            <path d="m2 2 20 20"/>
        </svg>
    </button>
</div>
