<div class="mx-auto w-full space-y-7">
    @php($me = $this->me)
    @php($stats = $this->siteStats)
    @php($alerts = $this->alerts)

    {{-- ================================================================
         1. HERO BANNER
    ================================================================ --}}
    <div class="relative rounded-3xl border border-indigo-100/80 bg-gradient-to-r from-[#EDE9FE] via-[#E0E7FF]/90 to-[#EDE9FE] p-6 shadow-xs sm:p-8 dark:border-slate-800 dark:from-[#0F172A] dark:via-[#111827] dark:to-[#0F172A]">
        {{-- Background graphics container: keeps artwork and gradients clipped to rounded corners without clipping dropdowns --}}
        <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden rounded-3xl">
            {{-- Light mode 3D neural brain & liquid ribbon wave graphic --}}
            <div class="pointer-events-none absolute inset-y-0 right-0 w-full sm:w-4/5 md:w-3/5 lg:w-1/2 bg-cover bg-right bg-no-repeat dark:hidden"
                style="background-image: url('{{ asset('images/hero-ai-banner.jpg') }}'); mask-image: linear-gradient(to right, transparent 0%, black 35%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 35%);">
            </div>

            {{-- Dark mode 3D neural brain & liquid ribbon wave graphic --}}
            <div class="pointer-events-none absolute inset-y-0 right-0 hidden dark:block w-full sm:w-4/5 md:w-3/5 lg:w-1/2 bg-cover bg-right bg-no-repeat"
                style="background-image: url('{{ asset('images/hero-ai-banner-dark.jpg') }}'); mask-image: linear-gradient(to right, transparent 0%, black 35%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 35%);">
            </div>

            {{-- Smooth soft gradient overlay on the left to ensure crisp text contrast --}}
            <div class="pointer-events-none absolute inset-y-0 left-0 w-2/3 md:w-1/2 bg-gradient-to-r from-[#EDE9FE] via-[#EDE9FE]/95 to-transparent dark:from-[#0F172A] dark:via-[#0F172A]/95 dark:to-transparent"></div>
        </div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            {{-- Left column --}}
            <div class="flex-1 min-w-0 space-y-4">
                <div class="inline-flex items-center">
                    <span class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">
                        @if($me->hasRole('Super Admin'))
                            SUPER ADMIN DASHBOARD
                        @elseif($me->hasRole('Admin'))
                            ADMIN DASHBOARD
                        @else
                            {{ strtoupper($me->roles->first()?->name ?? 'PLATFORM') }} DASHBOARD
                        @endif
                    </span>
                </div>

                <div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-900 sm:text-4xl lg:text-[40px] dark:text-white leading-tight">
                        Welcome back, {{ $me->name }} 👋
                    </h1>
                    <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300">
                        Here's what's happening on your platform today.
                    </p>
                </div>

                {{-- Action buttons row (no scrollbar) --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @can('posts.create')
                        <a href="{{ route('admin.posts.create') }}" wire:navigate
                            class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200/90 bg-white/95 px-3 text-xs font-semibold text-slate-700 shadow-2xs backdrop-blur-md transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700/80 dark:bg-slate-800/90 dark:text-white dark:hover:border-indigo-500 dark:hover:bg-slate-700 dark:hover:text-white">
                            <i data-lucide="edit-3" class="h-3.5 w-3.5 text-indigo-500 dark:text-indigo-400"></i>
                            <span>New Post</span>
                        </a>
                    @endcan

                    @canany(['posts.view', 'posts.view_any'])
                        <a href="{{ route('admin.posts.index') }}" wire:navigate
                            class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200/90 bg-white/95 px-3 text-xs font-semibold text-slate-700 shadow-2xs backdrop-blur-md transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700/80 dark:bg-slate-800/90 dark:text-white dark:hover:border-indigo-500 dark:hover:bg-slate-700 dark:hover:text-white">
                            <i data-lucide="list" class="h-3.5 w-3.5 text-indigo-500 dark:text-indigo-400"></i>
                            <span>Manage Posts</span>
                        </a>
                    @endcanany

                    @can('editorial.review_queue')
                        <a href="{{ route('admin.editorial.queue') }}" wire:navigate
                            class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200/90 bg-white/95 px-3 text-xs font-semibold text-slate-700 shadow-2xs backdrop-blur-md transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700/80 dark:bg-slate-800/90 dark:text-white dark:hover:border-indigo-500 dark:hover:bg-slate-700 dark:hover:text-white">
                            <i data-lucide="check-square" class="h-3.5 w-3.5 text-indigo-500 dark:text-indigo-400"></i>
                            <span>Review Queue</span>
                        </a>
                    @endcan

                    @can('categories.view')
                        <a href="{{ route('admin.categories.index') }}" wire:navigate
                            class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200/90 bg-white/95 px-3 text-xs font-semibold text-slate-700 shadow-2xs backdrop-blur-md transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700/80 dark:bg-slate-800/90 dark:text-white dark:hover:border-indigo-500 dark:hover:bg-slate-700 dark:hover:text-white">
                            <i data-lucide="folder" class="h-3.5 w-3.5 text-blue-500 dark:text-blue-400"></i>
                            <span>Manage Categories</span>
                        </a>
                    @endcan

                    @can('media.view')
                        <a href="{{ route('admin.media.index') }}" wire:navigate
                            class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200/90 bg-white/95 px-3 text-xs font-semibold text-slate-700 shadow-2xs backdrop-blur-md transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700/80 dark:bg-slate-800/90 dark:text-white dark:hover:border-indigo-500 dark:hover:bg-slate-700 dark:hover:text-white">
                            <i data-lucide="image" class="h-3.5 w-3.5 text-blue-500 dark:text-blue-400"></i>
                            <span>Media Library</span>
                        </a>
                    @endcan

                    @can('staff.view')
                        <a href="{{ route('admin.staff.index') }}" wire:navigate
                            class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200/90 bg-white/95 px-3 text-xs font-semibold text-slate-700 shadow-2xs backdrop-blur-md transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700/80 dark:bg-slate-800/90 dark:text-white dark:hover:border-indigo-500 dark:hover:bg-slate-700 dark:hover:text-white">
                            <i data-lucide="users" class="h-3.5 w-3.5 text-blue-500 dark:text-blue-400"></i>
                            <span>Manage Users</span>
                        </a>
                    @endcan

                    {{-- More actions dropdown --}}
                    <div x-data="{ open: false, timeout: null }"
                        @mouseenter="clearTimeout(timeout); open = true"
                        @mouseleave="timeout = setTimeout(() => open = false, 250)"
                        @click.outside="open = false"
                        class="relative shrink-0">
                        <button @click="open = !open" type="button"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200/90 bg-white/95 text-slate-600 shadow-2xs transition hover:border-indigo-300 hover:text-indigo-600 dark:border-slate-700/80 dark:bg-slate-800/90 dark:text-slate-200 dark:hover:border-indigo-500 dark:hover:bg-slate-700 dark:hover:text-white cursor-pointer"
                            :aria-expanded="open.toString()"
                            title="More actions">
                            <i data-lucide="more-vertical" class="h-4 w-4"></i>
                        </button>
                        <div x-show="open" x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                            class="absolute left-0 z-50 mt-2 w-52 rounded-xl border border-slate-200 bg-white py-1.5 shadow-2xl sm:right-0 sm:left-auto dark:border-slate-800 dark:bg-slate-900">
                            @can('tags.view')
                                <a href="{{ route('admin.tags.index') }}" wire:navigate @click="open = false"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/60">
                                    <i data-lucide="tag" class="h-3.5 w-3.5 text-slate-400"></i> Manage Tags
                                </a>
                            @endcan
                            @can('comments.view')
                                <a href="{{ route('admin.comments.index') }}" wire:navigate @click="open = false"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/60">
                                    <i data-lucide="message-square" class="h-3.5 w-3.5 text-slate-400"></i> Moderate Comments
                                </a>
                            @endcan
                            @can('settings.view')
                                <a href="{{ route('admin.settings.index') }}" wire:navigate @click="open = false"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/60">
                                    <i data-lucide="settings" class="h-3.5 w-3.5 text-slate-400"></i> Site Settings
                                </a>
                            @endcan
                            <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                            <button type="button"
                                @click="
                                    window.toggleAdminTheme ? window.toggleAdminTheme() : (() => {
                                        const root = document.documentElement;
                                        const isDark = root.classList.toggle('dark');
                                        const theme = isDark ? 'dark' : 'light';
                                        localStorage.setItem('crm-theme', theme);
                                        document.cookie = 'crm_theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
                                        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme, isDark } }));
                                    })();
                                    open = false;
                                "
                                class="flex w-full items-center gap-2.5 px-3.5 py-2 text-left text-xs font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800/60 cursor-pointer">
                                <i data-lucide="moon" class="h-3.5 w-3.5 text-slate-400 dark:hidden"></i>
                                <i data-lucide="sun" class="hidden h-3.5 w-3.5 text-slate-400 dark:block"></i>
                                <span class="dark:hidden">Switch to Dark Mode</span>
                                <span class="hidden dark:inline">Switch to Light Mode</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: AI Insights frosted card matching the mockup --}}
            <div class="relative shrink-0">
                <div class="relative flex items-center gap-4 rounded-2xl border border-white/90 bg-white/75 px-5 py-4 shadow-xl backdrop-blur-xl dark:border-white/15 dark:bg-slate-900/80">
                    <div class="text-left">
                        <h3 class="text-sm font-black leading-tight text-slate-900 dark:text-white">
                            Smarter<br>Content<br>Stronger Insights
                        </h3>
                    </div>
                    @can('editorial.review_queue')
                        <a href="{{ route('admin.editorial.queue') }}" wire:navigate
                            class="ml-2 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-600 text-white shadow-md shadow-blue-500/30 transition hover:bg-blue-700 hover:scale-105 active:scale-95">
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </a>
                    @else
                        <a href="{{ route('admin.posts.index') }}" wire:navigate
                            class="ml-2 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-blue-600 text-white shadow-md shadow-blue-500/30 transition hover:bg-blue-700 hover:scale-105 active:scale-95">
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         2. ACTIONABLE ALERTS (4 Cards Grid)
    ================================================================ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Card 1: Posts awaiting approval (Red/Rose) --}}
        @can('editorial.review_queue')
            <a href="{{ route('admin.editorial.queue') }}" wire:navigate
                class="group relative flex flex-col justify-between rounded-2xl border border-rose-200/70 bg-gradient-to-b from-rose-50/50 via-white to-white p-5 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-rose-900/30 dark:from-rose-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-rose-500 text-white shadow-xs">
                            <i data-lucide="hourglass" class="h-5 w-5"></i>
                        </span>
                        <span class="grid h-7 w-7 place-items-center rounded-full text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-rose-600 dark:group-hover:text-rose-400">
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['pending_review'] }} posts awaiting approval
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Please review these posts before they can be published.
                    </p>
                </div>
            </a>
        @else
            <div class="group relative flex flex-col justify-between rounded-2xl border border-rose-200/70 bg-gradient-to-b from-rose-50/50 via-white to-white p-5 shadow-xs dark:border-rose-900/30 dark:from-rose-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-rose-500 text-white shadow-xs">
                            <i data-lucide="hourglass" class="h-5 w-5"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['pending_review'] }} posts awaiting approval
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Posts currently queued for editorial review.
                    </p>
                </div>
            </div>
        @endcan

        {{-- Card 2: Scheduled posts missed (Orange/Amber) --}}
        @canany(['posts.view', 'posts.view_any'])
            <a href="{{ route('admin.posts.index', ['status' => 'scheduled']) }}" wire:navigate
                class="group relative flex flex-col justify-between rounded-2xl border border-amber-200/70 bg-gradient-to-b from-amber-50/50 via-white to-white p-5 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-amber-900/30 dark:from-amber-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-500 text-white shadow-xs">
                            <i data-lucide="clock" class="h-5 w-5"></i>
                        </span>
                        <span class="grid h-7 w-7 place-items-center rounded-full text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-amber-600 dark:group-hover:text-amber-400">
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['missed_scheduled'] }} scheduled posts missed
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        These posts were scheduled to publish but were missed.
                    </p>
                </div>
            </a>
        @else
            <div class="group relative flex flex-col justify-between rounded-2xl border border-amber-200/70 bg-gradient-to-b from-amber-50/50 via-white to-white p-5 shadow-xs dark:border-amber-900/30 dark:from-amber-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-amber-500 text-white shadow-xs">
                            <i data-lucide="clock" class="h-5 w-5"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['missed_scheduled'] }} scheduled posts missed
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        These posts were scheduled to publish but were missed.
                    </p>
                </div>
            </div>
        @endcanany

        {{-- Card 3: Comments pending moderation (Purple) --}}
        @can('comments.view')
            <a href="{{ route('admin.comments.index') }}" wire:navigate
                class="group relative flex flex-col justify-between rounded-2xl border border-purple-200/70 bg-gradient-to-b from-purple-50/50 via-white to-white p-5 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-purple-900/30 dark:from-purple-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-purple-600 text-white shadow-xs">
                            <i data-lucide="message-square" class="h-5 w-5"></i>
                        </span>
                        <span class="grid h-7 w-7 place-items-center rounded-full text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['pending_comments'] }} comments pending moderation
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Review and approve these comments before they appear publicly.
                    </p>
                </div>
            </a>
        @else
            <div class="group relative flex flex-col justify-between rounded-2xl border border-purple-200/70 bg-gradient-to-b from-purple-50/50 via-white to-white p-5 shadow-xs dark:border-purple-900/30 dark:from-purple-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-purple-600 text-white shadow-xs">
                            <i data-lucide="message-square" class="h-5 w-5"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['pending_comments'] }} comments pending moderation
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Comments awaiting moderation by administrators.
                    </p>
                </div>
            </div>
        @endcan

        {{-- Card 4: Posts missing featured images (Blue) --}}
        @canany(['posts.view', 'posts.view_any'])
            <a href="{{ route('admin.posts.index') }}" wire:navigate
                class="group relative flex flex-col justify-between rounded-2xl border border-blue-200/70 bg-gradient-to-b from-blue-50/50 via-white to-white p-5 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-blue-900/30 dark:from-blue-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-xs">
                            <i data-lucide="image" class="h-5 w-5"></i>
                        </span>
                        <span class="grid h-7 w-7 place-items-center rounded-full text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['no_images'] }} posts missing featured images
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Add featured images to improve visual appeal and engagement.
                    </p>
                </div>
            </a>
        @else
            <div class="group relative flex flex-col justify-between rounded-2xl border border-blue-200/70 bg-gradient-to-b from-blue-50/50 via-white to-white p-5 shadow-xs dark:border-blue-900/30 dark:from-blue-950/20 dark:via-slate-900 dark:to-slate-900">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-600 text-white shadow-xs">
                            <i data-lucide="image" class="h-5 w-5"></i>
                        </span>
                    </div>
                    <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['no_images'] }} posts missing featured images
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Posts without featured cover images.
                    </p>
                </div>
            </div>
        @endcanany
    </div>

    {{-- ================================================================
         3. PLATFORM OVERVIEW (6 Metric Cards with Sparklines)
    ================================================================ --}}
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
                Platform Overview
            </h2>
            <div class="inline-flex items-center gap-2 rounded-xl border border-slate-200/90 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                <i data-lucide="calendar" class="h-3.5 w-3.5 text-slate-400"></i>
                <span>{{ now()->startOfMonth()->format('M d, Y') }} - {{ now()->endOfMonth()->format('M d, Y') }}</span>
                <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-slate-400"></i>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
            {{-- Metric 1: Posts --}}
            <a href="{{ route('admin.posts.index') }}" wire:navigate class="group block rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-blue-500/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-500/50">
                <div class="flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-lg bg-blue-50 text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white dark:bg-blue-500/20 dark:text-blue-400 dark:group-hover:bg-blue-500 dark:group-hover:text-white">
                        <i data-lucide="file-text" class="h-3.5 w-3.5"></i>
                    </span>
                    <span class="text-xs font-semibold text-slate-600 transition-colors group-hover:text-blue-600 dark:text-slate-400 dark:group-hover:text-blue-400">Posts</span>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ number_format($stats['total_posts']) }}
                    </span>
                </div>
                <div class="mt-2.5 flex items-center justify-between">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        ↑ +12%
                    </span>
                    <svg class="h-6 w-16 text-blue-500" viewBox="0 0 60 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 15 Q 15 16, 25 10 T 45 8 T 58 4" stroke-linecap="round"/>
                    </svg>
                </div>
            </a>

            {{-- Metric 2: Users --}}
            <a href="{{ route('admin.staff.index') }}" wire:navigate class="group block rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-orange-500/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-orange-500/50">
                <div class="flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-lg bg-orange-50 text-orange-600 transition-colors group-hover:bg-orange-600 group-hover:text-white dark:bg-orange-500/20 dark:text-orange-400 dark:group-hover:bg-orange-500 dark:group-hover:text-white">
                        <i data-lucide="users" class="h-3.5 w-3.5"></i>
                    </span>
                    <span class="text-xs font-semibold text-slate-600 transition-colors group-hover:text-orange-600 dark:text-slate-400 dark:group-hover:text-orange-400">Users</span>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ number_format($stats['total_users']) }}
                    </span>
                </div>
                <div class="mt-2.5 flex items-center justify-between">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        ↑ +5%
                    </span>
                    <svg class="h-6 w-16 text-cyan-500" viewBox="0 0 60 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 17 Q 18 16, 30 11 T 48 9 T 58 5" stroke-linecap="round"/>
                    </svg>
                </div>
            </a>

            {{-- Metric 3: Categories --}}
            <a href="{{ route('admin.categories.index') }}" wire:navigate class="group block rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-amber-500/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-amber-500/50">
                <div class="flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-lg bg-amber-50 text-amber-600 transition-colors group-hover:bg-amber-600 group-hover:text-white dark:bg-amber-500/20 dark:text-amber-400 dark:group-hover:bg-amber-500 dark:group-hover:text-white">
                        <i data-lucide="folder" class="h-3.5 w-3.5"></i>
                    </span>
                    <span class="text-xs font-semibold text-slate-600 transition-colors group-hover:text-amber-600 dark:text-slate-400 dark:group-hover:text-amber-400">Categories</span>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ number_format($stats['total_categories']) }}
                    </span>
                </div>
                <div class="mt-2.5 flex items-center justify-between">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-slate-500 dark:text-slate-400">
                        ↑ 0%
                    </span>
                    <svg class="h-6 w-16 text-blue-400" viewBox="0 0 60 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 12 Q 15 13, 30 11 T 45 10 T 58 6" stroke-linecap="round"/>
                    </svg>
                </div>
            </a>

            {{-- Metric 4: Tags --}}
            <a href="{{ route('admin.tags.index') }}" wire:navigate class="group block rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-purple-500/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-purple-500/50">
                <div class="flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-lg bg-purple-50 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white dark:bg-purple-500/20 dark:text-purple-400 dark:group-hover:bg-purple-500 dark:group-hover:text-white">
                        <i data-lucide="tag" class="h-3.5 w-3.5"></i>
                    </span>
                    <span class="text-xs font-semibold text-slate-600 transition-colors group-hover:text-purple-600 dark:text-slate-400 dark:group-hover:text-purple-400">Tags</span>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ number_format($stats['total_tags']) }}
                    </span>
                </div>
                <div class="mt-2.5 flex items-center justify-between">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        ↑ +14%
                    </span>
                    <svg class="h-6 w-16 text-indigo-500" viewBox="0 0 60 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 16 Q 16 15, 30 9 T 46 8 T 58 4" stroke-linecap="round"/>
                    </svg>
                </div>
            </a>

            {{-- Metric 5: Comments --}}
            <a href="{{ route('admin.comments.index') }}" wire:navigate class="group block rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-violet-500/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-violet-500/50">
                <div class="flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-lg bg-violet-50 text-violet-600 transition-colors group-hover:bg-violet-600 group-hover:text-white dark:bg-violet-500/20 dark:text-violet-400 dark:group-hover:bg-violet-500 dark:group-hover:text-white">
                        <i data-lucide="message-circle" class="h-3.5 w-3.5"></i>
                    </span>
                    <span class="text-xs font-semibold text-slate-600 transition-colors group-hover:text-violet-600 dark:text-slate-400 dark:group-hover:text-violet-400">Comments</span>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ number_format($stats['total_comments']) }}
                    </span>
                </div>
                <div class="mt-2.5 flex items-center justify-between">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-rose-600 dark:text-rose-400">
                        ↓ -8%
                    </span>
                    <svg class="h-6 w-16 text-purple-500" viewBox="0 0 60 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 8 Q 15 6, 30 11 T 46 14 T 58 16" stroke-linecap="round"/>
                    </svg>
                </div>
            </a>

            {{-- Metric 6: Total Views --}}
            <a href="{{ route('admin.dashboards.content') }}" wire:navigate class="group block rounded-2xl border border-slate-200/80 bg-white p-4.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/50 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-500/50">
                <div class="flex items-center gap-2">
                    <span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white dark:bg-emerald-500/20 dark:text-emerald-400 dark:group-hover:bg-emerald-500 dark:group-hover:text-white">
                        <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                    </span>
                    <span class="text-xs font-semibold text-slate-600 transition-colors group-hover:text-emerald-600 dark:text-slate-400 dark:group-hover:text-emerald-400">Total Views</span>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl dark:text-white">
                        {{ number_format($stats['total_views']) }}
                    </span>
                </div>
                <div class="mt-2.5 flex items-center justify-between">
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        ↑ +18%
                    </span>
                    <svg class="h-6 w-16 text-emerald-500" viewBox="0 0 60 20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 18 Q 18 16, 28 10 T 45 7 T 58 3" stroke-linecap="round"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>

    {{-- ================================================================
         4. CONTENT PIPELINE (8 cols) & RECENT ACTIVITY (4 cols)
    ================================================================ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Left: Content Pipeline (8 cols) --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs lg:col-span-8 dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    Content Pipeline
                </h3>
                <a href="{{ route('admin.posts.index') }}" wire:navigate
                    class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                    <span>View All</span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                {{-- Column 1: Drafts --}}
                <div class="flex flex-col justify-between rounded-xl border border-slate-100 bg-slate-50/60 p-3.5 dark:border-slate-800/80 dark:bg-slate-800/40">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    <i data-lucide="file-text" class="h-3 w-3"></i>
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Drafts</h4>
                            </div>
                            <span class="rounded-md bg-slate-200/70 px-1.5 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                {{ $stats['drafts'] }}
                            </span>
                        </div>
                        <ul class="space-y-2.5">
                            @forelse ($this->draftPosts->take(3) as $post)
                                <li>
                                    <a href="{{ route('admin.posts.edit', $post) }}" wire:navigate
                                        class="block truncate text-xs font-semibold text-slate-800 hover:text-indigo-600 dark:text-slate-200 dark:hover:text-indigo-400"
                                        title="{{ $post->translation()?->title ?? '#' . $post->id }}">
                                        {{ $post->translation()?->title ?? '#' . $post->id }}
                                    </a>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                        {{ $post->updated_at?->format('M d, Y') }}
                                    </p>
                                </li>
                            @empty
                                <li class="py-4 text-center text-xs text-slate-400">No drafts</li>
                            @endforelse
                        </ul>
                    </div>
                    @if ($stats['drafts'] > 3)
                        <div class="mt-4 pt-2 border-t border-slate-200/50 dark:border-slate-800">
                            <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" wire:navigate
                                class="text-[11px] font-semibold text-indigo-600 hover:underline dark:text-indigo-400">
                                + {{ $stats['drafts'] - 3 }} more drafts
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Column 2: Pending Review --}}
                <div class="flex flex-col justify-between rounded-xl border border-amber-100/80 bg-amber-50/40 p-3.5 dark:border-amber-900/30 dark:bg-amber-950/15">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400">
                                    <i data-lucide="clock" class="h-3 w-3"></i>
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Pending Review</h4>
                            </div>
                            <span class="rounded-md bg-amber-200/70 px-1.5 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                {{ $stats['pending_review'] }}
                            </span>
                        </div>
                        <ul class="space-y-2.5">
                            @forelse ($this->pendingReviewPosts->take(3) as $post)
                                <li>
                                    <a href="{{ route('admin.posts.edit', $post) }}" wire:navigate
                                        class="block truncate text-xs font-semibold text-slate-800 hover:text-amber-600 dark:text-slate-200 dark:hover:text-amber-400"
                                        title="{{ $post->translation()?->title ?? '#' . $post->id }}">
                                        {{ $post->translation()?->title ?? '#' . $post->id }}
                                    </a>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                        {{ $post->updated_at?->format('M d, Y') }}
                                    </p>
                                </li>
                            @empty
                                <li class="py-4 text-center text-xs text-slate-400">All clear!</li>
                            @endforelse
                        </ul>
                    </div>
                    @if ($stats['pending_review'] > 3)
                        <div class="mt-4 pt-2 border-t border-amber-200/50 dark:border-amber-900/30">
                            <a href="{{ route('admin.editorial.queue') }}" wire:navigate
                                class="text-[11px] font-semibold text-amber-700 hover:underline dark:text-amber-400">
                                + {{ $stats['pending_review'] - 3 }} more posts
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Column 3: Scheduled --}}
                <div class="flex flex-col justify-between rounded-xl border border-purple-100/80 bg-purple-50/40 p-3.5 dark:border-purple-900/30 dark:bg-purple-950/15">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400">
                                    <i data-lucide="calendar" class="h-3 w-3"></i>
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Scheduled</h4>
                            </div>
                            <span class="rounded-md bg-purple-200/70 px-1.5 py-0.5 text-[10px] font-bold text-purple-700 dark:bg-purple-500/20 dark:text-purple-300">
                                {{ $stats['scheduled'] }}
                            </span>
                        </div>
                        <ul class="space-y-2.5">
                            @forelse ($this->scheduledPosts->take(3) as $post)
                                <li>
                                    <a href="{{ route('admin.posts.edit', $post) }}" wire:navigate
                                        class="block truncate text-xs font-semibold text-slate-800 hover:text-purple-600 dark:text-slate-200 dark:hover:text-purple-400"
                                        title="{{ $post->translation()?->title ?? '#' . $post->id }}">
                                        {{ $post->translation()?->title ?? '#' . $post->id }}
                                    </a>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">
                                        {{ $post->scheduled_at?->format('M d, Y') }}
                                    </p>
                                </li>
                            @empty
                                <li class="py-4 text-center text-xs text-slate-400">No scheduled posts</li>
                            @endforelse
                        </ul>
                    </div>
                    @if ($stats['scheduled'] > 3)
                        <div class="mt-4 pt-2 border-t border-purple-200/50 dark:border-purple-900/30">
                            <a href="{{ route('admin.posts.index', ['status' => 'scheduled']) }}" wire:navigate
                                class="text-[11px] font-semibold text-purple-700 hover:underline dark:text-purple-400">
                                + {{ $stats['scheduled'] - 3 }} more scheduled
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Column 4: Ready to Publish --}}
                <div class="flex flex-col justify-between rounded-xl border border-emerald-100/80 bg-emerald-50/40 p-3.5 dark:border-emerald-900/30 dark:bg-emerald-950/15">
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                                    <i data-lucide="check" class="h-3 w-3"></i>
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Ready to Publish</h4>
                            </div>
                            <span class="rounded-md bg-emerald-200/70 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                {{ $stats['ready_to_publish'] }}
                            </span>
                        </div>
                        <ul class="space-y-2.5">
                            @forelse ($this->readyToPublishPosts->take(3) as $post)
                                <li>
                                    <a href="{{ route('admin.posts.edit', $post) }}" wire:navigate
                                        class="block truncate text-xs font-semibold text-slate-800 hover:text-emerald-600 dark:text-slate-200 dark:hover:text-emerald-400"
                                        title="{{ $post->translation()?->title ?? '#' . $post->id }}">
                                        {{ $post->translation()?->title ?? '#' . $post->id }}
                                    </a>
                                    <span class="inline-block text-[10px] font-bold text-emerald-600 dark:text-emerald-400">Ready</span>
                                </li>
                            @empty
                                <li class="py-4 text-center text-xs text-slate-400">No posts ready</li>
                            @endforelse
                        </ul>
                    </div>
                    @if ($stats['ready_to_publish'] > 3)
                        <div class="mt-4 pt-2 border-t border-emerald-200/50 dark:border-emerald-900/30">
                            <a href="{{ route('admin.editorial.queue') }}" wire:navigate
                                class="text-[11px] font-semibold text-emerald-700 hover:underline dark:text-emerald-400">
                                + {{ $stats['ready_to_publish'] - 3 }} more posts
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Recent Activity (4 cols) --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs lg:col-span-4 dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        Recent Activity
                    </h3>
                    <a href="{{ route('admin.logs.activity.index') }}" wire:navigate
                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                        <span>View All</span>
                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                    </a>
                </div>

                <ul class="space-y-3.5">
                    @forelse ($this->activityFeed as $activity)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full {{ $activity->color }}">
                                <i data-lucide="{{ $activity->icon }}" class="h-4 w-4"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ $activity->url }}" wire:navigate
                                    class="block truncate text-xs font-medium text-slate-800 hover:text-indigo-600 dark:text-slate-200 dark:hover:text-indigo-400">
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $activity->user }}</span>
                                    <span>{{ $activity->action }}</span>
                                </a>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                    {{ $activity->timestamp?->diffForHumans() }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center text-xs text-slate-400">No recent activity</li>
                    @endforelse
                </ul>
            </div>

            @if ($this->activityFeed->hasPages())
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-800">
                    <button wire:click="previousPage('activityPage')"
                        @disabled($this->activityFeed->onFirstPage())
                        class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-600 disabled:opacity-40 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <i data-lucide="chevron-left" class="h-3.5 w-3.5"></i>
                        <span>Previous</span>
                    </button>
                    <span class="text-xs text-slate-400">
                        Page {{ $this->activityFeed->currentPage() }} of {{ $this->activityFeed->lastPage() }}
                    </span>
                    <button wire:click="nextPage('activityPage')"
                        @disabled(!$this->activityFeed->hasMorePages())
                        class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-600 disabled:opacity-40 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <span>Next</span>
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ================================================================
         5. LOWER ROW: PUBLISHING TREND, TOP CATEGORIES, RECENTLY PUBLISHED
    ================================================================ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Col 1: Publishing Trend (Bar Chart) --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        Publishing Trend
                    </h3>
                    <div class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-700 shadow-2xs dark:border-slate-800 dark:bg-slate-800 dark:text-slate-300">
                        <span>Last 30 Days</span>
                        <i data-lucide="chevron-down" class="h-3 w-3 text-slate-400"></i>
                    </div>
                </div>
                <div class="relative mt-2 h-56 w-full">
                    <canvas id="publishing-chart"></canvas>
                </div>
            </div>
        </div>

        {{-- Col 2: Top Categories --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        Top Categories
                    </h3>
                    <a href="{{ route('admin.categories.index') }}" wire:navigate
                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                        <span>View All</span>
                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                    </a>
                </div>

                @php($topCats = $this->topCategories)
                @php($maxCat = $topCats->max('post_count') ?: 1)
                <ul class="space-y-3.5">
                    @forelse ($topCats as $cat)
                        <li class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $cat->category_name }}</span>
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $cat->post_count }}
                                </span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-500"
                                    style="width: {{ max(6, (int) (($cat->post_count / $maxCat) * 100)) }}%"></div>
                            </div>
                        </li>
                    @empty
                        <li class="py-8 text-center text-xs text-slate-400">No categories found</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Col 3: Recently Published --}}
        <div class="flex flex-col justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        Recently Published
                    </h3>
                    <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" wire:navigate
                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                        <span>View All</span>
                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                    </a>
                </div>

                <ul class="space-y-3">
                    @forelse ($this->recentlyPublished as $post)
                        <li class="flex items-center gap-3">
                            <div class="relative h-11 w-11 shrink-0 overflow-hidden rounded-xl border border-slate-100 bg-slate-100 dark:border-slate-800 dark:bg-slate-800">
                                @if ($post->featuredImage?->url())
                                    <img src="{{ $post->featuredImage->url() }}" alt="{{ $post->translation()?->title }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <div class="grid h-full w-full place-items-center bg-gradient-to-br from-indigo-500 to-blue-600 text-white">
                                        <i data-lucide="file-text" class="h-5 w-5"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.posts.edit', $post) }}" wire:navigate
                                    class="block truncate text-xs font-semibold text-slate-800 hover:text-indigo-600 dark:text-slate-100 dark:hover:text-indigo-400"
                                    title="{{ $post->translation()?->title ?? '#' . $post->id }}">
                                    {{ $post->translation()?->title ?? '#' . $post->id }}
                                </a>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                    {{ $post->published_at?->format('M d, Y') ?? $post->created_at?->format('M d, Y') }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="py-8 text-center text-xs text-slate-400">No published posts yet</li>
                    @endforelse
                </ul>
            </div>

            @if ($this->recentlyPublished->hasPages())
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 dark:border-slate-800">
                    <button wire:click="previousPage('publishedPage')"
                        @disabled($this->recentlyPublished->onFirstPage())
                        class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-600 disabled:opacity-40 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <i data-lucide="chevron-left" class="h-3.5 w-3.5"></i>
                        <span>Previous</span>
                    </button>
                    <span class="text-xs text-slate-400">
                        Page {{ $this->recentlyPublished->currentPage() }} of {{ $this->recentlyPublished->lastPage() }}
                    </span>
                    <button wire:click="nextPage('publishedPage')"
                        @disabled(!$this->recentlyPublished->hasMorePages())
                        class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-600 disabled:opacity-40 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <span>Next</span>
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ================================================================
         6. SCHEDULED PUBLISHING COLLAPSIBLE WIDGET
    ================================================================ --}}
    @php($schedule = $this->scheduledWidget)
    @if ($schedule['upcoming']->isNotEmpty() || $schedule['today'] > 0 || $schedule['missed']->isNotEmpty())
        <div x-data="{ open: localStorage.getItem('scheduled_widget_open') !== 'false' }"
            class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs transition-all duration-200 dark:border-slate-800 dark:bg-slate-900">
            <div class="flex cursor-pointer select-none items-center justify-between"
                :class="{ 'mb-4': open, 'mb-0': !open }"
                @click="open = !open; localStorage.setItem('scheduled_widget_open', open)">
                <div class="flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-xs">
                        <i data-lucide="clock" class="h-4 w-4"></i>
                    </span>
                    <div>
                        <h3 class="flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-white">
                            <span>Scheduled Publishing</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $schedule['today'] }} post(s) scheduled for today</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ($schedule['missed']->isNotEmpty())
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-[10px] font-bold text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">
                            ⚠️ {{ $schedule['missed']->count() }} missed
                        </span>
                    @endif
                    <button type="button"
                        class="grid h-8 w-8 place-items-center rounded-lg border border-slate-200 bg-slate-50 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 dark:border-slate-700/60 dark:bg-slate-800/80 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-200"
                        :title="open ? 'Collapse' : 'Expand'">
                        <svg class="h-4 w-4 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="open" x-collapse>
                @if ($schedule['next'])
                    <div class="mb-4 rounded-xl bg-gradient-to-r from-violet-50 to-purple-50 p-4 dark:from-violet-950/20 dark:to-purple-950/20">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-violet-600 dark:text-violet-400">Next publication</p>
                        <div class="mt-1 flex items-center justify-between">
                            <span class="font-bold text-slate-800 dark:text-slate-100">
                                {{ $schedule['next']->translation()?->title ?? '#' . $schedule['next']->id }}
                            </span>
                            <span class="rounded-full bg-violet-200 px-3 py-1 text-xs font-bold text-violet-700 dark:bg-violet-500/30 dark:text-violet-300">
                                {{ Carbon\Carbon::parse($schedule['next']->scheduled_at)->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $schedule['next']->author?->name }} ·
                            {{ Carbon\Carbon::parse($schedule['next']->scheduled_at)->format('M j, Y H:i') }}</p>
                    </div>
                @endif

                @if ($schedule['upcoming']->isNotEmpty())
                    <div>
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Upcoming</p>
                        <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($schedule['upcoming'] as $post)
                                <li class="flex items-center justify-between py-2">
                                    <div>
                                        <a href="{{ route('admin.posts.edit', $post) }}" wire:navigate
                                            class="text-xs font-semibold text-slate-700 hover:text-violet-600 dark:text-slate-200 dark:hover:text-violet-400">
                                            {{ $post->translation()?->title ?? '#' . $post->id }}
                                        </a>
                                        <p class="text-[9px] text-slate-400">{{ $post->author?->name }}</p>
                                    </div>
                                    <span class="font-mono text-xs text-violet-600 dark:text-violet-400">
                                        {{ Carbon\Carbon::parse($post->scheduled_at)->format('M j, H:i') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ================================================================
         7. TEAM PERFORMANCE TABLE
    ================================================================ --}}
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                Team Performance
            </h3>
            <div class="inline-flex items-center gap-1.5 shrink-0 text-xs">
                <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Show:</span>
                <div class="inline-flex rounded-lg border border-slate-200 bg-white p-0.5 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
                    <button type="button"
                        wire:click="$set('authorPerPage', 5)"
                        class="rounded-md px-2.5 py-1 text-xs font-bold transition {{ $authorPerPage === 5 ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                        5
                    </button>
                    <button type="button"
                        wire:click="$set('authorPerPage', 10)"
                        class="rounded-md px-2.5 py-1 text-xs font-bold transition {{ $authorPerPage === 10 ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                        10
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/60 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/50">
                            <th class="px-5 py-3">Author</th>
                            <th class="px-5 py-3 text-center">Published</th>
                            <th class="px-5 py-3 text-center">Total Views</th>
                            <th class="px-5 py-3 text-center">Avg Views</th>
                            <th class="px-5 py-3">First Published</th>
                            <th class="px-5 py-3">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->authorPerformance as $author)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-gradient-to-br from-sky-500 to-indigo-500 text-[10px] font-bold uppercase text-white">
                                            {{ mb_substr($author->name, 0, 1) }}
                                        </span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $author->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                                        {{ $author->published_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center font-mono text-sm text-slate-600 dark:text-slate-400">
                                    {{ number_format($author->total_views) }}
                                </td>
                                <td class="px-5 py-3 text-center font-mono text-sm text-slate-600 dark:text-slate-400">
                                    {{ $author->published_count > 0 ? number_format(ceil($author->total_views / $author->published_count)) : 0 }}
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ $author->first_published_at ? Carbon\Carbon::parse($author->first_published_at)->format('M j, Y') : '—' }}
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">
                                    {{ $author->last_published_at ? Carbon\Carbon::parse($author->last_published_at)->diffForHumans() : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-xs text-slate-500">No author data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->authorPerformance->hasPages())
                <div class="flex items-center justify-between border-t border-slate-100 px-5 py-3 dark:border-slate-800">
                    <button wire:click="previousPage('teamPage')"
                        @disabled($this->authorPerformance->onFirstPage())
                        class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-600 disabled:opacity-40 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <i data-lucide="chevron-left" class="h-3.5 w-3.5"></i>
                        <span>Previous</span>
                    </button>
                    <span class="text-xs text-slate-400">
                        Page {{ $this->authorPerformance->currentPage() }} of {{ $this->authorPerformance->lastPage() }}
                    </span>
                    <button wire:click="nextPage('teamPage')"
                        @disabled(!$this->authorPerformance->hasMorePages())
                        class="inline-flex h-8 items-center gap-1 rounded-lg border border-slate-200 px-2.5 text-xs font-medium text-slate-600 disabled:opacity-40 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <span>Next</span>
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@script
    <script>
        const renderPublishingChart = () => {
            const el = document.getElementById('publishing-chart');
            if (!el || typeof Chart === 'undefined') return;
            if (window._publishingChart) {
                window._publishingChart.destroy();
                window._publishingChart = null;
            }

            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.7)';
            const textColor = isDark ? '#94a3b8' : '#64748b';

            const rawLabels = @json($this->publishingChart['labels']);
            const published = @json($this->publishingChart['published']);
            const scheduled = @json($this->publishingChart['scheduled']);

            window._publishingChart = new Chart(el, {
                type: 'bar',
                data: {
                    labels: rawLabels,
                    datasets: [
                        {
                            label: 'Published',
                            data: published,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                            barPercentage: 0.65,
                            categoryPercentage: 0.8,
                        },
                        {
                            label: 'Scheduled',
                            data: scheduled,
                            backgroundColor: '#8b5cf6',
                            borderRadius: 4,
                            barPercentage: 0.65,
                            categoryPercentage: 0.8,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#0f172a' : '#ffffff',
                            titleColor: isDark ? '#ffffff' : '#0f172a',
                            bodyColor: isDark ? '#cbd5e1' : '#334155',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            borderWidth: 1,
                            padding: 8,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: textColor,
                                maxTicksLimit: 7,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor,
                                borderDash: [4, 4],
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        };

        const reinitLucide = () => {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        };

        renderPublishingChart();
        reinitLucide();

        Livewire.hook('morph.updated', () => {
            renderPublishingChart();
            reinitLucide();
        });

        // Re-render chart if theme changes
        window.addEventListener('theme-changed', () => {
            renderPublishingChart();
            reinitLucide();
        });
    </script>
@endscript
