@extends('frontend.layouts.app')

@section('title', '500 — Server Error')

@section('content')
<div class="relative overflow-hidden py-16 sm:py-24 lg:py-32">
    <div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center">
        <div class="h-[360px] w-[360px] rounded-full bg-gradient-to-tr from-rose-600/20 via-red-600/20 to-orange-500/20 blur-3xl dark:from-rose-500/15 dark:via-red-500/15 dark:to-orange-500/10"></div>
    </div>

    <div class="mx-auto max-w-2xl px-4 text-center sm:px-6 lg:px-8">
        <div class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50/80 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-rose-700 shadow-sm dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
            <span class="h-2 w-2 rounded-full bg-rose-600 animate-pulse dark:bg-rose-400"></span>
            Error 500
        </div>

        <div class="mt-4">
            <span class="text-7xl sm:text-8xl md:text-9xl font-black tracking-tight bg-gradient-to-r from-rose-600 via-red-500 to-amber-500 bg-clip-text text-transparent select-none drop-shadow-sm">
                500
            </span>
        </div>

        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl dark:text-white">
            Something Went Wrong
        </h1>
        <p class="mx-auto mt-3 max-w-lg text-sm sm:text-base leading-relaxed text-slate-600 dark:text-slate-300">
            Our servers encountered an unexpected issue while processing your request. Please try refreshing or check back in a moment.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a
                href="{{ route('frontend.home') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
            >
                <i data-lucide="home" class="h-4 w-4"></i>
                Back to Homepage
            </a>

            <button
                type="button"
                onclick="window.location.reload()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white"
            >
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                Refresh Page
            </button>
        </div>
    </div>
</div>
@endsection
