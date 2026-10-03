<div class="relative overflow-hidden">
    {{-- ───── Ambient Background Mesh / Radial Glows ───── --}}
    <div class="pointer-events-none absolute -top-24 right-0 h-[650px] w-[650px] rounded-full bg-gradient-to-br from-blue-400/15 via-indigo-500/15 to-purple-400/10 blur-[130px] dark:from-blue-600/15 dark:via-indigo-600/15 dark:to-purple-600/10 -z-10"></div>
    <div class="pointer-events-none absolute top-[600px] -left-20 h-[550px] w-[550px] rounded-full bg-gradient-to-tr from-sky-400/10 via-teal-400/10 to-transparent blur-[120px] dark:from-sky-600/10 dark:via-teal-600/10 -z-10"></div>
    <div class="pointer-events-none absolute bottom-40 right-10 h-[500px] w-[500px] rounded-full bg-gradient-to-tl from-indigo-500/10 via-purple-500/10 to-transparent blur-[120px] dark:from-indigo-600/10 dark:via-purple-600/10 -z-10"></div>

    {{-- ───── Hero Section (Clean Layout with Ambient Glow, without sideimage) ───── --}}
    <header class="relative mx-auto max-w-[1360px] px-4 pt-10 pb-12 sm:px-6 sm:pt-16 sm:pb-16 lg:px-8">
        <div class="mx-auto max-w-3xl text-center space-y-6">
            {{-- Eyebrow Badge Pill --}}
            <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/70 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-indigo-700 shadow-xs backdrop-blur-md dark:border-indigo-500/30 dark:bg-indigo-950/50 dark:text-indigo-300">
                <i data-lucide="sparkles" class="h-3.5 w-3.5"></i>
                <span>OUR BLOG</span>
            </div>

            {{-- Main Headline in Syne --}}
            <h1 class="hero-title text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.12] text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                Latest Articles on <span class="gradient-text">AI, Tech & Innovation</span>
            </h1>

            {{-- Subtitle --}}
            <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-neutral-300">
                Stay informed with the latest insights, trends, and practical guides on Artificial Intelligence, machine learning, automation and more. Learn, explore and stay ahead in the AI era.
            </p>

            {{-- Translucent Glass Search Form --}}
            <div class="pt-2 max-w-xl mx-auto">
                <form action="{{ route('frontend.search') }}" method="GET"
                      class="relative flex items-center rounded-full border border-slate-200/90 bg-white/90 p-1.5 shadow-xl shadow-indigo-500/5 backdrop-blur-xl transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/15 dark:bg-slate-900/80 dark:shadow-none">
                    <i data-lucide="search" class="ml-4 h-5 w-5 text-slate-400 dark:text-neutral-500 shrink-0"></i>
                    <input type="text" name="q" placeholder="Search articles, topics, or keywords..."
                           class="w-full bg-transparent px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 outline-none dark:text-white dark:placeholder-neutral-500 font-medium">
                    <button type="submit"
                            class="grid h-10 w-10 sm:h-11 sm:w-11 shrink-0 place-items-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25 transition hover:shadow-indigo-500/40 hover:scale-105 active:scale-95 cursor-pointer"
                            title="Search" aria-label="Search">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- ───── Main Content Section (Grid & Sidebar) ───── --}}
    <section id="blog-feed" class="mx-auto max-w-[1360px] px-4 pb-20 sm:px-6 lg:px-8">
        
        {{-- Filter Pills Bar & Sorting Dropdown (Directly from Mockup) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pt-4 border-t border-slate-200/70 dark:border-white/10">
            
            {{-- Category Filter Pills (Scrollable horizontally) --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar text-xs font-semibold">
                <button type="button"
                        wire:click="selectCategory('all')"
                        class="rounded-full px-4 py-2 transition-all cursor-pointer whitespace-nowrap {{ $selectedCategory === 'all' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25 font-bold' : 'border border-slate-200/80 bg-white/80 text-slate-600 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:border-indigo-400 dark:hover:text-white' }}">
                    All
                </button>

                @foreach ($this->categories as $cat)
                    @php
                        $catSlug = $cat->translate('slug') ?? ('cat-' . $cat->id);
                        $catName = html_entity_decode((string) ($cat->translate('name') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $isSelected = $selectedCategory === $catSlug;
                    @endphp
                    @if (!empty($catName))
                        <button type="button"
                                wire:click="selectCategory('{{ $catSlug }}')"
                                class="rounded-full px-4 py-2 transition-all cursor-pointer whitespace-nowrap {{ $isSelected ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25 font-bold' : 'border border-slate-200/80 bg-white/80 text-slate-600 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:border-indigo-400 dark:hover:text-white' }}">
                            {{ $catName }}
                        </button>
                    @endif
                @endforeach
            </div>

            {{-- Sorting Dropdown --}}
            <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                <div class="relative">
                    <select wire:model.live="sortBy"
                            class="appearance-none rounded-full border border-slate-200/80 bg-white/80 pl-4 pr-9 py-2 text-xs font-bold text-slate-700 shadow-xs outline-none transition focus:border-indigo-500 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-200 cursor-pointer">
                        <option value="latest">Latest Posts</option>
                        <option value="views">Most Viewed</option>
                        <option value="oldest">Oldest Posts</option>
                    </select>
                    <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Filters Strip (If filtered) --}}
        @if ($search !== '' || $selectedCategory !== 'all')
            <div class="mb-6 flex flex-wrap items-center gap-2 text-xs">
                <span class="text-slate-500 dark:text-neutral-400 font-medium">Filtering by:</span>
                @if ($selectedCategory !== 'all')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-500/30">
                        Category: {{ ucfirst(str_replace('-', ' ', $selectedCategory)) }}
                        <button type="button" wire:click="selectCategory('all')" class="hover:text-indigo-900 dark:hover:text-white">
                            <i data-lucide="x" class="h-3 w-3"></i>
                        </button>
                    </span>
                @endif
                @if ($search !== '')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-500/30">
                        "{{ $search }}"
                        <button type="button" wire:click="$set('search', '')" class="hover:text-indigo-900 dark:hover:text-white">
                            <i data-lucide="x" class="h-3 w-3"></i>
                        </button>
                    </span>
                @endif
                <button type="button" wire:click="clearFilters()" class="text-xs font-semibold text-slate-500 hover:text-indigo-600 underline ml-2 cursor-pointer">
                    Clear All
                </button>
            </div>
        @endif

        {{-- 2-Column Grid: Left Feed (8 Cols) + Right Sidebar (4 Cols) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            
            {{-- Left Column: 2-Col Blog Cards Grid --}}
            <div class="lg:col-span-8 min-w-0">
                @if ($this->posts->isEmpty())
                    <div class="glass-panel rounded-3xl p-16 text-center">
                        <i data-lucide="newspaper" class="mx-auto h-12 w-12 text-slate-300 dark:text-neutral-600"></i>
                        <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                            No articles found
                        </h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            No articles match your current criteria. Try adjusting your search or category filters.
                        </p>
                        <button type="button" wire:click="clearFilters()" class="mt-4 inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-500 transition">
                            <span>Reset Filters</span>
                        </button>
                    </div>
                @else
                    {{-- 2-Column Cards Grid (Faithfully matching All Blogs.png) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($this->posts as $post)
                            @php
                                $postT = $post->translation() ?? ($post->translations->firstWhere('language_id', $post->default_language_id) ?? $post->translations->first());
                                $slug = $postT?->slug;
                                $url = $slug ? route('frontend.post.show', ['slug' => $slug]) : '#';
                                $feat = $post->featuredImage;
                                $hasImg = $feat && ($feat->isImage() || ($feat->path !== null && $feat->path !== ''));
                                $imgUrl = $hasImg ? $feat->url() : "https://picsum.photos/seed/np{$post->id}/800/500";
                                $postCatName = html_entity_decode((string) ($post->category?->translate('name') ?? 'TECHNOLOGY'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $authorName = $post->author?->name ?? 'Editorial Staff';
                                $postDate = $post->published_at?->format('M d, Y') ?? 'Recent';
                                $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) ($postT->content ?? ''))) / 200));
                            @endphp

                            @if ($postT && $slug)
                                <article class="blog-card group flex flex-col justify-between rounded-3xl overflow-hidden">
                                    
                                    {{-- Card Top: Image with Top-Left Floating Dark Pill --}}
                                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-900">
                                        <a href="{{ $url }}" class="block h-full w-full">
                                            <img src="{{ $imgUrl }}"
                                                 alt="{{ $postT->title }}"
                                                 loading="lazy"
                                                 class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                                        </a>

                                        {{-- Top-Left Floating Category Pill --}}
                                        <div class="absolute top-4 left-4 z-10">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900/85 px-3 py-1.5 backdrop-blur-md border border-white/10 shadow-sm">
                                                <span class="h-2 w-2 rounded-full bg-blue-500 shrink-0"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-wider text-white">
                                                    {{ $postCatName }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Card Body --}}
                                    <div class="p-6 sm:p-7 flex flex-col flex-1">
                                        
                                        {{-- Author · Date · Reading Time --}}
                                        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-neutral-400">
                                            <span class="font-bold text-slate-800 dark:text-neutral-100">{{ $authorName }}</span>
                                            <span class="text-slate-300 dark:text-neutral-600">·</span>
                                            <span class="text-slate-600 dark:text-neutral-300">{{ $postDate }}</span>
                                            <span class="text-slate-300 dark:text-neutral-600">·</span>
                                            <span class="text-slate-600 dark:text-neutral-300">{{ $readingMinutes }}m read</span>
                                        </div>

                                        {{-- Title in Syne --}}
                                        <a href="{{ $url }}" class="block mt-3.5 group/title">
                                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white leading-snug line-clamp-2 group-hover/title:text-blue-600 dark:group-hover/title:text-indigo-400 transition"
                                                style="font-family: 'Syne', sans-serif;">
                                                {{ html_entity_decode((string) $postT->title, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                            </h2>
                                        </a>

                                        {{-- Excerpt --}}
                                        <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-neutral-300 line-clamp-2 leading-relaxed flex-1">
                                            {{ html_entity_decode((string) ($postT->excerpt ?: 'The rebranded experience replaces the classic 10-blue-links page for signed-in users in 12...'), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                        </p>

                                        {{-- Read Full Article Link --}}
                                        <div class="mt-6 pt-1">
                                            <a href="{{ $url }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-blue-600 dark:text-indigo-400 hover:text-blue-700 dark:hover:text-indigo-300 group-hover:gap-2.5 transition-all">
                                                <span>Read Full Article</span>
                                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>

                    {{-- Pagination Controls matching Mockup --}}
                    <div class="mt-12 flex justify-center">
                        {{ $this->posts->links('livewire.frontend.pagination', ['scrollTo' => '#blog-feed']) }}
                    </div>
                @endif
            </div>

            {{-- Right Sidebar (Directly from Mockup) --}}
            <aside class="lg:col-span-4 space-y-8 min-w-0">
                
                {{-- ───── Sidebar Widget 1: SEARCH BLOG ───── --}}
                <div class="sidebar-widget-card">
                    <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-4" style="font-family: 'Syne', sans-serif;">
                        Search Blog
                    </h3>
                    <div class="relative flex items-center rounded-2xl border border-slate-200/90 bg-slate-50/80 p-1.5 focus-within:border-indigo-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/10 dark:bg-slate-900/60 dark:focus-within:bg-slate-900 transition">
                        <i data-lucide="search" class="ml-3 h-4 w-4 text-slate-400 dark:text-neutral-500 shrink-0"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search articles, topics..."
                               class="w-full bg-transparent px-2.5 py-1.5 text-xs text-slate-900 placeholder-slate-400 outline-none dark:text-white dark:placeholder-neutral-500 font-medium">
                        
                        <button type="button"
                                class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xs"
                                aria-label="Search">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- ───── Sidebar Widget 2: TOP CATEGORIES (Matching Homepage) ───── --}}
                @if ($this->categories->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-5" style="font-family: 'Syne', sans-serif;">
                            TOP CATEGORIES
                        </h3>
                        <div class="space-y-3">
                            @foreach ($this->categories as $cat)
                                @php
                                    $catSlug = $cat->translate('slug') ?? ('cat-' . $cat->id);
                                    $catName = html_entity_decode((string) ($cat->translate('name') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    $isCurSelected = $selectedCategory === $catSlug;
                                @endphp
                                @if (!empty($catName))
                                    <button type="button"
                                            wire:click="selectCategory('{{ $catSlug }}')"
                                            class="w-full flex items-center justify-between py-1.5 text-xs font-semibold transition group border-b border-slate-100 dark:border-white/10 last:border-0 cursor-pointer text-left {{ $isCurSelected ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-700 dark:text-neutral-300 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                                        <span class="flex items-center gap-2">
                                            @if ($cat->icon)
                                                <i data-lucide="{{ $cat->icon }}" class="h-3.5 w-3.5 {{ $isCurSelected ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 group-hover:text-indigo-500' }} transition"></i>
                                            @else
                                                <span class="h-1.5 w-1.5 rounded-full {{ $isCurSelected ? 'bg-indigo-600' : 'bg-slate-400 group-hover:bg-indigo-500' }} transition"></span>
                                            @endif
                                            <span>{{ $catName }}</span>
                                        </span>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold transition {{ $isCurSelected ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-neutral-300' }}">
                                            {{ $cat->posts_count }}
                                        </span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ───── Sidebar Widget 3: RECENT POSTS ───── --}}
                @if ($this->recentPosts->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-4" style="font-family: 'Syne', sans-serif;">
                            Recent Posts
                        </h3>
                        <div class="space-y-4">
                            @foreach ($this->recentPosts as $rp)
                                @php
                                    $rpT = $rp->translation() ?? ($rp->translations->firstWhere('language_id', $rp->default_language_id) ?? $rp->translations->first());
                                    $rpSlug = $rpT?->slug;
                                    $rpUrl = $rpSlug ? route('frontend.post.show', ['slug' => $rpSlug]) : '#';
                                    $rpImg = $rp->featuredImage?->url() ?? "https://picsum.photos/seed/np{$rp->id}/160/160";
                                    $rpDate = $rp->published_at?->format('M d, Y') ?? 'Recent';
                                    $rpRead = max(1, (int) ceil(str_word_count(strip_tags((string) ($rpT->content ?? ''))) / 200));
                                @endphp
                                @if ($rpT && $rpSlug)
                                    <div class="group flex items-center gap-3">
                                        <a href="{{ $rpUrl }}" class="h-14 w-14 shrink-0 overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200/60 dark:border-white/10">
                                            <img src="{{ $rpImg }}" alt="{{ $rpT->title }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110" loading="lazy">
                                        </a>
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ $rpUrl }}" class="block">
                                                <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-snug line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition" style="font-family: 'Syne', sans-serif;">
                                                    {{ html_entity_decode((string) $rpT->title, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                                </h4>
                                            </a>
                                            <div class="mt-1 flex items-center gap-2 text-[10px] text-slate-400">
                                                <span>{{ $rpDate }}</span>
                                                <span>·</span>
                                                <span>{{ $rpRead }} min read</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ───── Sidebar Widget 4: NEWSLETTER SIGNUP (Matching Mockup) ───── --}}
                <div class="relative overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-br from-blue-50/90 via-indigo-50/80 to-purple-50/90 p-6 shadow-xl shadow-indigo-500/5 backdrop-blur-xl dark:border-white/10 dark:from-slate-900/90 dark:via-indigo-950/50 dark:to-slate-900/90">
                    {{-- Decorative Paper Airplane graphic in top-right --}}
                    <div class="pointer-events-none absolute right-4 top-4 opacity-75">
                        <svg class="h-14 w-14 text-indigo-500/30" viewBox="0 0 100 100" fill="currentColor">
                            <path d="M10 50 L90 10 L60 90 L45 60 Z"></path>
                        </svg>
                    </div>

                    <div class="relative z-10 space-y-3">
                        <h4 class="text-base font-bold text-slate-900 dark:text-white leading-snug" style="font-family: 'Syne', sans-serif;">
                            Get the Latest AI Insights Straight to Your Inbox
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-neutral-300 leading-relaxed">
                            Subscribe to our newsletter and never miss the latest articles, tips and resources.
                        </p>

                        <form action="{{ route('frontend.home') }}" method="GET" class="space-y-2.5 pt-2">
                            <input type="email"
                                   required
                                   placeholder="Enter your email address..."
                                   class="w-full rounded-xl border border-slate-200/90 bg-white/95 px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 shadow-xs outline-none transition focus:border-indigo-500 dark:border-white/15 dark:bg-slate-950/80 dark:text-white">
                            <button type="submit"
                                    class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-500/25 transition hover:shadow-indigo-500/40 hover:scale-[1.02] active:scale-95 cursor-pointer"
                                    style="font-family: 'Syne', sans-serif;">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>

            </aside>
        </div>
    </section>
</div>
