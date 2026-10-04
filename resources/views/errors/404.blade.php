@extends('frontend.layouts.app')

@section('title', '404 — Page Not Found')

@section('content')
@php
    try {
        $quickCategories = \App\Models\Category::query()
            ->with('translations')
            ->limit(6)
            ->get();
    } catch (\Throwable) {
        $quickCategories = collect();
    }
@endphp

<div class="relative overflow-hidden py-16 sm:py-24 lg:py-32">
    {{-- Background Ambient Gradient Glows --}}
    <div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center">
        <div class="h-[360px] w-[360px] rounded-full bg-gradient-to-tr from-indigo-600/20 via-purple-600/20 to-pink-500/20 blur-3xl dark:from-indigo-500/15 dark:via-purple-500/15 dark:to-pink-500/10"></div>
        <div class="absolute -top-12 h-64 w-64 rounded-full bg-blue-500/10 blur-2xl dark:bg-blue-400/5"></div>
    </div>

    <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
        {{-- Status Badge --}}
        <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50/80 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-700 shadow-sm dark:border-indigo-900/50 dark:bg-indigo-950/40 dark:text-indigo-300">
            <span class="h-2 w-2 rounded-full bg-indigo-600 animate-pulse dark:bg-indigo-400"></span>
            Error 404
        </div>

        {{-- Big 404 Number --}}
        <div class="mt-4">
            <span class="text-7xl sm:text-8xl md:text-9xl font-black tracking-tight bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 bg-clip-text text-transparent select-none drop-shadow-sm dark:from-indigo-400 dark:via-purple-400 dark:to-pink-400">
                404
            </span>
        </div>

        {{-- Heading & Message --}}
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl dark:text-white">
            Oops! Page Not Found
        </h1>
        <p class="mx-auto mt-3 max-w-lg text-sm sm:text-base leading-relaxed text-slate-600 dark:text-slate-300">
            The article or page you are looking for might have been moved, renamed, or is temporarily unavailable.
        </p>

        {{-- Search Bar --}}
        <form action="{{ route('frontend.search') }}" method="GET" class="mx-auto mt-8 max-w-md">
            <div class="relative flex items-center shadow-sm">
                <span class="pointer-events-none absolute left-4 text-slate-400 dark:text-slate-500">
                    <i data-lucide="search" class="h-4 w-4"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    placeholder="Search articles, guides, topics..."
                    required
                    class="w-full rounded-2xl border border-slate-200 bg-white/90 py-3.5 pl-11 pr-28 text-sm text-slate-900 placeholder-slate-400 backdrop-blur-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-900/90 dark:text-white dark:placeholder-slate-500 dark:focus:border-indigo-400"
                >
                <button
                    type="submit"
                    class="absolute right-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                >
                    Search
                </button>
            </div>
        </form>

        {{-- Action Buttons --}}
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a
                href="{{ route('frontend.home') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
            >
                <i data-lucide="home" class="h-4 w-4"></i>
                Back to Homepage
            </a>

            <a
                href="{{ route('frontend.blogs') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white"
            >
                <i data-lucide="book-open" class="h-4 w-4"></i>
                Explore Articles
            </a>
        </div>

        {{-- Quick Categories --}}
        @if ($quickCategories->isNotEmpty())
            <div class="mt-12 border-t border-slate-200/80 pt-8 dark:border-slate-800/80">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Or explore by category
                </p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    @foreach ($quickCategories as $category)
                        @php
                            $catName = $category->translate('name') ?? $category->name ?? 'Category';
                            $catSlug = $category->slug ?? \Illuminate\Support\Str::slug($catName);
                        @endphp
                        <a
                            href="{{ route('frontend.category', ['slug' => $catSlug]) }}"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200/70 bg-white/60 px-3 py-1.5 text-xs font-medium text-slate-600 backdrop-blur-sm transition hover:border-indigo-300 hover:bg-indigo-50/60 hover:text-indigo-600 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-300 dark:hover:border-indigo-700 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                            {{ $catName }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
