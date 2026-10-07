@php
    $currentLocale = app(\App\Support\LocaleResolver::class)->current();
    $catDescMap = [
        'artificial-intelligence' => 'Deep dives into core AI breakthroughs, neural networks, machine learning algorithms, and intelligent computing.',
        'generative-ai' => 'Exploring modern generative models, multimodal intelligence, diffusion architectures, synthetic media, and creative workflows.',
        'llms-nlp' => 'Practical guides, architecture breakdowns, prompt engineering, context windows, and natural language processing developments.',
        'ai-agents' => 'Autonomous agent architectures, multi-agent coordination, tool execution loops, memory systems, and goal-driven AI.',
        'ai-engineering' => 'Production AI stacks, RAG evaluation, embedding pipelines, fine-tuning, latency optimization, and robust model deployment.',
        'software-development' => 'Clean code, system architecture, design patterns, testing strategies, and modern programming language paradigms.',
        'web-development' => 'Fullstack web development, modern frontend frameworks, responsive UI, backend architectures, and web performance.',
        'cloud-devops' => 'Cloud infrastructure, Kubernetes, CI/CD automation, Docker, observability, site reliability, and container orchestration.',
        'cybersecurity' => 'Vulnerability research, application defense, cryptography, zero trust architectures, ethical hacking, and digital privacy.',
        'data-analytics' => 'Data pipelines, modern data warehousing, stream processing, business intelligence dashboards, and predictive metrics.',
        'blockchain-web3' => 'Decentralized networks, smart contracts, zero-knowledge proofs, consensus mechanisms, and Web3 protocol architecture.',
        'emerging-technology' => 'Quantum computing, spatial computing, robotics, neuromorphic hardware, edge devices, and frontier tech horizons.',
        // Legacy fallbacks
        'technology' => 'Discover the best tech tools, software and platforms to boost your productivity, creativity and workflow.',
        'business' => 'Insights into emerging markets, enterprise strategies, venture capital and global finance.',
        'world' => 'Global news, geopolitical analyses, diplomacy and international community reports.',
        'politics' => 'In-depth political commentary, governance, elections and global policy developments.',
        'culture' => 'Arts, design, entertainment, digital lifestyle and societal transformations.',
        'sports' => 'Comprehensive coverage of international sporting tournaments, leagues and athletic milestones.',
    ];
@endphp

<div class="relative overflow-hidden">
    {{-- ───── Ambient Background Mesh / Radial Glows (Matching Home & Blogs Hero) ───── --}}
    <div class="pointer-events-none absolute -top-24 right-0 h-[650px] w-[650px] rounded-full bg-gradient-to-br from-blue-400/20 via-indigo-500/20 to-purple-400/15 blur-[130px] dark:from-blue-600/20 dark:via-indigo-600/20 dark:to-purple-600/15 -z-10"></div>
    <div class="pointer-events-none absolute top-[600px] -left-20 h-[550px] w-[550px] rounded-full bg-gradient-to-tr from-sky-400/15 via-teal-400/10 to-transparent blur-[120px] dark:from-sky-600/15 dark:via-teal-600/10 -z-10"></div>
    <div class="pointer-events-none absolute bottom-40 right-10 h-[500px] w-[500px] rounded-full bg-gradient-to-tl from-indigo-500/15 via-purple-500/15 to-transparent blur-[120px] dark:from-indigo-600/15 dark:via-purple-600/15 -z-10"></div>

    @if ($isIndividual)
        

        {{-- ───── INDIVIDUAL CATEGORY HERO (Consistent with Blogs Page Header) ───── --}}
        <header class="relative mx-auto max-w-[1360px] px-4 pt-10 pb-12 sm:px-6 sm:pt-16 sm:pb-16 lg:px-8">
            <div class="mx-auto max-w-3xl text-center space-y-6">
                {{-- Eyebrow Badge Pill --}}
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/70 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-indigo-700 shadow-xs backdrop-blur-md dark:border-indigo-500/30 dark:bg-indigo-950/50 dark:text-indigo-300">
                    <i data-lucide="tag" class="h-3.5 w-3.5"></i>
                    <span>CATEGORY</span>
                </div>

                {{-- Category Headline in Syne --}}
                @php
                    $nameParts = explode(' ', trim($activeCategoryName));
                    $firstWord = array_shift($nameParts);
                    $remainingWords = implode(' ', $nameParts);
                @endphp
                <h1 class="hero-title text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.12] text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                    <span>{{ $firstWord }}</span>
                    @if ($remainingWords)
                        <span class="gradient-text">{{ $remainingWords }}</span>
                    @else
                        <span class="gradient-text">Articles</span>
                    @endif
                </h1>

                {{-- Subtitle / Description --}}
                <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-neutral-300">
                    {{ $metaDescription }}
                </p>

                {{-- Translucent Glass Search Form --}}
                <div class="pt-2 max-w-xl mx-auto">
                    <div class="relative flex items-center rounded-full border border-slate-200/90 bg-white/90 p-1.5 shadow-xl shadow-indigo-500/5 backdrop-blur-xl transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/15 dark:bg-slate-900/80 dark:shadow-none">
                        <i data-lucide="search" class="ml-4 h-5 w-5 text-slate-400 dark:text-neutral-500 shrink-0"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search articles in {{ $activeCategoryName }}..."
                               class="w-full min-w-0 border-0 border-transparent bg-transparent px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 shadow-none outline-none ring-0 focus:border-0 focus:border-transparent focus:outline-none focus:ring-0 focus:shadow-none dark:text-white dark:placeholder-neutral-500 font-medium">
                        @if ($search !== '')
                            <button type="button" wire:click="$set('search', '')" class="mr-2 text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 cursor-pointer" title="Clear">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        @endif
                        <button type="button"
                                class="grid h-10 w-10 sm:h-11 sm:w-11 shrink-0 place-items-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25 transition hover:shadow-indigo-500/40 hover:scale-105 active:scale-95 cursor-pointer"
                                title="Search" aria-label="Search">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- 4 Feature Pills Strip (Centered) --}}
                <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-3 pt-2">
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs backdrop-blur-md dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">
                        <i data-lucide="zap" class="h-3.5 w-3.5 text-blue-500"></i>
                        <span>Latest {{ $activeCategoryName }}</span>
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs backdrop-blur-md dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">
                        <i data-lucide="search" class="h-3.5 w-3.5 text-indigo-500"></i>
                        <span>In-Depth Reviews</span>
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs backdrop-blur-md dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">
                        <i data-lucide="book-open" class="h-3.5 w-3.5 text-purple-500"></i>
                        <span>Tips & Tutorials</span>
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-xs backdrop-blur-md dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">
                        <i data-lucide="sparkles" class="h-3.5 w-3.5 text-pink-500"></i>
                        <span>Expert Insights</span>
                    </span>
                </div>
            </div>
        </header>
    @else
        {{-- ═════════════════════════════════════════════════════════════════ --}}
        {{-- ───── "ALL CATEGORIES" DIRECTORY HERO (Consistent with Blogs Page Header) ───── --}}
        {{-- ═════════════════════════════════════════════════════════════════ --}}
        <header class="relative mx-auto max-w-[1360px] px-4 pt-10 pb-12 sm:px-6 sm:pt-16 sm:pb-16 lg:px-8">
            <div class="mx-auto max-w-3xl text-center space-y-6">
                {{-- Eyebrow Badge Pill --}}
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/70 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-indigo-700 shadow-xs backdrop-blur-md dark:border-indigo-500/30 dark:bg-indigo-950/50 dark:text-indigo-300">
                    <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                    <span>CATEGORIES</span>
                </div>

                {{-- Main Headline in Syne --}}
                <h1 class="hero-title text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.12] text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                    Explore by <span class="gradient-text">Category</span>
                </h1>

                {{-- Subtitle --}}
                <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-neutral-300">
                    Find the latest articles, tutorials and insights on your favorite topics. Browse through our curated categories and dive deeper into the world of AI, tech and culture.
                </p>

                {{-- Translucent Glass Search Form --}}
                <div class="pt-2 max-w-xl mx-auto">
                    <div class="relative flex items-center rounded-full border border-slate-200/90 bg-white/90 p-1.5 shadow-xl shadow-indigo-500/5 backdrop-blur-xl transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/15 dark:bg-slate-900/80 dark:shadow-none">
                        <i data-lucide="search" class="ml-4 h-5 w-5 text-slate-400 dark:text-neutral-500 shrink-0"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search articles across all categories..."
                               class="w-full min-w-0 border-0 border-transparent bg-transparent px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 shadow-none outline-none ring-0 focus:border-0 focus:border-transparent focus:outline-none focus:ring-0 focus:shadow-none dark:text-white dark:placeholder-neutral-500 font-medium">
                        @if ($search !== '')
                            <button type="button" wire:click="$set('search', '')" class="mr-2 text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 cursor-pointer" title="Clear">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        @endif
                        <button type="button"
                                class="grid h-10 w-10 sm:h-11 sm:w-11 shrink-0 place-items-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25 transition hover:shadow-indigo-500/40 hover:scale-105 active:scale-95 cursor-pointer"
                                title="Search" aria-label="Search">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Stats Strip (Centered) --}}
                <div class="flex flex-wrap items-center justify-center gap-6 pt-1 text-xs font-semibold text-slate-600 dark:text-neutral-400">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                            <i data-lucide="layers" class="h-4 w-4"></i>
                        </span>
                        <span>{{ $this->categories->count() }} Curated Topics</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                            <i data-lucide="newspaper" class="h-4 w-4"></i>
                        </span>
                        <span>{{ $this->categories->sum('posts_count') }}+ Total Articles</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- ───── "All Categories" Directory Cards Section (Matching category.png Mockup) ───── --}}
        <section class="mx-auto max-w-[1360px] px-4 pb-16 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between pb-6 border-b border-slate-200/70 dark:border-white/10">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                        All Categories
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-neutral-400 mt-0.5">
                        Browse topics to quickly discover curated articles and guides
                    </p>
                </div>
                <span class="rounded-full bg-slate-100 dark:bg-white/10 px-3 py-1 text-xs font-bold text-slate-600 dark:text-neutral-300">
                    {{ $this->categories->count() }} Categories
                </span>
            </div>

            {{-- 4-Column Grid of Category Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
                @foreach ($this->categories as $cat)
                    @php
                        $catSlug = $cat->translate('slug') ?? ('cat-' . $cat->id);
                        $catName = html_entity_decode((string) ($cat->translate('name') ?? ('#' . $cat->id)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $catColor = $cat->color ?: '#6366f1';
                        $catIcon = $cat->icon ?: 'folder';
                        $catDesc = $cat->translate('description') ?: ($catDescMap[$catSlug] ?? "Explore curated articles, updates and practical analyses in {$catName}.");
                        $isCurrent = $selectedCategory === $catSlug;
                    @endphp
                    <a href="{{ route('frontend.category', ['slug' => $catSlug]) }}"
                       class="blog-card group relative flex flex-col justify-between rounded-3xl p-6 transition-all duration-300 hover:-translate-y-1.5 cursor-pointer {{ $isCurrent ? 'ring-2 ring-indigo-500 border-indigo-500 shadow-lg shadow-indigo-500/10' : '' }}">
                        
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="grid h-12 w-12 place-items-center rounded-2xl shadow-xs transition-transform duration-300 group-hover:scale-110"
                                      style="background-color: {{ $catColor }}18; color: {{ $catColor }};">
                                    <i data-lucide="{{ $catIcon }}" class="h-6 w-6"></i>
                                </span>
                            </div>

                            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition mt-4" style="font-family: 'Syne', sans-serif;">
                                {{ $catName }}
                            </h3>

                            <p class="text-xs font-semibold text-slate-500 dark:text-neutral-400 mt-1">
                                {{ $cat->posts_count }} {{ \Illuminate\Support\Str::plural('Article', $cat->posts_count) }}
                            </p>

                            <p class="text-xs text-slate-600 dark:text-neutral-400 mt-3 line-clamp-3 leading-relaxed">
                                {{ $catDesc }}
                            </p>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-[11px] font-bold">
                            <span class="{{ $isCurrent ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-neutral-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400' }} transition">
                                {{ $isCurrent ? 'Viewing Articles ↓' : 'View Articles →' }}
                            </span>
                            @if ($isCurrent)
                                <span class="h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ───── Top Filter Bar: Category Pills + Search Bar in the Same Row ───── --}}
        <div class="mx-auto max-w-[1360px] px-4 sm:px-6 lg:px-8 pb-5 border-b border-slate-200 dark:border-white/5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                
                {{-- Category Filter Pills (Scrollable horizontally) --}}
                <div class="flex items-center gap-2 sm:gap-2.5 overflow-x-auto pb-2 lg:pb-0 scrollbar-hide scrollbar-none no-scrollbar flex-1 min-w-0">
                    <button type="button"
                            wire:click="selectCategory('all')"
                            class="shrink-0 rounded-full px-5 py-2.5 text-xs font-bold transition cursor-pointer shadow-xs {{ $selectedCategory === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'border border-slate-200/80 bg-white/80 text-slate-700 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:border-indigo-400' }}">
                        All Categories
                    </button>

                    @foreach ($this->categories as $c)
                        @php
                            $cSlug = $c->translate('slug') ?? ('cat-' . $c->id);
                            $cName = html_entity_decode((string) ($c->translate('name') ?? ('#' . $c->id)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $isSel = $selectedCategory === $cSlug;
                        @endphp
                        <button type="button"
                                wire:click="selectCategory('{{ $cSlug }}')"
                                class="shrink-0 rounded-full px-4 sm:px-5 py-2.5 text-xs font-bold transition cursor-pointer shadow-xs {{ $isSel ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/25' : 'border border-slate-200/80 bg-white/80 text-slate-700 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:border-indigo-400' }}">
                            {{ $cName }}
                        </button>
                    @endforeach
                </div>

                {{-- Search Bar in the Same Row --}}
                <div class="w-full lg:w-80 sm:w-96 shrink-0">
                    <div class="relative flex items-center rounded-full border border-slate-200/90 bg-white/90 p-1 pl-4 shadow-xs backdrop-blur-md transition focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/10 dark:bg-slate-900/80">
                        <i data-lucide="search" class="h-4 w-4 text-slate-400 shrink-0"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search articles, topics..."
                               class="w-full min-w-0 border-0 border-transparent bg-transparent px-3 py-1.5 text-xs font-medium text-slate-900 placeholder-slate-400 shadow-none outline-none ring-0 focus:border-0 focus:border-transparent focus:outline-none focus:ring-0 focus:shadow-none dark:text-white dark:placeholder-neutral-500">
                        
                        @if ($search !== '')
                            <button type="button"
                                    wire:click="$set('search', '')"
                                    class="mr-1 text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 cursor-pointer"
                                    title="Clear search">
                                <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            </button>
                        @endif

                        <button type="button"
                                class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-xs transition hover:opacity-95 cursor-pointer"
                                aria-label="Search">
                            <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ═════════════════════════════════════════════════════════════════ --}}
    {{-- ───── MAIN ARTICLES FEED + SIDEBAR (Matching Mockups) ───── --}}
    {{-- ═════════════════════════════════════════════════════════════════ --}}
    <section id="category-posts" class="mx-auto max-w-[1360px] px-4 pt-6 pb-20 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            
            {{-- Left Column (8 cols): Articles Feed --}}
            <div class="lg:col-span-8 min-w-0">
                


                {{-- Active Filter Pills / Search Notice --}}
                @if ($search !== '' || ($selectedCategory !== 'all' && !$isIndividual))
                    <div class="mb-6 flex flex-wrap items-center gap-2 text-xs">
                        <span class="text-slate-500 dark:text-neutral-400 font-medium">Filtering by:</span>
                        @if ($selectedCategory !== 'all' && !$isIndividual)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-500/30">
                                Category: {{ $activeCategoryName }}
                                <button type="button" wire:click="selectCategory('all')" class="hover:text-indigo-900 dark:hover:text-white cursor-pointer">
                                    <i data-lucide="x" class="h-3 w-3"></i>
                                </button>
                            </span>
                        @endif
                        @if ($search !== '')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-500/30">
                                "{{ $search }}"
                                <button type="button" wire:click="$set('search', '')" class="hover:text-indigo-900 dark:hover:text-white cursor-pointer">
                                    <i data-lucide="x" class="h-3 w-3"></i>
                                </button>
                            </span>
                            <button type="button" wire:click="clearFilters()" class="text-indigo-600 hover:underline dark:text-indigo-400 font-semibold cursor-pointer">
                                Clear All
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Empty State --}}
                @if ($this->posts->isEmpty())
                    <div class="blog-card rounded-3xl p-12 text-center">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                            <i data-lucide="folder-search" class="h-8 w-8"></i>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                            No articles found
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                            {{ $search ? "No posts matched \"{$search}\"." : "There are currently no published articles in this category." }}
                        </p>
                        @if ($search !== '' || $selectedCategory !== 'all')
                            <button type="button"
                                    wire:click="clearFilters()"
                                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-indigo-500/25 transition hover:bg-indigo-500 cursor-pointer">
                                <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                                <span>Reset Filters</span>
                            </button>
                        @endif
                    </div>
                @else
                    {{-- ───── 2-Column Articles Grid (Faithfully matching Blogs Page Card Layout) ───── --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-7">
                        @foreach ($this->posts as $post)
                            @php
                                $postT = $post->translation() ?? ($post->translations->firstWhere('language_id', $post->default_language_id) ?? $post->translations->first());
                                $slug = $postT?->slug;
                                $url = $slug ? route('frontend.post.show', ['slug' => $slug]) : '#';
                                $img = $post->featuredImage?->url() ?? "https://picsum.photos/seed/catpost{$post->id}/800/480";
                                $postCat = $post->category;
                                $cName = html_entity_decode((string) ($postCat?->translate('name') ?? $activeCategoryName), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $postDate = $post->published_at?->format('M d, Y') ?? 'Recent';
                                $authorName = $post->author?->name ?? 'Editorial Staff';
                                $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) ($postT?->content ?? ''))) / 200));
                            @endphp

                            @if ($postT)
                                <article class="blog-card group flex flex-col overflow-hidden rounded-3xl transition-all duration-300 hover:-translate-y-1.5 cursor-pointer">
                                    {{-- Thumbnail Box --}}
                                    <div class="relative aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-900">
                                        <a href="{{ $url }}" class="block h-full w-full">
                                            <img src="{{ $img }}"
                                                 alt="{{ $postT->title }}"
                                                 loading="lazy"
                                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        </a>

                                        {{-- Top-Left Floating Dark Capsule Badge with Blue Dot --}}
                                        <div class="absolute left-4 top-4 z-10">
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-900/90 text-white backdrop-blur-md px-3.5 py-1.5 text-[11px] font-bold tracking-wide uppercase shadow-lg shadow-black/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                <span>{{ $cName }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Card Content Body --}}
                                    <div class="flex flex-1 flex-col justify-between p-6 sm:p-7">
                                        <div>
                                            {{-- Meta Line: Author · Date · Reading Time --}}
                                            <div class="flex items-center gap-2 text-xs font-semibold">
                                                <span class="font-bold text-slate-800 dark:text-neutral-100">{{ $authorName }}</span>
                                                <span class="text-slate-300 dark:text-neutral-600">·</span>
                                                <span class="text-slate-600 dark:text-neutral-300">{{ $postDate }}</span>
                                                <span class="text-slate-300 dark:text-neutral-600">·</span>
                                                <span class="text-slate-600 dark:text-neutral-300">{{ $readingMinutes }}m read</span>
                                            </div>

                                            {{-- Title in Syne --}}
                                            <a href="{{ $url }}" class="block mt-3.5 group/title">
                                                <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white leading-snug line-clamp-2 group-hover/title:text-blue-600 dark:group-hover/title:text-indigo-400 transition"
                                                    style="font-family: 'Syne', sans-serif;">
                                                    {{ html_entity_decode((string) $postT->title, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                                </h3>
                                            </a>

                                            {{-- Excerpt --}}
                                            <p class="mt-3 text-xs sm:text-sm text-slate-600 dark:text-neutral-300 line-clamp-2 leading-relaxed flex-1">
                                                {{ html_entity_decode((string) ($postT->excerpt ?: 'Insights and comprehensive analyses on the latest advancements and industry trends.'), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                            </p>
                                        </div>

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

                    {{-- Pagination Controls --}}
                    <div class="mt-12 flex justify-center">
                        {{ $this->posts->links('livewire.frontend.pagination', ['scrollTo' => '#category-posts']) }}
                    </div>
                @endif
            </div>

            {{-- Right Sidebar (4 cols): Search, Popular Categories, Recent Posts, Newsletter --}}
            <aside class="lg:col-span-4 space-y-8 min-w-0">
                


                {{-- ───── Sidebar Widget 2: POPULAR CATEGORIES (Matching Mockup) ───── --}}
                @if ($this->categories->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-5" style="font-family: 'Syne', sans-serif;">
                            Popular Categories
                        </h3>
                        <div class="space-y-3">
                            @foreach ($this->categories as $c)
                                @php
                                    $cSlug = $c->translate('slug') ?? ('cat-' . $c->id);
                                    $cName = html_entity_decode((string) ($c->translate('name') ?? ('#' . $c->id)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    $isCurSelected = $selectedCategory === $cSlug;
                                @endphp
                                @if (!empty($cName))
                                    <a href="{{ route('frontend.category', ['slug' => $cSlug]) }}"
                                       class="w-full flex items-center justify-between py-1.5 text-xs font-semibold transition group border-b border-slate-100 dark:border-white/10 last:border-0 cursor-pointer text-left {{ $isCurSelected ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-700 dark:text-neutral-300 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                                        <span class="flex items-center gap-2">
                                            @if ($c->icon)
                                                <i data-lucide="{{ $c->icon }}" class="h-3.5 w-3.5 {{ $isCurSelected ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 group-hover:text-indigo-500' }} transition"></i>
                                            @else
                                                <span class="h-1.5 w-1.5 rounded-full {{ $isCurSelected ? 'bg-indigo-600' : 'bg-slate-400 group-hover:bg-indigo-500' }} transition"></span>
                                            @endif
                                            <span>{{ $cName }}</span>
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold transition {{ $isCurSelected ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-neutral-300' }}">
                                                {{ $c->posts_count }}
                                            </span>
                                            <svg class="h-3 w-3 text-slate-400 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ───── Sidebar Widget 3: RECENT POSTS (Matching Mockup) ───── --}}
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

                {{-- ───── Sidebar Widget 4: STAY UPDATED NEWSLETTER (Matching Mockup) ───── --}}
                <div class="relative overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-br from-blue-50/90 via-indigo-50/80 to-purple-50/90 p-6 shadow-xl shadow-indigo-500/5 backdrop-blur-xl dark:border-white/10 dark:from-slate-900/90 dark:via-indigo-950/50 dark:to-slate-900/90">
                    {{-- Decorative Paper Airplane graphic in top-right --}}
                    <div class="pointer-events-none absolute right-4 top-4 opacity-75">
                        <svg class="h-14 w-14 text-indigo-500/30" viewBox="0 0 100 100" fill="currentColor">
                            <path d="M10 50 L90 10 L60 90 L45 60 Z"></path>
                        </svg>
                    </div>

                    <div class="relative z-10 space-y-3">
                        <div class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-500/30">
                            <i data-lucide="mail" class="h-5 w-5"></i>
                        </div>

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

    {{-- ───── Bottom Banner: Stay Ahead with AI Insights (Matching Mockup) ───── --}}
    <section class="mx-auto max-w-[1360px] px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border border-indigo-100 bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-purple-50/90 p-8 sm:p-10 shadow-lg shadow-indigo-500/5 backdrop-blur-xl dark:border-white/10 dark:from-slate-900/90 dark:via-indigo-950/40 dark:to-slate-900/90 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="pointer-events-none absolute -right-6 -bottom-6 opacity-30 dark:opacity-10">
                <svg class="h-40 w-40 text-indigo-600" viewBox="0 0 100 100" fill="currentColor">
                    <path d="M10 50 L90 10 L60 90 L45 60 Z"></path>
                </svg>
            </div>

            <div class="relative z-10 max-w-lg">
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                    Stay Ahead with AI Insights
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-neutral-300 mt-1 leading-relaxed">
                    Get the latest articles, trends, and resources delivered directly to your inbox.
                </p>
            </div>

            <div class="relative z-10 w-full md:w-auto min-w-[280px] sm:min-w-[360px]">
                <form action="{{ route('frontend.home') }}" method="GET" class="flex items-center rounded-full border border-slate-200/90 bg-white/95 p-1.5 shadow-sm dark:border-white/15 dark:bg-slate-950/80">
                    <input type="email"
                           required
                           placeholder="Enter your email address..."
                           class="w-full bg-transparent px-4 py-2 text-xs text-slate-900 placeholder-slate-400 outline-none dark:text-white dark:placeholder-neutral-500">
                    <button type="submit"
                            class="shrink-0 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-500/25 transition hover:shadow-indigo-500/40 hover:scale-[1.02] active:scale-95 cursor-pointer"
                            style="font-family: 'Syne', sans-serif;">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
