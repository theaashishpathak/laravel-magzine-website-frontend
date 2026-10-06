@php($title = 'Login')
@extends('layouts.guest')

@section('content')
    @php($companyName = $settings->get('site.name') ?: ($settings->get('company.name') ?: config('app.name', 'Blogger4U')))
    @php($appTagline = $settings->get('site.tagline') ?: ($settings->get('company.tagline') ?: 'AI-powered news & blogging platform'))
    @php($logoLight = $settings->get('branding.logo'))
    @php($logoDark = $settings->get('branding.logo_dark') ?: $logoLight)
    @php($logoLightUrl = $logoLight ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoLight) : null)
    @php($logoDarkUrl = $logoDark ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoDark) : null)

    <div class="w-full max-w-md space-y-4">
        <div class="rounded-3xl bg-white p-8 shadow-xl dark:bg-slate-900">
            <div class="text-center">
                @if ($logoLightUrl)
                    <img src="{{ $logoLightUrl }}" alt="{{ $companyName }}" class="mx-auto h-16 w-16 rounded-2xl object-contain {{ $logoDarkUrl && $logoDarkUrl !== $logoLightUrl ? 'dark:hidden' : '' }}">
                    @if ($logoDarkUrl && $logoDarkUrl !== $logoLightUrl)
                        <img src="{{ $logoDarkUrl }}" alt="{{ $companyName }}" class="mx-auto hidden h-16 w-16 rounded-2xl object-contain dark:block">
                    @endif
                @else
                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-indigo-600 text-2xl font-bold text-white shadow-lg shadow-indigo-500/25">
                        {{ strtoupper(substr($companyName, 0, 1)) }}
                    </div>
                @endif

                <h1 class="mt-6 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Sign in to {{ $companyName }}</h1>
                @if ($appTagline)
                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">{{ $appTagline }}</p>
                @endif
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Welcome back — sign in with your credentials to access your dashboard.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-4" id="login-form">
                @csrf

                @if ($errors->any())
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="mb-2 block text-sm font-medium">Email</label>
                    <input type="email" name="email" id="login-email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium">Password</label>
                    <x-password-input name="password" id="login-password" required autocomplete="current-password" />
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Forgot password?</a>
                </div>

                <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700 cursor-pointer transition">Enter Dashboard</button>

                <p class="text-center text-sm text-slate-500 dark:text-slate-400 pt-2">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition">Sign up</a>
                </p>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js"></script>
        <script>
            (function () {
                if (window.lucide) window.lucide.createIcons();
            })();
        </script>
    @endpush
@endsection
