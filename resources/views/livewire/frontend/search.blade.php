<div class="relative overflow-hidden">
    {{-- ───── Ambient Background Mesh / Radial Glows ───── --}}
    <div class="pointer-events-none absolute -top-24 right-0 h-[650px] w-[650px] rounded-full bg-gradient-to-br from-blue-400/15 via-indigo-500/15 to-purple-400/10 blur-[130px] dark:from-blue-600/15 dark:via-indigo-600/15 dark:to-purple-600/10 -z-10"></div>
    <div class="pointer-events-none absolute top-[600px] -left-20 h-[550px] w-[550px] rounded-full bg-gradient-to-tr from-sky-400/10 via-teal-400/10 to-transparent blur-[120px] dark:from-sky-600/10 dark:via-teal-600/10 -z-10"></div>

    {{-- Breadcrumb at Top --}}
    <div class="mx-auto max-w-[1360px] px-4 pt-6 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-neutral-400" aria-label="Breadcrumb">
            <a href="{{ route('frontend.home') }}" class="hover:text-indigo-600 dark:hover:text-white transition">Home</a>
            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="font-bold text-slate-900 dark:text-white">Search</span>
        </nav>
    </div>

    {{-- Centered Hero with Search Bar (Consistent with Blogs Page Header) --}}
    <header class="relative mx-auto max-w-[1360px] px-4 pt-10 pb-12 sm:px-6 sm:pt-16 sm:pb-16 lg:px-8">
        <div class="mx-auto max-w-3xl text-center space-y-6">
            {{-- Eyebrow Badge Pill --}}
            <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/70 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-indigo-700 shadow-xs backdrop-blur-md dark:border-indigo-500/30 dark:bg-indigo-950/50 dark:text-indigo-300">
                <i data-lucide="search" class="h-3.5 w-3.5"></i>
                <span>SEARCH ARTICLES</span>
            </div>

            {{-- Main Headline in Syne --}}
            <h1 class="hero-title text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.12] text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                Search Our <span class="gradient-text">Publications</span>
            </h1>

            {{-- Subtitle --}}
            <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-neutral-300">
                Type keywords, topics, authors, or subjects to instantly discover curated articles and guides across the platform.
            </p>

            {{-- Translucent Glass Search Form --}}
            <div class="pt-2 max-w-xl mx-auto">
                <div class="relative flex items-center rounded-full border border-slate-200/90 bg-white/90 p-1.5 shadow-xl shadow-indigo-500/5 backdrop-blur-xl transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/15 dark:bg-slate-900/80 dark:shadow-none">
                    <i data-lucide="search" class="ml-4 h-5 w-5 text-slate-400 dark:text-neutral-500 shrink-0"></i>
                    <input type="text"
                           wire:model.live.debounce.300ms="query"
                           placeholder="Type a topic, author, keyword…"
                           autofocus
                           class="w-full min-w-0 border-0 border-transparent bg-transparent px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 shadow-none outline-none ring-0 focus:border-0 focus:border-transparent focus:outline-none focus:ring-0 focus:shadow-none dark:text-white dark:placeholder-neutral-500 font-medium">
                    @if (trim($query) !== '')
                        <button type="button" wire:click="$set('query', '')" class="mr-2 text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 cursor-pointer" title="Clear">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    @endif
                    <div class="grid h-10 w-10 sm:h-11 sm:w-11 shrink-0 place-items-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                </div>
            </div>

            @if (trim($query) !== '')
                <p class="pt-1 text-sm font-semibold text-slate-600 dark:text-neutral-300">
                    {{ $this->results->total() }} {{ \Illuminate\Support\Str::plural('result', $this->results->total()) }} for
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">"{{ $query }}"</span>
                </p>
            @endif
        </div>
    </header>

    <section class="mx-auto max-w-7xl px-4 py-10 lg:py-12">
        @if (trim($query) === '')
            <div class="rounded-2xl border-2 border-dashed border-slate-200 p-16 text-center dark:border-slate-700">
                <i data-lucide="search" class="mx-auto h-12 w-12 text-slate-300"></i>
                <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-slate-100">Start typing to find articles</h3>
                <p class="mt-1 text-sm text-slate-500">Search by title, content, author, or keyword.</p>
            </div>
        @elseif ($this->results->isEmpty())
            <div class="rounded-2xl border-2 border-dashed border-slate-200 p-16 text-center dark:border-slate-700">
                <i data-lucide="search-x" class="mx-auto h-12 w-12 text-slate-300"></i>
                <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-slate-100">No results found</h3>
                <p class="mt-1 text-sm text-slate-500">Try different keywords or check the spelling.</p>
                <a href="{{ route('frontend.home') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-emerald-700 hover:underline">
                    Browse all articles <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->results as $post)
                    <x-frontend.post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-10">{{ $this->results->onEachSide(1)->links('livewire.frontend.pagination', ['scrollTo' => 'header']) }}</div>
        @endif
    </section>
</div>
