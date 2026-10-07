@php
    $settings = app(\App\Services\SettingService::class);
    $siteName = (string) ($settings->get('footer.brand_text') ?? ($settings->get('site.name') ?? 'BLOGGER4U'));
    $footerIcon = (string) ($settings->get('footer.brand_icon') ?? 'sparkles');
    $logoType = (string) ($settings->get('footer.logo_type') ?? 'text');
    $logoUrl = (string) ($settings->get('footer.logo_url') ?? '');
    $logoHeight = (int) ($settings->get('footer.logo_height') ?? 32);

    // Email Address
    $email = (string) ($settings->get('footer.email') ?? ($settings->get('site.contact_email') ?? ($settings->get('company.email') ?? config('mail.from.address', 'contact@blogger4u.com'))));
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email = 'contact@blogger4u.com';
    }

    // Copyright
    $rawCopyright = (string) ($settings->get('footer.copyright') ?? '© {year} {siteName}. All Rights Reserved.');
    $copyright = str_replace(['{year}', '{siteName}'], [date('Y'), $siteName], $rawCopyright);

    // Social Links
    $facebookUrl = (string) ($settings->get('footer.social_facebook') ?? '');
    $twitterUrl = (string) ($settings->get('footer.social_twitter') ?? '');
    $instagramUrl = (string) ($settings->get('footer.social_instagram') ?? '');
    $linkedinUrl = (string) ($settings->get('footer.social_linkedin') ?? '');
    $youtubeUrl = (string) ($settings->get('footer.social_youtube') ?? '');
    $githubUrl = (string) ($settings->get('footer.social_github') ?? '');

    // Fallbacks if not set
    if (!$facebookUrl && !$twitterUrl && !$instagramUrl && !$linkedinUrl && !$githubUrl && !$youtubeUrl) {
        $twitterUrl = 'https://twitter.com';
        $githubUrl = 'https://github.com';
        $linkedinUrl = 'https://linkedin.com';
    }

    // Legal Pages
    $localeResolver = app(\App\Support\LocaleResolver::class);
    $currentLocale = $localeResolver->current();

    $legalPages = \App\Models\Page::query()
        ->where('status', \App\Enums\PageStatus::Published->value)
        ->orderBy('sort_order')
        ->limit(5)
        ->get();

    // News Continuous Ticker Posts
    $stickyPosts = \App\Models\Post::query()
        ->with(['translations', 'featuredImage'])
        ->where('status', \App\Enums\PostStatus::Published->value)
        ->whereNotNull('published_at')
        ->where('published_at', '<=', now())
        ->orderByDesc('published_at')
        ->limit(10)
        ->get()
        ->filter(function($sp) {
            $t = $sp->translation() ?? ($sp->translations->firstWhere('language_id', $sp->default_language_id) ?? $sp->translations->first());
            return $t && !empty($t->slug);
        });
@endphp

<footer class="border-t border-slate-200/80 bg-white text-slate-600 dark:border-white/10 dark:bg-[#070913] dark:text-neutral-400 transition-colors duration-200 {{ $stickyPosts->isNotEmpty() ? 'pb-24' : 'pb-10' }}">
    <div class="mx-auto max-w-[1360px] px-4 pt-12 pb-8 sm:px-6 lg:px-8 text-center flex flex-col items-center space-y-6">
        
        {{-- 1. Logo --}}
        <div>
            <a href="{{ route('frontend.home') }}" wire:navigate class="inline-flex items-center gap-2.5 group">
                @if ($logoType === 'image' && !empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="height: {{ $logoHeight }}px" class="w-auto object-contain">
                @else
                    <span class="grid h-9 w-9 place-items-center rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-md shadow-indigo-500/25 group-hover:scale-105 transition-transform duration-200">
                        <i data-lucide="{{ $footerIcon }}" class="h-4.5 w-4.5"></i>
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-lg font-black uppercase tracking-wider text-slate-900 dark:text-white" style="font-family: 'Syne', sans-serif;">
                            {{ $siteName }}
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-500/30">
                            TECH
                        </span>
                    </div>
                @endif
            </a>
        </div>

        {{-- 2. Legal Pages --}}
        <nav class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-semibold text-slate-600 dark:text-neutral-400">
            @forelse ($legalPages as $page)
                @php
                    $trans = $page->translation($currentLocale?->code) ?? $page->translation();
                    $pageTitle = $trans ? html_entity_decode((string) $trans->title, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
                @endphp
                @if ($trans && $pageTitle)
                    <a href="{{ route('frontend.page', ['slug' => $trans->slug]) }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        {{ $pageTitle }}
                    </a>
                @endif
            @empty
                <a href="{{ route('frontend.home') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">Privacy Policy</a>
                <a href="{{ route('frontend.home') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400">Terms of Service</a>
            @endforelse
        </nav>

        {{-- 3. Social Media Icons along with Email Icon --}}
        <div class="flex items-center justify-center gap-2.5 text-slate-500 dark:text-neutral-400">
            {{-- Email Icon Button --}}
            <a href="mailto:{{ $email }}" class="grid h-8.5 w-8.5 place-items-center rounded-full border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition shadow-2xs" title="Send Email ({{ $email }})">
                <i data-lucide="mail" class="h-4 w-4"></i>
            </a>

            @if ($facebookUrl)
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-8.5 w-8.5 place-items-center rounded-full border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition shadow-2xs" title="Facebook">
                    <i data-lucide="facebook" class="h-4 w-4"></i>
                </a>
            @endif
            @if ($twitterUrl)
                <a href="{{ $twitterUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-8.5 w-8.5 place-items-center rounded-full border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition shadow-2xs" title="X / Twitter">
                    <i data-lucide="twitter" class="h-4 w-4"></i>
                </a>
            @endif
            @if ($instagramUrl)
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-8.5 w-8.5 place-items-center rounded-full border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition shadow-2xs" title="Instagram">
                    <i data-lucide="instagram" class="h-4 w-4"></i>
                </a>
            @endif
            @if ($linkedinUrl)
                <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-8.5 w-8.5 place-items-center rounded-full border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition shadow-2xs" title="LinkedIn">
                    <i data-lucide="linkedin" class="h-4 w-4"></i>
                </a>
            @endif
            @if ($githubUrl)
                <a href="{{ $githubUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-8.5 w-8.5 place-items-center rounded-full border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition shadow-2xs" title="GitHub">
                    <i data-lucide="github" class="h-4 w-4"></i>
                </a>
            @endif
            @if ($youtubeUrl)
                <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener noreferrer" class="grid h-8.5 w-8.5 place-items-center rounded-full border border-slate-200/80 bg-slate-50 hover:bg-white hover:border-indigo-400 hover:text-indigo-600 hover:scale-105 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-white/[0.08] dark:hover:border-indigo-400 dark:hover:text-indigo-400 transition shadow-2xs" title="YouTube">
                    <i data-lucide="youtube" class="h-4 w-4"></i>
                </a>
            @endif
        </div>

        {{-- 4. Copyright Text --}}
        <div class="pt-1 text-xs text-slate-500 dark:text-neutral-500">
            {{ $copyright }}
        </div>

    </div>

    {{-- 5. News Continuous Ticker --}}
    @if ($stickyPosts->isNotEmpty())
        <div class="fixed inset-x-0 bottom-0 z-30 footer-ticker-glass bg-white/95 dark:bg-[#080B18]/95 backdrop-blur-xl px-4 py-2.5 shadow-2xl border-t border-slate-200/80 dark:border-white/10">
            <div class="mx-auto flex max-w-[1440px] items-center justify-between gap-4">
                {{-- Continuous Moving Marquee Track --}}
                <div class="relative flex-1 min-w-0 overflow-hidden ticker-mask py-1">
                    <div class="ticker-track gap-8">
                        {{-- Set 1 --}}
                        @foreach ($stickyPosts as $sp)
                            @php
                                $spT = $sp->translation() ?? ($sp->translations->firstWhere('language_id', $sp->default_language_id) ?? $sp->translations->first());
                                $spSlug = $spT?->slug;
                                $spUrl = route('frontend.post.show', ['slug' => $spSlug]);
                                $spImg = $sp->featuredImage?->url() ?? "https://picsum.photos/seed/np{$sp->id}/120/80";
                            @endphp
                            <a href="{{ $spUrl }}" wire:navigate class="group flex shrink-0 items-center gap-2.5 transition">
                                <div class="h-8 w-11 shrink-0 overflow-hidden rounded-lg bg-slate-100 dark:bg-[#1f212a] border border-slate-200/60 dark:border-[#262832]">
                                    <img src="{{ $spImg }}" alt="{{ $spT->title }}" class="h-full w-full object-cover group-hover:scale-105 transition" loading="lazy">
                                </div>
                                <span class="ticker-title max-w-[200px] sm:max-w-[260px] truncate text-xs font-semibold text-slate-800 group-hover:text-indigo-600 dark:text-neutral-300 dark:group-hover:text-indigo-400 transition">
                                    {{ $spT->title }}
                                </span>
                            </a>
                        @endforeach

                        {{-- Set 2 (Duplicated for seamless infinite movement) --}}
                        @foreach ($stickyPosts as $sp)
                            @php
                                $spT = $sp->translation() ?? ($sp->translations->firstWhere('language_id', $sp->default_language_id) ?? $sp->translations->first());
                                $spSlug = $spT?->slug;
                                $spUrl = route('frontend.post.show', ['slug' => $spSlug]);
                                $spImg = $sp->featuredImage?->url() ?? "https://picsum.photos/seed/np{$sp->id}/120/80";
                            @endphp
                            <a href="{{ $spUrl }}" wire:navigate aria-hidden="true" tabindex="-1" class="group flex shrink-0 items-center gap-2.5 transition">
                                <div class="h-8 w-11 shrink-0 overflow-hidden rounded-lg bg-slate-100 dark:bg-[#1f212a] border border-slate-200/60 dark:border-[#262832]">
                                    <img src="{{ $spImg }}" alt="{{ $spT->title }}" class="h-full w-full object-cover group-hover:scale-105 transition" loading="lazy">
                                </div>
                                <span class="ticker-title max-w-[200px] sm:max-w-[260px] truncate text-xs font-semibold text-slate-800 group-hover:text-indigo-600 dark:text-neutral-300 dark:group-hover:text-indigo-400 transition">
                                    {{ $spT->title }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Scroll to Top Button --}}
                <button type="button" x-on:click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="grid h-8.5 w-8.5 shrink-0 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 dark:border-[#262832] dark:bg-[#181920] dark:text-neutral-300 dark:hover:bg-[#22242e] shadow-xs cursor-pointer z-10"
                    title="Back to Top" aria-label="Back to Top">
                    <i data-lucide="chevron-up" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
    @endif
</footer>
