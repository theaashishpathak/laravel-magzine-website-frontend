@php
    $settings = app(\App\Services\SettingService::class);
    $siteName = (string) ($settings->get('site.name') ?? 'AI Insights');
    $siteDescription = (string) ($settings->get('site.description') ?? 'Stay updated with the latest AI trends, tools, and innovations. Get in-depth articles, expert opinions, and practical guides to help you build, create, and grow with AI.');

    // Homepage Customizer Settings
    $heroEnabled = (bool) $settings->get('homepage.hero_enabled', true);
    $heroTitle = (string) $settings->get('homepage.hero_title', 'Explore the Future with AI Insights');
    $heroSubtitle = (string) $settings->get('homepage.hero_subtitle', $siteDescription);
    $trendingTopicsLabel = (string) $settings->get('homepage.trending_topics_label', 'Trending:');
    $trendingMode = (string) $settings->get('homepage.trending_mode', 'auto');
    $selectedCategories = (array) $settings->get('homepage.selected_categories', []);

    // Feed settings
    $showCategoryBadge = (bool) $settings->get('homepage.show_category_badge', true);
    $showFeaturedBadge = (bool) $settings->get('homepage.show_featured_badge', true);
    $showAuthor = (bool) $settings->get('homepage.show_author', true);
    $showDate = (bool) $settings->get('homepage.show_date', true);
    $showExcerpt = (bool) $settings->get('homepage.show_excerpt', true);
    $discoverBtnText = (string) $settings->get('homepage.discover_button_text', 'Discover More');
    // Sidebar: Featured Widget
    $featuredEnabled = (bool) $settings->get('homepage.featured_enabled', true);
    $featuredTitle = (string) $settings->get('homepage.featured_title', 'FEATURED POSTS');
    $featuredMode = (string) $settings->get('homepage.featured_mode', 'auto');
    $featuredPostId = $settings->get('homepage.featured_post_id');
    $manualFeaturedPost = ($featuredMode === 'manual' && $featuredPostId) ? \App\Models\Post::find($featuredPostId) : null;

    // Sidebar: Categories Widget
    $categoriesEnabled = (bool) $settings->get('homepage.categories_enabled', true);
    $categoriesTitle = (string) $settings->get('homepage.categories_title', 'TOP CATEGORIES');
    $categoriesCount = (int) $settings->get('homepage.categories_count', 5);

    // Sidebar: Tags Widget
    $tagsEnabled = (bool) $settings->get('homepage.tags_enabled', true);
    $tagsTitle = (string) $settings->get('homepage.tags_title', 'POPULAR TOPICS');
    $tagsCount = (int) $settings->get('homepage.tags_count', 10);

    // Sidebar: Editorial Widget
    $editorialEnabled = (bool) $settings->get('homepage.editorial_enabled', true);
    $editorialTitle = (string) $settings->get('homepage.editorial_title', 'EDITORIAL PICKS');
    $editorialMode = (string) $settings->get('homepage.editorial_mode', 'trending');
    $editorialCount = (int) $settings->get('homepage.editorial_count', 7);

    // Newsletter Section
    $newsletterEnabled = (bool) $settings->get('homepage.newsletter_enabled', true);
    $newsletterTitle = (string) $settings->get('homepage.newsletter_title', 'Get the Latest AI Insights Straight to Your Inbox');
    $newsletterSubtitle = (string) $settings->get('homepage.newsletter_subtitle', 'Subscribe to our newsletter and never miss the latest articles, tips and resources.');
    $newsletterButtonText = (string) $settings->get('homepage.newsletter_button_text', 'Subscribe');
    $newsletterBadgeText = (string) $settings->get('homepage.newsletter_badge_text', 'STAY UPDATED');

    // Fetch real top categories with post counts
    $popularCategory = \App\Models\Category::query()
        ->whereHas('posts', fn($q) => $q->where('status', \App\Enums\PostStatus::Published->value))
        ->withCount(['posts' => fn($q) => $q->where('status', \App\Enums\PostStatus::Published->value)])
        ->orderByDesc('posts_count')
        ->take(12)
        ->get();

    // Determine trending topic categories
    if ($trendingMode === 'manual' && !empty($selectedCategories)) {
        $trendingCategories = \App\Models\Category::whereIn('id', $selectedCategories)->get();
    } else {
        $trendingCategories = $popularCategory->take(8);
    }

    // Fetch popular tags
    $popularTags = \App\Models\Tag::query()
        ->has('posts')
        ->withCount('posts')
        ->orderByDesc('posts_count')
        ->take(15)
        ->get();

    // Curated Editorial Picks: Strictly highest count of views (exactly 8 posts)
    $curatedStories = $this->editorsPick;

    // Curated topic cards matching mockup
    $mockupTopics = [
        [
            'name' => 'AI Tools',
            'desc' => 'Discover the best AI tools for productivity and creativity.',
            'icon' => 'cpu',
            'bg_light' => 'bg-blue-50 text-blue-600',
            'bg_dark' => 'dark:bg-blue-950/50 dark:text-blue-400',
            'slug' => 'ai-tools'
        ],
        [
            'name' => 'Machine Learning',
            'desc' => 'Learn ML concepts, models and real-world applications.',
            'icon' => 'brain',
            'bg_light' => 'bg-indigo-50 text-indigo-600',
            'bg_dark' => 'dark:bg-indigo-950/50 dark:text-indigo-400',
            'slug' => 'machine-learning'
        ],
        [
            'name' => 'ChatGPT',
            'desc' => 'Tips, tricks and advanced uses of ChatGPT.',
            'icon' => 'bot',
            'bg_light' => 'bg-sky-50 text-sky-600',
            'bg_dark' => 'dark:bg-sky-950/50 dark:text-sky-400',
            'slug' => 'chatgpt'
        ],
        [
            'name' => 'Automation',
            'desc' => 'Automate your workflow with AI solutions.',
            'icon' => 'cog',
            'bg_light' => 'bg-teal-50 text-teal-600',
            'bg_dark' => 'dark:bg-teal-950/50 dark:text-teal-400',
            'slug' => 'automation'
        ],
        [
            'name' => 'Technology',
            'desc' => 'The latest tech trends and innovations.',
            'icon' => 'bar-chart-3',
            'bg_light' => 'bg-violet-50 text-violet-600',
            'bg_dark' => 'dark:bg-violet-950/50 dark:text-violet-400',
            'slug' => 'technology'
        ],
    ];
@endphp

<div class="relative min-h-screen bg-[var(--bg-primary)] text-[var(--text-primary)] transition-colors duration-200 overflow-x-hidden selection:bg-indigo-600 selection:text-white">

    {{-- ───── Ambient Background Mesh / Radial Glows ───── --}}
    <div class="pointer-events-none absolute -top-24 right-0 h-[650px] w-[650px] rounded-full bg-gradient-to-br from-blue-400/15 via-indigo-500/15 to-purple-400/10 blur-[130px] dark:from-blue-600/15 dark:via-indigo-600/15 dark:to-purple-600/10 -z-10"></div>
    <div class="pointer-events-none absolute top-[600px] -left-20 h-[550px] w-[550px] rounded-full bg-gradient-to-tr from-sky-400/10 via-teal-400/10 to-transparent blur-[120px] dark:from-sky-600/10 dark:via-teal-600/10 -z-10"></div>
    <div class="pointer-events-none absolute bottom-40 right-10 h-[500px] w-[500px] rounded-full bg-gradient-to-tl from-indigo-500/10 via-purple-500/10 to-transparent blur-[120px] dark:from-indigo-600/10 dark:via-purple-600/10 -z-10"></div>


  

    {{-- ───── Hero Section (Faithfully matching "Client Side UI" mockup) ───── --}}
    @if ($heroEnabled)
        <header class="relative mx-auto max-w-[1360px] px-4 pt-8 pb-12 sm:px-6 sm:pt-14 sm:pb-16 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12 lg:gap-12">
                
                {{-- Left Column: Hero Content & Search --}}
                <div class="lg:col-span-7 space-y-6">
                    {{-- Eyebrow Badge Pill --}}
                    <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/70 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-indigo-700 shadow-xs backdrop-blur-md dark:border-indigo-500/30 dark:bg-indigo-950/50 dark:text-indigo-300">
                        <span>THE LATEST IN AI</span>
                    </div>

                    {{-- Main Headline (Syne Bold with brand gradient accent) --}}
                    <h1 class="hero-title text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight sm:leading-[1.08] text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                        @if (str_contains($heroTitle, 'AI Insights'))
                            {!! str_replace('AI Insights', '<span class="gradient-text">AI Future Insights</span>', e($heroTitle)) !!}
                        @else
                            Explore the Future with <span class="gradient-text">AI future Insights</span>
                        @endif
                    </h1>

                    {{-- Subtitle --}}
                    <p class="max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base dark:text-neutral-300">
                        {{ $heroSubtitle }}
                    </p>

                    {{-- Translucent Glass Search Form --}}
                    <div class="pt-2">
                        <form action="{{ route('frontend.search') }}" method="GET"
                            class="relative flex items-center max-w-xl rounded-full border border-slate-200/90 bg-white/85 p-1.5 shadow-xl shadow-indigo-500/5 backdrop-blur-xl transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/15 dark:bg-slate-900/80 dark:shadow-none">
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

                    {{-- Trending Tags Row --}}
                    <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                        <span class="font-bold text-slate-700 dark:text-neutral-300 mr-1">
                            {{ $trendingTopicsLabel }}
                        </span>
                        @if ($trendingCategories->isNotEmpty())
                            @foreach ($trendingCategories->take(5) as $cat)
                                @php
                                    $catName = html_entity_decode((string) ($cat->translate('name') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                @endphp
                                <a href="{{ route('frontend.category', ['slug' => $cat->translate('slug')]) }}"
                                    class="rounded-full border border-slate-200/85 bg-white/70 px-3.5 py-1 font-medium text-slate-600 shadow-xs backdrop-blur-md transition hover:-translate-y-0.5 hover:border-indigo-400 hover:bg-white hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:border-indigo-400 dark:hover:bg-slate-800 dark:hover:text-indigo-400">
                                    {{ $catName }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('frontend.search', ['q' => 'ChatGPT']) }}" class="rounded-full border border-slate-200/85 bg-white/70 px-3.5 py-1 font-medium text-slate-600 shadow-xs backdrop-blur-md transition hover:-translate-y-0.5 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">ChatGPT</a>
                            <a href="{{ route('frontend.search', ['q' => 'Machine Learning']) }}" class="rounded-full border border-slate-200/85 bg-white/70 px-3.5 py-1 font-medium text-slate-600 shadow-xs backdrop-blur-md transition hover:-translate-y-0.5 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">Machine Learning</a>
                            <a href="{{ route('frontend.search', ['q' => 'AI Tools']) }}" class="rounded-full border border-slate-200/85 bg-white/70 px-3.5 py-1 font-medium text-slate-600 shadow-xs backdrop-blur-md transition hover:-translate-y-0.5 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">AI Tools</a>
                            <a href="{{ route('frontend.search', ['q' => 'Deep Learning']) }}" class="rounded-full border border-slate-200/85 bg-white/70 px-3.5 py-1 font-medium text-slate-600 shadow-xs backdrop-blur-md transition hover:-translate-y-0.5 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">Deep Learning</a>
                            <a href="{{ route('frontend.search', ['q' => 'Automation']) }}" class="rounded-full border border-slate-200/85 bg-white/70 px-3.5 py-1 font-medium text-slate-600 shadow-xs backdrop-blur-md transition hover:-translate-y-0.5 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300">Automation</a>
                        @endif
                    </div>
                </div>

                {{-- Right Column: AI Visual with 3 Floating Translucent Glass Badges --}}
                <div class="lg:col-span-5 relative flex items-center justify-center">
                    {{-- Soft Circular Ambient Aura --}}
                    <div class="pointer-events-none absolute inset-0 -m-6 rounded-full bg-gradient-to-tr from-cyan-400/25 via-blue-500/20 to-indigo-600/20 blur-3xl -z-10"></div>

                    {{-- Main Visual Container --}}
                    <div class="relative w-full max-w-[420px] aspect-square rounded-3xl overflow-hidden border border-white/80 bg-white/40 p-2 shadow-2xl shadow-indigo-500/10 backdrop-blur-xl dark:border-white/15 dark:bg-slate-900/40">
                        <img src="/images/hero-ai-visual.jpg"
                             alt="AI Insights - Future of Intelligence"
                             class="h-full w-full object-cover rounded-2xl transition duration-700 hover:scale-105">
                        
                        {{-- Subtle inner bottom gradient vignette --}}
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-slate-950/60 via-slate-950/20 to-transparent"></div>
                    </div>

                    {{-- Floating Glass Badge 1: Top Right "Smarter Ideas" --}}
                    <div class="absolute -top-3 -right-2 sm:-top-4 sm:-right-4 rounded-2xl border border-white/85 bg-white/80 p-3 sm:p-3.5 shadow-xl shadow-indigo-500/10 backdrop-blur-xl dark:border-white/15 dark:bg-slate-900/80 animate-float-slow flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/70 dark:text-indigo-400 shadow-xs">
                            <i data-lucide="sparkles" class="h-4 w-4"></i>
                        </span>
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Smarter</p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-neutral-400 leading-tight">Ideas</p>
                        </div>
                    </div>

                    {{-- Floating Glass Badge 2: Left Middle Trend Chart --}}
                    <div class="absolute top-1/2 -left-3 sm:-left-6 -translate-y-1/2 rounded-2xl border border-white/85 bg-white/80 px-3.5 py-2.5 shadow-xl shadow-indigo-500/10 backdrop-blur-xl dark:border-white/15 dark:bg-slate-900/80 animate-float-reverse flex items-center gap-2.5">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-950/70 dark:text-sky-400 shadow-xs">
                            <i data-lucide="trending-up" class="h-4 w-4"></i>
                        </span>
                        <div class="w-12 h-6 flex items-center">
                            <svg class="w-full h-full text-sky-500" viewBox="0 0 48 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 20 L14 14 L26 18 L46 4"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Floating Glass Badge 3: Bottom Right "Better Tomorrow" --}}
                    <div class="absolute -bottom-3 -right-2 sm:-bottom-4 sm:-right-4 rounded-2xl border border-white/85 bg-white/80 p-3 sm:p-3.5 shadow-xl shadow-indigo-500/10 backdrop-blur-xl dark:border-white/15 dark:bg-slate-900/80 animate-float-slow flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/70 dark:text-amber-400 shadow-xs">
                            <i data-lucide="lightbulb" class="h-4 w-4"></i>
                        </span>
                        <div>
                            <p class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Better</p>
                            <p class="text-xs font-semibold text-slate-500 dark:text-neutral-400 leading-tight">Tomorrow</p>
                        </div>
                    </div>

                </div>

            </div>
        </header>
    @endif

    {{-- ───── Explore Topics / Browse by Category (Matching mockup 5-card row) ───── --}}
    <section class="mx-auto max-w-[1360px] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="flex items-end justify-between mb-7">
            <div>
                <h2 class="section-title text-2xl sm:text-3xl  lg:text-4xl font-bold  text-slate-900 dark:text-white"
                    style="font-family: 'Syne', sans-serif;">
                    Explore Topics
                </h2>
            </div>
            <a href="{{ route('frontend.search') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition group">
                <span>View All Categories</span>
                <i data-lucide="arrow-right" class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>

        {{-- 5-Card Translucent Glass Grid --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 sm:gap-5">
            @php
                // Display real categories if present, otherwise enrich with mockup topics
                $displayTopics = collect();
                if ($popularCategory->isNotEmpty()) {
                    foreach ($popularCategory->take(5) as $idx => $realCat) {
                        $fallback = $mockupTopics[$idx % count($mockupTopics)];
                        $displayTopics->push([
                            'name' => html_entity_decode((string) ($realCat->translate('name') ?? $fallback['name']), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                            'desc' => $realCat->translate('description') ?: $fallback['desc'],
                            'icon' => $realCat->icon ?: $fallback['icon'],
                            'bg_light' => $fallback['bg_light'],
                            'bg_dark' => $fallback['bg_dark'],
                            'url' => route('frontend.category', ['slug' => $realCat->translate('slug')]),
                        ]);
                    }
                }
                // If fewer than 5, backfill from mockup topics
                while ($displayTopics->count() < 5) {
                    $nextFallback = $mockupTopics[$displayTopics->count()];
                    $displayTopics->push([
                        'name' => $nextFallback['name'],
                        'desc' => $nextFallback['desc'],
                        'icon' => $nextFallback['icon'],
                        'bg_light' => $nextFallback['bg_light'],
                        'bg_dark' => $nextFallback['bg_dark'],
                        'url' => route('frontend.search', ['q' => $nextFallback['name']]),
                    ]);
                }
            @endphp

            @foreach ($displayTopics as $topic)
                <a href="{{ $topic['url'] }}"
                   class="glass-card glass-card-hover group relative flex flex-col justify-between rounded-2xl p-5 sm:p-6 transition-all duration-300">
                    <div>
                        {{-- Icon Badge --}}
                        <div class="grid h-12 w-12 place-items-center rounded-2xl {{ $topic['bg_light'] }} {{ $topic['bg_dark'] }} shadow-xs transition duration-300 group-hover:scale-110">
                            <i data-lucide="{{ $topic['icon'] }}" class="h-6 w-6"></i>
                        </div>
                        {{-- Title --}}
                        <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                            {{ $topic['name'] }}
                        </h3>
                        {{-- Description --}}
                        <p class="mt-1.5 text-xs leading-relaxed text-slate-500 dark:text-neutral-400 line-clamp-2">
                            {{ $topic['desc'] }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ───── Latest Articles (5 Stories) + Multi-Widget Glass Sidebar ───── --}}
    <section class="mx-auto max-w-[1360px] px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        {{-- Section Header with Divider (Spans across so category card and stories are at the same level below divider) --}}
        <div class="border-b border-[var(--border-default)] pb-4 mb-8 dark:border-[var(--border-default)]">
            <h2 class="section-title text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-slate-900 dark:text-white"
                style="font-family: 'Syne', sans-serif;">
                Latest Articles
            </h2>
        </div>

        <div class="magazine-grid">
            
            {{-- Left Column: Main Story Rows --}}
            <div id="latest-stories" class="min-w-0">

                @if ($this->latest->isEmpty())
                    <div class="glass-panel rounded-3xl p-16 text-center">
                        <i data-lucide="newspaper" class="mx-auto h-12 w-12 text-slate-300 dark:text-neutral-600"></i>
                        <h3 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">No posts published yet</h3>
                        <p class="mt-1 text-sm text-slate-500">Check back soon for fresh stories.</p>
                    </div>
                @else
                    <div class="space-y-6 sm:space-y-8">
                        @foreach ($this->latest->take(5) as $post)
                            @php
                                $postT = $post->translation() ?? ($post->translations->firstWhere('language_id', $post->default_language_id) ?? $post->translations->first());
                                $slug = $postT?->slug;
                                $url = $slug ? route('frontend.post.show', ['slug' => $slug]) : null;
                                $feat = $post->featuredImage;
                                $hasImg = $feat && ($feat->isImage() || ($feat->path !== null && $feat->path !== ''));
                                $imgUrl = $hasImg ? $feat->url() : "https://picsum.photos/seed/np{$post->id}/800/500";
                                $postCatName = html_entity_decode((string) ($post->category?->translate('name') ?? 'GENERAL'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $authorName = $post->author?->name ?? ($leadAuthor?->name ?? 'Author');
                                $postDate = $post->published_at?->format('F d, Y') ?? 'Recent';
                            @endphp

                            @if ($postT && $slug && !$post->is_featured)
                                <article class="story-card group">
                                    {{-- Thumbnail Media Card --}}
                                    <div class="story-card-media">
                                        <a href="{{ $url }}" class="block h-full w-full">
                                            <img src="{{ $imgUrl }}" alt="{{ $postT->title }}" loading="lazy">
                                        </a>
                                        <div class="absolute left-3.5 top-3.5 flex items-center gap-1.5 z-10">
                                            @if ($showCategoryBadge)
                                                <span class="category-glass-pill">
                                                    <span class="pill-dot"></span>
                                                    <span>{{ $postCatName }}</span>
                                                </span>
                                            @endif
                                            @if (!$post->is_featured)
                                                <span class="inline-flex items-center gap-1 rounded-full border border-blue-400/30 bg-blue-500/15 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-500 dark:text-blue-400 backdrop-blur-md">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse"></span>
                                                    <span>Latest</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Content Column --}}
                                    <div class="story-card-content">
                                        {{-- Byline --}}
                                        @if ($showAuthor || $showDate)
                                            @php
                                                $authorInitials = collect(explode(' ', $authorName))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
                                                $readingMinutes = max(1, (int) ceil(str_word_count(strip_tags((string) ($postT->content ?? ''))) / 200));
                                            @endphp
                                            <div class="flex flex-wrap items-center gap-2.5 text-xs text-slate-500 dark:text-neutral-400 mb-1">
                                                @if ($showAuthor)
                                                    <div class="inline-flex items-center gap-1.5 group/author">
                                                        @if ($post->author?->avatar)
                                                            <img src="{{ $post->author->avatarUrl() }}" alt="{{ $authorName }}" class="h-5 w-5 rounded-full object-cover ring-1 ring-slate-300 dark:ring-white/20">
                                                        @else
                                                            <span class="grid h-5 w-5 place-items-center rounded-full bg-indigo-500/15 text-[9px] font-black text-indigo-700 dark:text-indigo-300 ring-1 ring-indigo-500/30">
                                                                {{ $authorInitials ?: 'A' }}
                                                            </span>
                                                        @endif
                                                        <span class="font-bold text-slate-900 dark:text-slate-100 group-hover/author:text-indigo-600 dark:group-hover/author:text-indigo-400 transition">
                                                            {{ $authorName }}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if ($showAuthor && $showDate)
                                                    <span class="text-slate-300 dark:text-neutral-600 font-bold">·</span>
                                                @endif

                                                @if ($showDate)
                                                    <span class="inline-flex items-center gap-1 font-medium text-slate-600 dark:text-neutral-300">
                                                        <i data-lucide="calendar" class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400"></i>
                                                        <span>{{ $post->published_at?->format('M d, Y') ?? $postDate }}</span>
                                                    </span>
                                                @endif

                                                <span class="text-slate-300 dark:text-neutral-600 font-bold">·</span>

                                                <span class="inline-flex items-center gap-1 font-medium text-slate-500 dark:text-neutral-400">
                                                    <i data-lucide="clock" class="h-3.5 w-3.5 text-slate-400 dark:text-neutral-500"></i>
                                                    <span>{{ $readingMinutes }} min read</span>
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Title --}}
                                        <a href="{{ $url }}" class="mt-2 block">
                                            <h2 class="story-card-title article-title text-xl sm:text-2xl md:text-3xl font-bold leading-snug break-words group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition"
                                                style="font-family: 'Syne', sans-serif;">
                                                {{ html_entity_decode(str_replace(["\xc2\xa0", '&nbsp;'], ' ', (string) $postT->title), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                            </h2>
                                        </a>

                                        {{-- Excerpt --}}
                                        @if ($showExcerpt)
                                            <p class="mt-2.5 text-xs sm:text-sm text-slate-600 dark:text-neutral-400 leading-relaxed line-clamp-2 break-words">
                                                <span class="text-indigo-400 mr-1.5 font-bold">✣</span>{{ html_entity_decode(str_replace(["\xc2\xa0", '&nbsp;'], ' ', (string) ($postT->excerpt ?: 'Providing authentic perspectives and in-depth analyses on contemporary artificial intelligence advancements.')), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                            </p>
                                        @endif

                                        {{-- Discover More Button --}}
                                        <div class="mt-4 pt-0.5">
                                            <a href="{{ $url }}" class="discover-btn hover:border-indigo-500 hover:text-indigo-600 dark:hover:border-indigo-400 dark:hover:text-indigo-400">
                                                <span>{{ $discoverBtnText ?: 'Discover More' }}</span>
                                                <i data-lucide="arrow-right" class="h-3 w-3"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        @endforeach
                    </div>
                @endif


            </div>

            {{-- Right Sidebar: Glassmorphism Multi-Widgets (Category, Featured, Topics, Editorial) --}}
            <aside class="space-y-8 min-w-0">
                
                {{-- ───── Widget 1: TOP CATEGORIES ───── --}}
                @if ($categoriesEnabled && $popularCategory->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-5" style="font-family: 'Syne', sans-serif;">
                            {{ $categoriesTitle }}
                        </h3>
                        <div class="space-y-3">
                            @foreach ($popularCategory->take($categoriesCount) as $cat)
                                @php
                                    $cName = html_entity_decode((string) ($cat->translate('name') ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                @endphp
                                <a href="{{ route('frontend.category', ['slug' => $cat->translate('slug')]) }}"
                                   class="flex items-center justify-between py-1.5 text-xs font-semibold text-slate-700 dark:text-neutral-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition group border-b border-slate-100 dark:border-white/10 last:border-0">
                                    <span class="flex items-center gap-2">
                                        @if ($cat->icon)
                                            <i data-lucide="{{ $cat->icon }}" class="h-3.5 w-3.5 text-slate-400 group-hover:text-indigo-500"></i>
                                        @else
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400 group-hover:bg-indigo-500"></span>
                                        @endif
                                        <span>{{ $cName }}</span>
                                    </span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 dark:bg-white/10 dark:text-neutral-300">
                                        {{ $cat->posts_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ───── Widget 2: FEATURED SPOTLIGHT CAROUSEL ───── --}}
                @if ($featuredEnabled)
                    @php
                        $featuredItems = $manualFeaturedPost 
                            ? collect([$manualFeaturedPost])->concat($this->featured->where('id', '!=', $manualFeaturedPost->id))
                            : $this->featured;
                    @endphp
                    @if ($featuredItems->isNotEmpty())
                        <div x-data="{
                                active: 0,
                                total: {{ $featuredItems->count() }},
                                timer: null,
                                next() { this.active = (this.active + 1) % this.total; },
                                prev() { this.active = (this.active - 1 + this.total) % this.total; },
                                start() { this.timer = setInterval(() => this.next(), 6000); },
                                stop() { clearInterval(this.timer); }
                             }"
                             x-init="start()"
                             x-on:mouseenter="stop()"
                             x-on:mouseleave="start()"
                             class="sidebar-widget-card relative overflow-hidden group/featured">
                            
                            {{-- Header with Dynamic Navigation & Slide Counter --}}
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 animate-pulse"></span>
                                    <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400" style="font-family: 'Syne', sans-serif;">
                                        {{ $featuredTitle }}
                                    </h3>
                                </div>

                                @if ($featuredItems->count() > 1)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-bold tracking-wider text-slate-400 dark:text-neutral-500"
                                              x-text="(active + 1).toString().padStart(2, '0') + '/' + total.toString().padStart(2, '0')">
                                            01/{{ str_pad((string)$featuredItems->count(), 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <div class="flex items-center gap-1 ml-1">
                                            <button type="button" @click="prev()" aria-label="Previous featured story"
                                                    class="grid h-6 w-6 place-items-center rounded-lg border border-slate-200/80 bg-white/70 text-slate-600 hover:border-indigo-400 hover:bg-indigo-600 hover:text-white dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-400 dark:hover:bg-indigo-600 dark:hover:text-white transition duration-150 cursor-pointer">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="next()" aria-label="Next featured story"
                                                    class="grid h-6 w-6 place-items-center rounded-lg border border-slate-200/80 bg-white/70 text-slate-600 hover:border-indigo-400 hover:bg-indigo-600 hover:text-white dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-400 dark:hover:bg-indigo-600 dark:hover:text-white transition duration-150 cursor-pointer">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Spotlight Slides Stage --}}
                            <div class="relative overflow-hidden rounded-2xl aspect-[16/11] bg-slate-950 shadow-lg shadow-indigo-500/10 ring-1 ring-black/5 dark:ring-white/10">
                                @foreach ($featuredItems->values() as $idx => $fCard)
                                    @php
                                        $fcT = $fCard->translation() ?? ($fCard->translations->firstWhere('language_id', $fCard->default_language_id) ?? $fCard->translations->first());
                                        $fcSlug = $fcT?->slug;
                                        $fcUrl = $fcSlug ? route('frontend.post.show', ['slug' => $fcSlug]) : '#';
                                        $fcImg = $fCard->featuredImage?->url() ?? "https://picsum.photos/seed/np{$fCard->id}/700/500";
                                        $fcCatName = html_entity_decode((string) ($fCard->category?->translate('name') ?? 'FEATURED'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                        $authorName = $fCard->author?->name ?? 'Editor';
                                        $authorAvatar = $fCard->author?->profile_photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($authorName) . "&background=3B50F9&color=fff";
                                    @endphp
                                    <div x-show="active === {{ $idx }}"
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-300 transform"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-200 absolute inset-0 transform"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="group/slide absolute inset-0">
                                        
                                        {{-- Image with hover zoom --}}
                                        <img src="{{ $fcImg }}" alt="{{ $fcT?->title }}"
                                             class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover/slide:scale-108">
                                        
                                        {{-- Dramatic Gradient Overlay --}}
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-slate-900/20"></div>

                                        {{-- Top Row: Category Glass Badge + Star Pill --}}
                                        <div class="absolute left-3.5 right-3.5 top-3.5 flex items-center justify-between gap-2 z-10">
                                            <span class="category-glass-pill backdrop-blur-md">
                                                <span class="pill-dot"></span>
                                                <span>{{ $fcCatName }}</span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 rounded-full border border-amber-400/30 bg-amber-400/10 px-2 py-0.5 text-[10px] font-bold text-amber-300 backdrop-blur-md shadow-xs">
                                                <span>★</span>
                                                <span>Featured</span>
                                            </span>
                                        </div>

                                        {{-- Bottom Content on Glass Backdrop --}}
                                        <div class="absolute inset-x-0 bottom-0 p-4 z-10">
                                            {{-- Author & Date Meta --}}
                                            <div class="flex items-center gap-2 mb-1.5">
                                                <img src="{{ $authorAvatar }}" alt="{{ $authorName }}" class="h-4 w-4 rounded-full object-cover ring-1 ring-white/30">
                                                <span class="text-[11px] font-medium text-slate-300 truncate max-w-[120px]">
                                                    {{ $authorName }}
                                                </span>
                                                <span class="text-slate-500">•</span>
                                                <span class="text-[10px] font-medium text-slate-400">
                                                    {{ $fCard->published_at?->format('M d, Y') ?? 'Recent' }}
                                                </span>
                                            </div>

                                            {{-- Headline --}}
                                            <a href="{{ $fcUrl }}" class="block group/link">
                                                <h4 class="text-sm sm:text-base font-bold text-white leading-snug group-hover/link:text-indigo-300 transition-colors line-clamp-2" style="font-family: 'Syne', sans-serif;">
                                                    {{ $fcT?->title }}
                                                </h4>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Bottom Animated Dot/Pill Progress Indicators --}}
                            @if ($featuredItems->count() > 1)
                                <div class="flex items-center justify-center gap-1.5 mt-3.5">
                                    @foreach ($featuredItems->values() as $idx => $fCard)
                                        <button type="button"
                                                @click="active = {{ $idx }}"
                                                aria-label="Go to featured slide {{ $idx + 1 }}"
                                                class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                                :class="active === {{ $idx }} ? 'w-6 bg-gradient-to-r from-blue-500 to-indigo-600 shadow-xs' : 'w-2 bg-slate-200 dark:bg-white/20 hover:bg-indigo-300 dark:hover:bg-white/40'">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endif

                {{-- ───── Widget 3: POPULAR TOPICS ───── --}}
                @if ($tagsEnabled && $popularTags->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-5" style="font-family: 'Syne', sans-serif;">
                            {{ $tagsTitle }}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($popularTags->take($tagsCount) as $t)
                                @php
                                    $tagName = html_entity_decode((string) ($t->translate('name') ?? $t->name), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    $tagSlug = $t->translate('slug') ?? $t->slug;
                                @endphp
                                <a href="{{ route('frontend.tag', ['tag' => $tagSlug]) }}"
                                   class="inline-flex items-center gap-1 rounded-xl border border-slate-200/80 bg-white/70 px-3 py-1.5 text-[11px] font-medium text-slate-600 transition hover:border-indigo-400 hover:bg-white hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400">
                                    <span>#{{ $tagName }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ───── Widget 4: CURATED EDITORIAL PICKS ───── --}}
                @if ($editorialEnabled && $curatedStories->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-5" style="font-family: 'Syne', sans-serif;">
                            {{ $editorialTitle }}
                        </h3>
                        <div class="space-y-4 text-xs">
                            @foreach ($curatedStories->take(8) as $cs)
                                @php
                                    $csT = $cs->translation() ?? ($cs->translations->firstWhere('language_id', $cs->default_language_id) ?? $cs->translations->first());
                                    $csSlug = $csT?->slug;
                                    $csUrl = $csSlug ? route('frontend.post.show', ['slug' => $csSlug]) : '#';
                                @endphp
                                @if ($csT && $csSlug)
                                    <div class="@if (!$loop->first) border-t border-slate-100 pt-3.5 dark:border-white/10 @endif">
                                        <a href="{{ $csUrl }}" class="group flex items-start justify-between gap-2 font-bold text-slate-900 dark:text-white hover:text-indigo-500 transition">
                                            <span class="line-clamp-2 leading-snug" style="font-family: 'Syne', sans-serif;">{{ $csT->title }}</span>
                                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400 dark:text-neutral-500 group-hover:text-indigo-500 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                            </svg>
                                        </a>
                                        @if ($csT->excerpt)
                                            <p class="mt-1 text-[11px] text-slate-500 dark:text-neutral-400 line-clamp-2">{{ $csT->excerpt }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    {{-- ───── Reimagined Translucent Glass Newsletter Section (Matching mockup) ───── --}}
    @if ($newsletterEnabled)
        <section class="mx-auto max-w-[1360px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl border border-indigo-100/90 bg-gradient-to-r from-blue-50/80 via-indigo-50/60 to-purple-50/80 p-8 sm:p-12 shadow-xl shadow-indigo-500/5 backdrop-blur-xl dark:border-white/10 dark:from-slate-900/80 dark:via-indigo-950/40 dark:to-slate-900/80">
                
                {{-- Decorative Floating Paper Airplane graphic --}}
                <div class="pointer-events-none absolute right-8 top-8 opacity-80 sm:right-16 sm:top-1/2 sm:-translate-y-1/2">
                    <svg class="h-28 w-28 text-sky-400/30 sm:h-40 sm:w-40" viewBox="0 0 100 100" fill="currentColor">
                        <path d="M10 50 L90 10 L60 90 L45 60 Z"></path>
                    </svg>
                </div>

                <div class="relative z-10 grid grid-cols-1 items-center gap-8 lg:grid-cols-12">
                    {{-- Left side: Copy --}}
                    <div class="lg:col-span-7 space-y-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">
                            {{ $newsletterBadgeText }}
                        </span>
                        <h2 class="section-title text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-slate-900 dark:text-white leading-tight"
                            style="font-family: 'Syne', sans-serif;">
                            {{ $newsletterTitle }}
                        </h2>
                        <p class="max-w-lg text-xs sm:text-sm text-slate-600 dark:text-neutral-300">
                            {{ $newsletterSubtitle }}
                        </p>
                    </div>

                    {{-- Right side: Subscription Form --}}
                    <div class="lg:col-span-5">
                        <form action="{{ route('frontend.search') }}" method="GET"
                            class="relative flex items-center rounded-full border border-slate-200/90 bg-white/90 p-1.5 shadow-lg shadow-indigo-500/5 backdrop-blur-xl transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 dark:border-white/15 dark:bg-slate-900/90">
                            <input type="email" placeholder="Enter your email address..." required
                                class="w-full bg-transparent px-4 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 outline-none dark:text-white dark:placeholder-neutral-500 font-medium">
                            <button type="submit"
                                class="btn-primary shrink-0 text-xs font-bold active:scale-95">
                                {{ $newsletterButtonText }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </section>
    @endif

</div>
