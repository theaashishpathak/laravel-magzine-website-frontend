@php
    $settings = app(\App\Services\SettingService::class);
    $siteName = (string) ($settings->get('header.brand_text') ?? ($settings->get('site.name') ?? 'BLOGGER4U'));
    $brandIcon = (string) ($settings->get('header.brand_icon') ?? 'sparkles');
    $logoType = (string) ($settings->get('header.logo_type') ?? 'text');
    $logoUrl = (string) ($settings->get('header.logo_url') ?? '');
    $logoHeight = (int) ($settings->get('header.logo_height') ?? 32);

    $showSearch = (bool) ($settings->get('header.show_search') ?? true);
    $showThemeToggle = (bool) ($settings->get('header.show_theme_toggle') ?? true);
    $isSticky = (bool) ($settings->get('header.sticky') ?? true);

    $showActionButton = (bool) ($settings->get('header.show_action_button') ?? true);
    $actionAuthText = (string) ($settings->get('header.action_auth_text') ?? 'Dashboard');
    $actionAuthUrl = (string) ($settings->get('header.action_auth_url') ?? route('dashboard'));
    $actionGuestText = (string) ($settings->get('header.action_guest_text') ?? 'Sign in');
    $actionGuestUrl = (string) ($settings->get('header.action_guest_url') ?? route('login'));
    $actionIcon = (string) ($settings->get('header.action_icon') ?? 'layout-dashboard');

    $localeResolver = app(\App\Support\LocaleResolver::class);
    $currentLocale = $localeResolver->current();

    // Query dynamic navigation items (with auto-seed fallback)
    $headerNavItems = \App\Models\NavigationItem::forLocation('header')
        ->topLevel()
        ->active()
        ->with(['children' => fn($q) => $q->active()->ordered()])
        ->ordered()
        ->get();

    if ($headerNavItems->isEmpty()) {
        \App\Models\NavigationItem::seedDefaults();
        $headerNavItems = \App\Models\NavigationItem::forLocation('header')
            ->topLevel()
            ->active()
            ->with(['children' => fn($q) => $q->active()->ordered()])
            ->ordered()
            ->get();
    }

    // Categories for the rich Mega Menu
    $categoriesForMegaMenu = \App\Models\Category::query()
        ->with('translations')
        ->withCount(['posts' => fn($q) => $q->where('status', \App\Enums\PostStatus::Published->value)])
        ->orderBy('sort_order')
        ->get();
@endphp

<header x-data="{
        mobileOpen: false,
        isDark: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.isDark = !this.isDark;
            document.documentElement.classList.toggle('dark', this.isDark);
            localStorage.setItem('crm-theme', this.isDark ? 'dark' : 'light');
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        }
    }"
    @keydown.window.cmd.k.prevent="$refs.headerSearchInput?.focus(); $refs.headerSearchInput?.select()"
    @keydown.window.ctrl.k.prevent="$refs.headerSearchInput?.focus(); $refs.headerSearchInput?.select()"
    x-effect="document.body.style.overflow = mobileOpen ? 'hidden' : ''"
    class="{{ $isSticky ? 'sticky top-0' : 'relative' }} z-40 transition-all duration-300">

    {{-- ───── Ambient Top Edge Glow (Refractive Laser Accent) ───── --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-indigo-500/60 to-purple-500/60 z-50"></div>

    {{-- ───── Frosted Glass Container ───── --}}
    <div class="{{ $isSticky ? 'bg-white/85 dark:bg-[#070913]/85 backdrop-blur-2xl shadow-xs dark:shadow-none' : 'bg-white dark:bg-[#0c0e18]' }} border-b border-slate-200/80 dark:border-white/[0.08] transition-colors duration-200">
        <div class="mx-auto flex max-w-[1360px] items-center justify-between px-4 py-2.5 sm:px-6 lg:px-8">
        
            {{-- ───── Brand / Logo (Left) ───── --}}
            <div class="flex items-center gap-3 min-w-0 shrink">
                <a href="{{ route('frontend.home') }}" wire:navigate class="flex items-center gap-2.5 sm:gap-3 group min-w-0">
                    @if ($logoType === 'image' && !empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="height: {{ $logoHeight }}px" class="w-auto max-h-8 object-contain">
                    @else
                        {{-- Vibrant Gradient Squircle Badge with Pulsing Status Dot --}}
                        <div class="relative flex items-center justify-center">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-500/25 ring-1 ring-white/20 group-hover:scale-105 group-hover:shadow-indigo-500/40 transition-all duration-300">
                                <i data-lucide="{{ $brandIcon }}" class="h-4.5 w-4.5"></i>
                            </span>
                            <span class="absolute -top-0.5 -right-0.5 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                            </span>
                        </div>
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-base sm:text-lg font-black uppercase tracking-wider sm:tracking-widest text-slate-900 dark:text-white truncate" style="font-family: 'Syne', sans-serif;">
                                {{ $siteName }}
                            </span>
                       
                        </div>
                    @endif
                </a>
            </div>

            {{-- ───── Center Navigation (Desktop) ───── --}}
            <nav class="header-nav hidden md:flex items-center gap-1 lg:gap-1.5 text-sm font-semibold">
                @foreach ($headerNavItems as $navItem)
                    @if ($navItem->children->isNotEmpty())
                        @php
                            $isCatDropdown = ($navItem->title === 'Categories');
                            $isCatActive = $isCatDropdown && (request()->is('categories*') || request()->is('category/*'));
                        @endphp
                        <div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
                            <div class="inline-flex items-center">
                                <button type="button" x-on:click="open = !open"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-semibold transition-all duration-200 cursor-pointer {{ $isCatActive ? 'bg-indigo-50 text-indigo-600 font-bold dark:bg-indigo-950/70 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-500/30 shadow-xs' : 'text-slate-600 hover:text-slate-950 dark:text-neutral-300 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-white/[0.06]' }}"
                                    aria-label="Toggle {{ $navItem->title }} menu">
                                    @if ($navItem->icon)
                                        <i data-lucide="{{ $navItem->icon }}" class="h-3.5 w-3.5 opacity-70"></i>
                                    @endif
                                    <span>{{ $navItem->title }}</span>
                                    <i data-lucide="chevron-down" class="h-3.5 w-3.5 transition-transform duration-200 opacity-60" x-bind:class="open && 'rotate-180 text-indigo-600 dark:text-indigo-400'"></i>
                                </button>
                            </div>

                            {{-- ───── Mega Menu Dropdown for Categories ───── --}}
                            @if ($isCatDropdown && $categoriesForMegaMenu->isNotEmpty())
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2 scale-98"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-2 scale-98"
                                    class="header-dropdown absolute left-1/2 -translate-x-1/2 lg:left-0 lg:translate-x-0 mt-3 w-[640px] lg:w-[720px] rounded-3xl border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/15 dark:border-white/10 dark:bg-[#0c0e18] z-50 overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                                    
                                    {{-- Dropdown Header Strip --}}
                                    <div class="px-2 pb-3 mb-2 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300">
                                                <i data-lucide="sparkles" class="h-3 w-3"></i>
                                                <span>Curated Topics</span>
                                            </span>
                                            <span class="text-xs text-slate-400 dark:text-neutral-500">12 Technology Categories</span>
                                        </div>
                                        <a href="{{ route('frontend.categories') }}" wire:navigate x-on:click="open = false"
                                           class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 inline-flex items-center gap-1 group">
                                            <span>View All</span>
                                            <i data-lucide="arrow-right" class="h-3 w-3 transition-transform group-hover:translate-x-0.5"></i>
                                        </a>
                                    </div>

                                    {{-- 3-Column Grid for the 12 Categories --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-1.5 max-h-[380px] overflow-y-auto pr-1">
                                        @foreach ($categoriesForMegaMenu as $cat)
                                            @php
                                                $catName = $cat->translate('name') ?? ('#' . $cat->id);
                                                $catSlug = $cat->translate('slug') ?? ('cat-' . $cat->id);
                                                $catColor = $cat->color ?: '#6366f1';
                                            @endphp
                                            <a href="{{ route('frontend.category', ['slug' => $catSlug]) }}" wire:navigate x-on:click="open = false"
                                               class="flex items-center gap-2.5 rounded-2xl p-2 transition-all duration-200 hover:bg-slate-100 dark:hover:bg-white/[0.08] group/item border border-transparent hover:border-slate-200/60 dark:hover:border-white/5">
                                                <span class="grid h-8.5 w-8.5 shrink-0 place-items-center rounded-xl transition-transform duration-200 group-hover/item:scale-110 shadow-2xs"
                                                      style="background-color: {{ $catColor }}18; color: {{ $catColor }};">
                                                    <i data-lucide="{{ $cat->icon ?: 'tag' }}" class="h-4 w-4"></i>
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-xs font-bold text-slate-800 dark:text-neutral-100 truncate group-hover/item:text-indigo-600 dark:group-hover/item:text-indigo-400 transition-colors">
                                                        {{ $catName }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 dark:text-neutral-500 font-medium">
                                                        {{ $cat->posts_count ?? 0 }} {{ Str::plural('article', $cat->posts_count ?? 0) }}
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>

                                    {{-- Dropdown Footer Hub Link --}}
                                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-white/10 bg-slate-50 dark:bg-[#121422] -mx-4 -mb-4 px-5 py-2.5 flex items-center justify-between text-xs">
                                        <span class="text-slate-500 dark:text-neutral-400 text-[11px]">
                                            Deep dives into AI, engineering, and modern code
                                        </span>
                                        <a href="{{ route('frontend.categories') }}" wire:navigate x-on:click="open = false"
                                           class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline inline-flex items-center gap-1">
                                            <span>Explore Directory Hub</span>
                                            <i data-lucide="arrow-right" class="h-3 w-3"></i>
                                        </a>
                                    </div>
                                </div>
                            @else
                                {{-- Fallback Standard Dropdown for custom items --}}
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="header-dropdown absolute left-0 mt-3 w-60 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-[#262832] dark:bg-[#181920] z-50">
                                    @foreach ($navItem->children as $child)
                                        <a href="{{ $child->url }}" wire:navigate target="{{ $child->target ?? '_self' }}"
                                           class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-[#22242e] text-slate-700 dark:text-neutral-200">
                                            <span class="flex items-center gap-2">
                                                @if ($child->icon)
                                                    <i data-lucide="{{ $child->icon }}" class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400"></i>
                                                @endif
                                                <span>{{ $child->title }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        @php
                            $isActive = request()->is(ltrim($navItem->url, '/')) || ($navItem->url === '/' && request()->is('/'));
                        @endphp
                        <a href="{{ $navItem->url }}" wire:navigate target="{{ $navItem->target ?? '_self' }}"
                            class="relative px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-semibold transition-all duration-200 {{ $isActive ? 'bg-indigo-50 text-indigo-600 font-bold dark:bg-indigo-950/70 dark:text-indigo-400 border border-indigo-200/60 dark:border-indigo-500/30 shadow-xs' : 'text-slate-600 hover:text-slate-950 dark:text-neutral-300 dark:hover:text-white hover:bg-slate-100/80 dark:hover:bg-white/[0.06]' }}">
                            @if ($navItem->icon)
                                <i data-lucide="{{ $navItem->icon }}" class="h-3.5 w-3.5 inline mr-1 opacity-70"></i>
                            @endif
                            <span>{{ $navItem->title }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            {{-- ───── Right Controls (Search, Theme Switcher, Action CTA) ───── --}}
            <div class="flex items-center gap-2 sm:gap-2.5 md:gap-3 shrink-0">
                {{-- Inline Writable Search Bar --}}
                @if ($showSearch)
                    <form action="{{ route('frontend.search') }}" method="GET" class="relative flex items-center group">
                        <div class="relative flex items-center">
                            <i data-lucide="search" class="pointer-events-none absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors"></i>
                            <input
                                type="text"
                                name="q"
                                x-ref="headerSearchInput"
                                value="{{ request('q') }}"
                                placeholder="Search articles..."
                                autocomplete="off"
                                class="w-28 xs:w-36 sm:w-44 md:w-48 lg:w-60 xl:w-68 rounded-full border border-slate-200/90 bg-slate-100/70 py-1.5 pl-7.5 sm:pl-8.5 pr-7 sm:pr-8 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none transition-all duration-200 focus:w-36 xs:focus:w-48 sm:focus:w-56 md:focus:w-60 lg:focus:w-72 xl:focus:w-80 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-white/[0.05] dark:text-neutral-100 dark:placeholder-neutral-500 dark:focus:border-indigo-400 dark:focus:bg-[#0c0e18]"
                            />
                            <div class="pointer-events-none absolute right-2 sm:right-2.5 top-1/2 -translate-y-1/2 flex items-center">
                                <kbd class="hidden lg:inline-flex items-center rounded border border-slate-200/80 bg-white px-1.5 py-0.5 text-[9px] font-mono font-medium text-slate-400 shadow-2xs group-focus-within:hidden dark:border-white/10 dark:bg-white/[0.08] dark:text-neutral-400">⌘K</kbd>
                                <button type="submit" class="hidden group-focus-within:inline-flex pointer-events-auto text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 p-0.5 cursor-pointer" title="Search">
                                    <i data-lucide="arrow-right" class="h-3 w-3"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                {{-- Modern Dual-Icon Glass Theme Switcher --}}
                @if ($showThemeToggle)
                    {{-- Mobile mini icon --}}
                    <button type="button" x-on:click="toggleTheme()"
                        class="grid h-8.5 w-8.5 place-items-center rounded-full text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-neutral-400 dark:hover:bg-white/[0.08] dark:hover:text-white sm:hidden cursor-pointer"
                        title="Toggle Theme" aria-label="Toggle Theme">
                        <i data-lucide="moon" class="h-4 w-4" x-show="!isDark"></i>
                        <i data-lucide="sun" class="h-4 w-4 text-amber-500" x-show="isDark" x-cloak></i>
                    </button>

                    {{-- Sliding Dual-Icon Capsule on sm+ --}}
                    <button type="button" x-on:click="toggleTheme()"
                        class="relative hidden sm:inline-flex h-8 w-[60px] items-center justify-between rounded-full border border-slate-200/90 bg-slate-100/80 p-0.5 shadow-2xs transition-colors duration-200 dark:border-white/10 dark:bg-white/[0.06] cursor-pointer"
                        title="Toggle Theme" aria-label="Toggle Theme">
                        <span class="absolute top-0.5 left-0.5 h-7 w-7 rounded-full bg-white shadow-xs transition-transform duration-200 ease-in-out dark:bg-slate-900 ring-1 ring-black/5 dark:ring-white/10"
                              :class="isDark ? 'translate-x-7' : 'translate-x-0'"
                              aria-hidden="true"></span>
                        <span class="relative z-10 flex h-7 w-7 items-center justify-center transition-colors duration-200"
                              :class="!isDark ? 'text-amber-500 font-bold' : 'text-slate-400 dark:text-neutral-500'">
                            <i data-lucide="sun" class="h-3.5 w-3.5"></i>
                        </span>
                        <span class="relative z-10 flex h-7 w-7 items-center justify-center transition-colors duration-200"
                              :class="isDark ? 'text-indigo-400 font-bold' : 'text-slate-400 dark:text-neutral-500'">
                            <i data-lucide="moon" class="h-3.5 w-3.5"></i>
                        </span>
                    </button>
                @endif

                {{-- Primary Action CTA: High-Energy Gradient Button --}}
                @if ($showActionButton)
                    @auth
                        <a href="{{ $actionAuthUrl }}" wire:navigate
                            class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-1.5 text-xs font-bold text-white shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/35 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            @if ($actionIcon)
                                <i data-lucide="{{ $actionIcon }}" class="h-3.5 w-3.5"></i>
                            @endif
                            <span>{{ $actionAuthText }}</span>
                        </a>
                    @else
                        <a href="{{ $actionGuestUrl }}" wire:navigate
                            class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-1.5 text-xs font-bold text-white shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/35 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            <span>{{ $actionGuestText }}</span>
                            <i data-lucide="arrow-right" class="h-3 w-3"></i>
                        </a>
                    @endauth
                @endif

                {{-- Mobile Drawer Trigger --}}
                <button type="button" x-on:click="mobileOpen = true"
                    class="grid h-8.5 w-8.5 place-items-center rounded-xl border border-slate-200/80 bg-slate-50 text-slate-700 hover:bg-slate-100 md:hidden dark:border-white/10 dark:bg-white/[0.04] dark:text-neutral-200 dark:hover:bg-white/[0.08] cursor-pointer"
                    aria-label="Open menu">
                    <i data-lucide="menu" class="h-4.5 w-4.5"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ───── Mobile Drawer ───── --}}
    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 md:hidden"
        x-on:keydown.escape.window="mobileOpen = false">
        <div x-show="mobileOpen" x-transition.opacity x-on:click="mobileOpen = false"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40"></div>

        <aside x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 flex h-screen h-[100dvh] w-[85%] max-w-sm flex-col bg-white dark:bg-[#0c0e18] shadow-2xl border-r border-slate-200 dark:border-white/10">
            
            {{-- Mobile Drawer Header --}}
            <div class="flex items-center justify-between border-b border-slate-200/80 px-4 py-3.5 dark:border-white/10">
                <div class="flex items-center gap-2">
                    <span class="grid h-7 w-7 place-items-center rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-xs">
                        <i data-lucide="{{ $brandIcon }}" class="h-3.5 w-3.5"></i>
                    </span>
                    <span class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                        {{ $siteName }}
                    </span>
                </div>
                <button type="button" x-on:click="mobileOpen = false" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 dark:text-neutral-400 dark:hover:bg-white/[0.08] dark:hover:text-white cursor-pointer">
                    <i data-lucide="x" class="h-4.5 w-4.5"></i>
                </button>
            </div>
 
            {{-- Mobile Drawer Search --}}
            @if ($showSearch)
                <div class="px-4 pt-3 pb-1 border-b border-slate-100 dark:border-white/5">
                    <form action="{{ route('frontend.search') }}" method="GET" class="relative">
                        <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"></i>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Search articles & topics..."
                            class="w-full rounded-xl border border-slate-200/90 bg-slate-50 py-2 pl-9 pr-3 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 dark:border-white/10 dark:bg-white/[0.04] dark:text-neutral-100 dark:placeholder-neutral-500"
                        />
                    </form>
                </div>
            @endif

            {{-- Mobile Drawer Navigation --}}
            <nav class="mobile-nav flex-1 space-y-1.5 overflow-y-auto px-4 py-4 text-sm font-semibold">
                @foreach ($headerNavItems as $navItem)
                    @if ($navItem->children->isNotEmpty())
                        <div x-data="{ open: true }" class="space-y-1">
                            <button type="button" x-on:click="open = !open"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2.5 hover:bg-slate-100/80 dark:hover:bg-white/[0.06] text-slate-800 dark:text-neutral-200 cursor-pointer">
                                <span class="flex items-center gap-2">
                                    @if ($navItem->icon)
                                        <i data-lucide="{{ $navItem->icon }}" class="h-4 w-4 text-indigo-600 dark:text-indigo-400"></i>
                                    @endif
                                    <span>{{ $navItem->title }}</span>
                                </span>
                                <i data-lucide="chevron-down" class="h-4 w-4 transition-transform duration-200 opacity-60" x-bind:class="open && 'rotate-180'"></i>
                            </button>
                            <div x-show="open" x-cloak class="pl-3 space-y-1 border-l-2 border-indigo-200/50 dark:border-indigo-500/20 ml-3">
                                @if ($navItem->title === 'Categories' && $categoriesForMegaMenu->isNotEmpty())
                                    @foreach ($categoriesForMegaMenu as $cat)
                                        @php
                                            $catName = $cat->translate('name') ?? ('#' . $cat->id);
                                            $catSlug = $cat->translate('slug') ?? ('cat-' . $cat->id);
                                            $catColor = $cat->color ?: '#6366f1';
                                        @endphp
                                        <a href="{{ route('frontend.category', ['slug' => $catSlug]) }}" wire:navigate x-on:click="mobileOpen = false"
                                            class="flex items-center justify-between rounded-lg px-2.5 py-1.5 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-white/[0.06] text-slate-700 dark:text-neutral-300">
                                            <span class="flex items-center gap-2 truncate">
                                                <span class="h-2 w-2 rounded-full shrink-0" style="background-color: {{ $catColor }};"></span>
                                                <span class="truncate">{{ $catName }}</span>
                                            </span>
                                            <span class="text-[10px] text-slate-400 shrink-0 font-medium">{{ $cat->posts_count ?? 0 }}</span>
                                        </a>
                                    @endforeach
                                    <a href="{{ route('frontend.categories') }}" wire:navigate x-on:click="mobileOpen = false"
                                        class="flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <span>View All 12 Categories</span>
                                        <i data-lucide="arrow-right" class="h-3 w-3"></i>
                                    </a>
                                @else
                                    @foreach ($navItem->children as $child)
                                        <a href="{{ $child->url }}" wire:navigate target="{{ $child->target ?? '_self' }}" x-on:click="mobileOpen = false"
                                            class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-white/[0.06] text-slate-700 dark:text-neutral-300">
                                            @if ($child->icon)
                                                <i data-lucide="{{ $child->icon }}" class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400"></i>
                                            @endif
                                            <span>{{ $child->title }}</span>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @else
                        <a href="{{ $navItem->url }}" wire:navigate target="{{ $navItem->target ?? '_self' }}" x-on:click="mobileOpen = false"
                            class="flex items-center justify-between rounded-xl px-3 py-2.5 hover:bg-slate-100/80 dark:hover:bg-white/[0.06] text-slate-800 dark:text-neutral-200">
                            <span class="flex items-center gap-2">
                                @if ($navItem->icon)
                                    <i data-lucide="{{ $navItem->icon }}" class="h-4 w-4"></i>
                                @endif
                                <span>{{ $navItem->title }}</span>
                            </span>
                            <i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-400"></i>
                        </a>
                    @endif
                @endforeach
            </nav>

            {{-- Mobile Drawer Footer --}}
            <div class="border-t border-slate-200/80 p-4 dark:border-white/10 space-y-2.5 bg-slate-50/50 dark:bg-white/[0.02]">
                @if ($showThemeToggle)
                    <button type="button" x-on:click="toggleTheme()"
                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-bold text-slate-700 dark:border-white/10 dark:bg-white/[0.04] dark:text-neutral-300 shadow-2xs cursor-pointer">
                        <span x-show="!isDark" class="inline-flex items-center gap-1.5"><i data-lucide="moon" class="h-3.5 w-3.5"></i> Switch to Dark Mode</span>
                        <span x-show="isDark" x-cloak class="inline-flex items-center gap-1.5"><i data-lucide="sun" class="h-3.5 w-3.5 text-amber-500"></i> Switch to Light Mode</span>
                    </button>
                @endif

                @if ($showActionButton)
                    @auth
                        <a href="{{ $actionAuthUrl }}" wire:navigate class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-500/20">
                            @if ($actionIcon)
                                <i data-lucide="{{ $actionIcon }}" class="h-3.5 w-3.5"></i>
                            @endif
                            <span>{{ $actionAuthText }}</span>
                        </a>
                    @else
                        <a href="{{ $actionGuestUrl }}" wire:navigate class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-500/20">
                            <span>{{ $actionGuestText }}</span>
                            <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                        </a>
                    @endauth
                @endif
            </div>
        </aside>
    </div>
</header>
