@php
    $social = is_array($author->social_links) ? $author->social_links : [];
    $socialIcons = [
        'twitter' => 'twitter',
        'facebook' => 'facebook',
        'linkedin' => 'linkedin',
        'instagram' => 'instagram',
        'youtube' => 'youtube',
        'website' => 'globe',
        'github' => 'github',
    ];
@endphp

<div class="relative overflow-hidden">
    {{-- ───── Ambient Background Mesh / Radial Glows ───── --}}
    <div class="pointer-events-none absolute -top-24 right-0 h-[650px] w-[650px] rounded-full bg-gradient-to-br from-blue-400/15 via-indigo-500/15 to-purple-400/10 blur-[130px] dark:from-blue-600/15 dark:via-indigo-600/15 dark:to-purple-600/10 -z-10"></div>
    <div class="pointer-events-none absolute top-[600px] -left-20 h-[550px] w-[550px] rounded-full bg-gradient-to-tr from-sky-400/10 via-teal-400/10 to-transparent blur-[120px] dark:from-sky-600/10 dark:via-teal-600/10 -z-10"></div>
    <div class="pointer-events-none absolute bottom-40 right-10 h-[500px] w-[500px] rounded-full bg-gradient-to-tl from-indigo-500/10 via-purple-500/10 to-transparent blur-[120px] dark:from-indigo-600/10 dark:via-purple-600/10 -z-10"></div>

    {{-- ───── Breadcrumbs at Top ───── --}}
    <div class="mx-auto max-w-[1360px] px-4 pt-6 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-neutral-400" aria-label="Breadcrumb">
            <a href="{{ route('frontend.home') }}" class="hover:text-indigo-600 dark:hover:text-white transition">Home</a>
            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-slate-400 dark:text-neutral-500">Authors</span>
            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="font-bold text-slate-900 dark:text-white truncate">{{ $author->name }}</span>
        </nav>
    </div>

    {{-- ───── AUTHOR PROFILE HERO CARD (Matching Mockup with Dynamic Backend Data) ───── --}}
    <header class="mx-auto max-w-[1360px] px-4 pt-6 pb-10 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white/80 p-6 sm:p-8 lg:p-10 shadow-xl shadow-indigo-500/5 backdrop-blur-xl dark:border-white/10 dark:bg-slate-900/80">
            
            {{-- Internal Ambient Halo Glow --}}
            <div class="pointer-events-none absolute -top-20 -left-20 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl dark:bg-blue-600/10"></div>
            <div class="pointer-events-none absolute -bottom-20 -right-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl dark:bg-indigo-600/10"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center lg:items-center justify-between gap-8 lg:gap-12">
                
                {{-- Left & Center: Avatar + Info --}}
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-6 lg:gap-8 flex-1 min-w-0">
                    
                    {{-- Large Circular Avatar with halo ring --}}
                    <div class="relative shrink-0">
                        <div class="relative h-32 w-32 sm:h-40 sm:w-40 lg:h-44 lg:w-44 overflow-hidden rounded-full ring-4 ring-white dark:ring-slate-800 shadow-2xl bg-gradient-to-tr from-blue-100 to-indigo-100 dark:from-slate-800 dark:to-indigo-950">
                            @if ($author->avatar)
                                <img src="{{ $author->avatarUrl() }}" alt="{{ $author->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="grid h-full w-full place-items-center bg-gradient-to-br from-indigo-500/10 to-blue-500/20 text-indigo-700 dark:text-indigo-300 text-4xl sm:text-5xl font-black uppercase">
                                    {{ mb_substr($author->name, 0, 1) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Author Details --}}
                    <div class="space-y-3 min-w-0 flex-1">
                        {{-- Eyebrow Badge Pill --}}
                        <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/70 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-indigo-700 shadow-xs backdrop-blur-md dark:border-indigo-500/30 dark:bg-indigo-950/50 dark:text-indigo-300">
                            <i data-lucide="user-check" class="h-3.5 w-3.5"></i>
                            <span>AUTHOR</span>
                        </div>

                        {{-- Author Name --}}
                        <h1 class="hero-title text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                            {{ $author->name }}
                        </h1>

                        {{-- Job Title / Designation (Rendered dynamically from backend) --}}
                        <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ $author->job_title ?: ($author->isStaff() ? 'Staff Writer & Contributor' : 'Contributing Author') }}
                        </p>

                        {{-- Bio (Rendered dynamically from backend) --}}
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-neutral-300 leading-relaxed max-w-xl">
                            {{ $author->bio ?: 'Technology and AI researcher focused on artificial intelligence, machine learning, and practical insights for modern creators.' }}
                        </p>

                        {{-- Social Links + Follow Action Row --}}
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-2">
                            @if (! empty($social))
                                <div class="flex items-center gap-2">
                                    @foreach ($socialIcons as $platform => $icon)
                                        @if (! empty($social[$platform]))
                                            @php
                                                $href = $social[$platform];
                                                if (! str_starts_with($href, 'http') && $platform !== 'website') {
                                                    $handle = ltrim($href, '@');
                                                    $href = match ($platform) {
                                                        'twitter' => "https://twitter.com/{$handle}",
                                                        'facebook' => "https://facebook.com/{$handle}",
                                                        'linkedin' => "https://linkedin.com/in/{$handle}",
                                                        'instagram' => "https://instagram.com/{$handle}",
                                                        'youtube' => "https://youtube.com/@{$handle}",
                                                        'github' => "https://github.com/{$handle}",
                                                        default => $href,
                                                    };
                                                }
                                            @endphp
                                            <a href="{{ $href }}" target="_blank" rel="noopener"
                                               aria-label="{{ $platform }}"
                                               class="grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-full border border-slate-200/90 bg-white/90 text-slate-600 shadow-xs transition hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-slate-800/80 dark:text-neutral-300 dark:hover:border-indigo-400 dark:hover:text-white">
                                                <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Follow Button --}}
                            <livewire:frontend.follow-button targetType="author" :targetId="$author->id" :wire:key="'follow-author-'.$author->id" />
                        </div>
                    </div>
                </div>

                {{-- Right: 4 Backend-Derived Stat Cards (Matching Mockup Stack) --}}
                <div class="w-full lg:w-80 shrink-0">
                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-5 shadow-inner dark:border-white/5 dark:bg-slate-800/50 space-y-4">
                        
                        {{-- Stat 1: Articles Published --}}
                        <div class="flex items-center gap-3.5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-blue-100/80 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 shadow-xs">
                                <i data-lucide="file-text" class="h-5 w-5"></i>
                            </span>
                            <div>
                                <div class="text-lg font-black text-slate-900 dark:text-white leading-none" style="font-family: 'Syne', sans-serif;">
                                    {{ $this->totalArticlesCount }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-neutral-400 mt-1 font-medium">
                                    Articles Published
                                </div>
                            </div>
                        </div>

                        {{-- Stat 2: Total Reads --}}
                        <div class="flex items-center gap-3.5 pt-1 border-t border-slate-200/60 dark:border-white/5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-indigo-100/80 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 shadow-xs">
                                <i data-lucide="book-open" class="h-5 w-5"></i>
                            </span>
                            <div>
                                <div class="text-lg font-black text-slate-900 dark:text-white leading-none" style="font-family: 'Syne', sans-serif;">
                                    {{ $this->formattedTotalReads }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-neutral-400 mt-1 font-medium">
                                    Total Reads
                                </div>
                            </div>
                        </div>

                        {{-- Stat 3: Topics Covered --}}
                        <div class="flex items-center gap-3.5 pt-1 border-t border-slate-200/60 dark:border-white/5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-purple-100/80 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 shadow-xs">
                                <i data-lucide="layers" class="h-5 w-5"></i>
                            </span>
                            <div>
                                <div class="text-lg font-black text-slate-900 dark:text-white leading-none" style="font-family: 'Syne', sans-serif;">
                                    {{ $this->topicsCoveredCount }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-neutral-400 mt-1 font-medium">
                                    Topics Covered
                                </div>
                            </div>
                        </div>

                        {{-- Stat 4: Years of Experience / Active Tenure --}}
                        <div class="flex items-center gap-3.5 pt-1 border-t border-slate-200/60 dark:border-white/5">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-pink-100/80 text-pink-600 dark:bg-pink-950/60 dark:text-pink-400 shadow-xs">
                                <i data-lucide="award" class="h-5 w-5"></i>
                            </span>
                            <div>
                                <div class="text-lg font-black text-slate-900 dark:text-white leading-none" style="font-family: 'Syne', sans-serif;">
                                    {{ $this->yearsOfExperience }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-neutral-400 mt-1 font-medium">
                                    Years of Experience
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- ───── Filter Pills Bar & Sorting Dropdown (Matching Mockup) ───── --}}
    <div class="mx-auto max-w-[1360px] px-4 pb-6 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-white/10">
            
            {{-- Topic Filter Pills --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide scrollbar-none no-scrollbar text-xs font-semibold">
                <button type="button"
                        wire:click="selectCategory('all')"
                        class="rounded-full px-4 py-2 transition-all cursor-pointer whitespace-nowrap {{ $selectedCategory === 'all' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25 font-bold' : 'border border-slate-200/80 bg-white/80 text-slate-600 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:border-indigo-400 dark:hover:text-white' }}">
                    All Articles
                </button>

                @foreach ($this->authorTopics as $cat)
                    @php
                        $catSlug = $cat->translate('slug') ?? ('cat-' . $cat->id);
                        $catName = html_entity_decode((string) ($cat->translate('name') ?? ('#' . $cat->id)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $isCurrent = $selectedCategory === $catSlug;
                    @endphp
                    @if (!empty($catName))
                        <button type="button"
                                wire:click="selectCategory('{{ $catSlug }}')"
                                class="rounded-full px-4 py-2 transition-all cursor-pointer whitespace-nowrap {{ $isCurrent ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-indigo-500/25 font-bold' : 'border border-slate-200/80 bg-white/80 text-slate-600 hover:border-indigo-400 hover:text-indigo-600 dark:border-white/10 dark:bg-slate-900/60 dark:text-neutral-300 dark:hover:border-indigo-400 dark:hover:text-white' }}">
                            {{ $catName }}
                        </button>
                    @endif
                @endforeach
            </div>

            {{-- Sort Dropdown + Search input --}}
            <div class="flex items-center gap-3 shrink-0">
                <div class="relative">
                    <select wire:model.live="sort"
                            class="appearance-none rounded-full border border-slate-200/80 bg-white/80 py-2 pl-4 pr-9 text-xs font-bold text-slate-700 shadow-xs backdrop-blur-md outline-none transition hover:border-indigo-400 dark:border-white/10 dark:bg-slate-900/80 dark:text-neutral-200 cursor-pointer">
                        <option value="latest">Latest First</option>
                        <option value="popular">Most Popular</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                    <i data-lucide="chevron-down" class="pointer-events-none absolute right-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>

        </div>
    </div>

    {{-- ═════════════════════════════════════════════════════════════════ --}}
    {{-- ───── MAIN ARTICLES SECTION (GRID LAYOUT + SIDEBAR WITH AUTHOR ROW) ───── --}}
    {{-- ═════════════════════════════════════════════════════════════════ --}}
    <section id="author-posts" class="mx-auto max-w-[1360px] px-4 pt-2 pb-20 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            
            {{-- Left Column (8 cols): Articles in GRID format (per user instruction) --}}
            <div class="lg:col-span-8 min-w-0">
                
                {{-- Feed Title & Count --}}
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                        Articles by {{ $author->name }}
                    </h2>
                    <span class="rounded-full bg-slate-100 dark:bg-white/10 px-3 py-1 text-xs font-bold text-slate-600 dark:text-neutral-300">
                        {{ $this->posts->total() }} {{ \Illuminate\Support\Str::plural('Article', $this->posts->total()) }}
                    </span>
                </div>

                {{-- Active Filter Pills Notice --}}
                @if ($search !== '' || $selectedCategory !== 'all')
                    <div class="mb-6 flex flex-wrap items-center gap-2 text-xs">
                        <span class="text-slate-500 dark:text-neutral-400 font-medium">Filtering by:</span>
                        @if ($selectedCategory !== 'all')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-500/30">
                                Topic: {{ $selectedCategory }}
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
                        @endif
                        <button type="button" wire:click="clearFilters()" class="text-indigo-600 hover:underline dark:text-indigo-400 font-semibold cursor-pointer">
                            Clear All
                        </button>
                    </div>
                @endif

                {{-- Empty State --}}
                @if ($this->posts->isEmpty())
                    <div class="blog-card rounded-3xl p-12 text-center">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                            <i data-lucide="file-question" class="h-8 w-8"></i>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                            No articles found
                        </h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                            {{ $search ? "No posts matched \"{$search}\"." : "There are currently no published articles by this author in this category." }}
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
                    {{-- ───── 2-Column Responsive Articles Grid (Replacing list cards with grid) ───── --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-7">
                        @foreach ($this->posts as $post)
                            @php
                                $postT = $post->translation() ?? ($post->translations->firstWhere('language_id', $post->default_language_id) ?? $post->translations->first());
                                $slug = $postT?->slug;
                                $url = $slug ? route('frontend.post.show', ['slug' => $slug]) : '#';
                                $img = $post->featuredImage?->url() ?? "https://picsum.photos/seed/authorpost{$post->id}/800/480";
                                $postCat = $post->category;
                                $cName = html_entity_decode((string) ($postCat?->translate('name') ?? 'Article'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $postDate = $post->published_at?->format('M d, Y') ?? 'Recent';
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

                                        {{-- Top-Left Floating Dark Capsule Badge with Dot --}}
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
                                            {{-- Meta Line: Date · Reading Time --}}
                                            <div class="flex items-center gap-2 text-xs font-semibold">
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
                                                {{ html_entity_decode((string) ($postT->excerpt ?: 'In-depth perspectives, trends and practical applications from the author.'), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
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
                        {{ $this->posts->links('livewire.frontend.pagination', ['scrollTo' => '#author-posts']) }}
                    </div>
                @endif
            </div>

            {{-- ───── Right Sidebar (4 cols): Highlights, Popular Topics, OTHER AUTHORS ROW, Newsletter ───── --}}
            <aside class="lg:col-span-4 space-y-8 min-w-0">
                
                {{-- ───── Sidebar Widget 1: AUTHOR HIGHLIGHTS (Matching Mockup) ───── --}}
                <div class="sidebar-widget-card">
                    <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400 mb-5" style="font-family: 'Syne', sans-serif;">
                        Author Highlights
                    </h3>
                    <div class="space-y-4">
                        {{-- Highlight 1: Top Writer --}}
                        <div class="flex items-start gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 shadow-xs">
                                <i data-lucide="award" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                                    Top Writer
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-neutral-400 mt-0.5">
                                    In {{ $this->topCategoryName }}
                                </p>
                            </div>
                        </div>

                        {{-- Highlight 2: Featured Author --}}
                        <div class="flex items-start gap-3 pt-3 border-t border-slate-100 dark:border-white/5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 shadow-xs">
                                <i data-lucide="star" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                                    Featured Author
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-neutral-400 mt-0.5">
                                    Verified Contributor
                                </p>
                            </div>
                        </div>

                        {{-- Highlight 3: Most Read Article --}}
                        @if ($this->mostReadArticle)
                            @php
                                $mraT = $this->mostReadArticle->translation() ?? $this->mostReadArticle->translations->first();
                                $mraSlug = $mraT?->slug;
                                $mraUrl = $mraSlug ? route('frontend.post.show', ['slug' => $mraSlug]) : '#';
                            @endphp
                            <div class="flex items-start gap-3 pt-3 border-t border-slate-100 dark:border-white/5">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 shadow-xs">
                                    <i data-lucide="trending-up" class="h-4 w-4"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                                        Most Read Article
                                    </h4>
                                    <a href="{{ $mraUrl }}" class="text-[11px] text-slate-500 hover:text-indigo-600 dark:text-neutral-400 dark:hover:text-indigo-400 mt-0.5 block truncate transition">
                                        {{ $mraT?->title ?? 'Explore article' }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Highlight 4: Active Since --}}
                        <div class="flex items-start gap-3 pt-3 border-t border-slate-100 dark:border-white/5">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 shadow-xs">
                                <i data-lucide="shield-check" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                                    Active Since
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-neutral-400 mt-0.5">
                                    {{ $this->activeSince }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ───── Sidebar Widget 2: POPULAR TOPICS BY AUTHOR (Matching Mockup) ───── --}}
                @if ($this->authorTopics->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400" style="font-family: 'Syne', sans-serif;">
                                Popular Topics by {{ $author->name }}
                            </h3>
                        </div>
                        <div class="space-y-3">
                            @foreach ($this->authorTopics as $topCat)
                                @php
                                    $tcSlug = $topCat->translate('slug') ?? ('cat-' . $topCat->id);
                                    $tcName = html_entity_decode((string) ($topCat->translate('name') ?? ('#' . $topCat->id)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    $isSelTopic = $selectedCategory === $tcSlug;
                                @endphp
                                @if (!empty($tcName))
                                    <button type="button"
                                            wire:click="selectCategory('{{ $tcSlug }}')"
                                            class="w-full flex items-center justify-between py-1.5 text-xs font-semibold transition group border-b border-slate-100 dark:border-white/10 last:border-0 cursor-pointer text-left {{ $isSelTopic ? 'text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-700 dark:text-neutral-300 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                                        <span class="flex items-center gap-2.5">
                                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 text-[10px]">
                                                <i data-lucide="tag" class="h-3 w-3"></i>
                                            </span>
                                            <span>{{ $tcName }}</span>
                                        </span>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold transition {{ $isSelTopic ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-slate-100 text-slate-500 dark:bg-white/10 dark:text-neutral-300' }}">
                                            {{ $topCat->posts_count }}
                                        </span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ───── Sidebar Widget 3: OTHER AUTHORS ROW (Explicitly Requested by User) ───── --}}
                @if ($this->otherAuthors->isNotEmpty())
                    <div class="sidebar-widget-card">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="sidebar-widget-title text-xs font-bold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-400" style="font-family: 'Syne', sans-serif;">
                                Other Authors
                            </h3>
                            <a href="{{ route('frontend.blogs') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition flex items-center gap-1">
                                <span>View All</span>
                                <i data-lucide="arrow-right" class="h-3 w-3"></i>
                            </a>
                        </div>

                        <div class="space-y-3.5">
                            @foreach ($this->otherAuthors as $other)
                                <a href="{{ route('frontend.author', ['user' => $other->id]) }}"
                                   class="group flex items-center justify-between p-2 rounded-2xl hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full ring-2 ring-slate-100 dark:ring-white/10 bg-slate-100 dark:bg-slate-800">
                                            @if ($other->avatar)
                                                <img src="{{ $other->avatarUrl() }}" alt="{{ $other->name }}" class="h-full w-full object-cover">
                                            @else
                                                <span class="grid h-full w-full place-items-center bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 text-xs font-black">
                                                    {{ mb_substr($other->name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition" style="font-family: 'Syne', sans-serif;">
                                                {{ $other->name }}
                                            </h4>
                                            <p class="text-[10px] text-slate-500 dark:text-neutral-400 truncate mt-0.5">
                                                {{ $other->job_title ?: ($other->posts_count . ' ' . \Illuminate\Support\Str::plural('Article', $other->posts_count)) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="h-7 w-7 grid place-items-center rounded-full text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all shrink-0">
                                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ───── Sidebar Widget 4: STAY UPDATED NEWSLETTER ───── --}}
                <div class="relative overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-br from-blue-50/90 via-indigo-50/80 to-purple-50/90 p-6 shadow-xl shadow-indigo-500/5 backdrop-blur-xl dark:border-white/10 dark:from-slate-900/90 dark:via-indigo-950/50 dark:to-slate-900/90">
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
</div>
