@extends('frontend.layouts.app')

@section('title', '403 — Access Forbidden')

@section('content')
<div class="relative overflow-hidden py-16 sm:py-24 lg:py-32">
    <div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center">
        <div class="h-[360px] w-[360px] rounded-full bg-gradient-to-tr from-amber-600/20 via-rose-600/20 to-orange-500/20 blur-3xl dark:from-amber-500/15 dark:via-rose-500/15 dark:to-orange-500/10"></div>
    </div>

    <div class="mx-auto max-w-2xl px-4 text-center sm:px-6 lg:px-8">
        <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50/80 px-3.5 py-1 text-xs font-semibold uppercase tracking-wider text-amber-700 shadow-sm dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            <span class="h-2 w-2 rounded-full bg-amber-600 animate-pulse dark:bg-amber-400"></span>
            Error 403
        </div>

        <div class="mt-4">
            <span class="text-7xl sm:text-8xl md:text-9xl font-black tracking-tight bg-gradient-to-r from-amber-500 via-rose-500 to-red-500 bg-clip-text text-transparent select-none drop-shadow-sm">
                403
            </span>
        </div>

        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl dark:text-white">
            Access Restricted
        </h1>
        <p class="mx-auto mt-3 max-w-lg text-sm sm:text-base leading-relaxed text-slate-600 dark:text-slate-300">
            {{ !empty($exception) && $exception->getMessage() ? $exception->getMessage() : 'You do not have sufficient permissions to access this page or resource.' }}
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a
                href="{{ route('frontend.home') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
            >
                <i data-lucide="home" class="h-4 w-4"></i>
                Back to Homepage
            </a>

            @guest
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-white"
                >
                    <i data-lucide="log-in" class="h-4 w-4"></i>
                    Sign In
                </a>
            @endguest
        </div>
    </div>
</div>
@endsection
