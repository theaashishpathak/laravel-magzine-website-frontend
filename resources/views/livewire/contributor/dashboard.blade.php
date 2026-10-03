<div class="space-y-6">
    {{-- Welcome strip --}}
    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 p-6 text-white shadow-lg">
        <div>
            <div class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-widest text-white backdrop-blur-md">
                <i data-lucide="pen-tool" class="h-3 w-3"></i>
                <span>Contributor Portal</span>
            </div>
            <h1 class="mt-2 text-2xl font-black tracking-tight" style="font-family: 'Syne', sans-serif;">
                Welcome back, {{ $this->user->name }}
            </h1>
            <p class="mt-1 text-xs text-white/90">
                You have <strong>{{ $this->counts['draft'] }}</strong> draft(s), <strong>{{ $this->counts['pending'] }}</strong> submission(s) in review, and <strong>{{ $this->counts['changes_requested'] }}</strong> requiring changes.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @can('posts.create')
                <a href="{{ route('admin.posts.create') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-bold text-teal-800 shadow-md transition hover:bg-teal-50">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    New Draft
                </a>
            @endcan
            <a href="{{ route('admin.posts.index') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                <i data-lucide="files" class="h-4 w-4"></i>
                My Submissions
            </a>
            <a href="{{ route('author.profile') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                <i data-lucide="user" class="h-4 w-4"></i>
                Profile
            </a>
        </div>
    </div>

    {{-- Stat tiles --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $tiles = [
                ['label' => 'My Drafts', 'value' => $this->counts['draft'], 'icon' => 'file-text', 'color' => 'slate'],
                ['label' => 'Under Review', 'value' => $this->counts['pending'], 'icon' => 'hourglass', 'color' => 'amber'],
                ['label' => 'Needs Attention', 'value' => $this->counts['changes_requested'], 'icon' => 'alert-circle', 'color' => 'rose'],
                ['label' => 'Published Articles', 'value' => $this->counts['published'], 'icon' => 'check-circle', 'color' => 'emerald'],
            ];
        @endphp

        @foreach ($tiles as $tile)
            @php
                $colorClass = match ($tile['color']) {
                    'amber' => 'from-amber-500 to-orange-500',
                    'rose' => 'from-rose-500 to-pink-500',
                    'emerald' => 'from-emerald-500 to-teal-500',
                    default => 'from-slate-500 to-slate-600',
                };
            @endphp
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $tile['label'] }}</p>
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-gradient-to-br {{ $colorClass }} text-white shadow-2xs">
                        <i data-lucide="{{ $tile['icon'] }}" class="h-4 w-4"></i>
                    </span>
                </div>
                <p class="mt-3 text-3xl font-black text-slate-900 dark:text-slate-100">{{ $tile['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
        {{-- Recent Submissions --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">My Submissions & Drafts</h2>
                    <p class="text-xs text-slate-400">Track the progress of your written articles.</p>
                </div>
                <a href="{{ route('admin.posts.index') }}" wire:navigate class="text-xs font-semibold text-teal-600 hover:underline dark:text-teal-400">See all →</a>
            </div>

            @if ($this->recentSubmissions->isEmpty())
                <div class="rounded-xl border-2 border-dashed border-slate-200 p-8 text-center text-xs text-slate-400 dark:border-slate-700">
                    No articles submitted yet. Click "New Draft" to begin writing.
                </div>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($this->recentSubmissions as $post)
                        @php
                            $t = $post->translation();
                            $statusColor = match ($post->status->value) {
                                'published' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400',
                                'pending_review','in_review' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400',
                                'changes_requested' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400',
                                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400',
                                'approved','scheduled' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-400',
                                default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                            };
                        @endphp
                        <li class="flex items-center gap-3 py-3">
                            <a href="{{ route('admin.posts.edit', $post) }}" wire:navigate class="flex-1 truncate">
                                <p class="truncate text-sm font-semibold text-slate-800 hover:text-teal-600 dark:text-slate-100 dark:hover:text-teal-400">
                                    {{ $t?->title ?? '#'.$post->id }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $post->category?->translate('name') ?? 'Uncategorised' }} · Updated {{ $post->updated_at?->diffForHumans() }}
                                </p>
                            </a>
                            <span class="rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $statusColor }}">
                                {{ $post->status->label() }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Editorial Feedback & Notes --}}
        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-teal-100 text-teal-700 dark:bg-teal-950/50 dark:text-teal-400">
                            <i data-lucide="message-square" class="h-4 w-4"></i>
                        </span>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Editorial Feedback</h3>
                    </div>
                </div>

                @if ($this->editorialNotes->isEmpty())
                    <p class="py-4 text-center text-xs text-slate-400">No review feedback notes yet.</p>
                @else
                    <ul class="space-y-2.5">
                        @foreach ($this->editorialNotes as $note)
                            @php($t = $note->post?->translation())
                            <li class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 text-xs dark:border-slate-800 dark:bg-slate-800/40">
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ $note->post ? route('admin.posts.edit', $note->post) : '#' }}" wire:navigate
                                       class="font-semibold text-slate-800 hover:text-teal-600 dark:text-slate-100 dark:hover:text-teal-400 truncate">
                                        {{ $t?->title ?? '#'.$note->post?->id }}
                                    </a>
                                    <span class="shrink-0 text-[10px] text-slate-400">{{ $note->created_at?->diffForHumans(null, true) }}</span>
                                </div>
                                <p class="mt-1 text-slate-600 dark:text-slate-300">"{{ \Illuminate\Support\Str::limit($note->body, 140) }}"</p>
                                <p class="mt-1 text-[10px] font-medium text-slate-400">
                                    From: {{ $note->author?->name ?? 'Editorial Team' }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- AI Writing Assistant tile --}}
            <div class="rounded-2xl border border-cyan-200 bg-gradient-to-br from-cyan-50 to-teal-50 p-5 shadow-xs dark:border-cyan-500/30 dark:from-cyan-500/10 dark:to-teal-500/10">
                <div class="mb-3 flex items-center gap-2">
                    <span class="grid h-7 w-7 place-items-center rounded-lg bg-gradient-to-br from-cyan-500 to-teal-500 text-white">
                        <i data-lucide="sparkles" class="h-3.5 w-3.5"></i>
                    </span>
                    <h3 class="text-sm font-bold text-cyan-950 dark:text-cyan-200">AI Assistant Calls</h3>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center">
                    <div class="rounded-xl bg-white/70 p-2.5 dark:bg-slate-900/40">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-700/80 dark:text-cyan-300">Used This Month</p>
                        <p class="text-2xl font-black text-cyan-950 dark:text-cyan-100">{{ $this->aiUsageThisMonth['calls'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white/70 p-2.5 dark:bg-slate-900/40">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-700/80 dark:text-cyan-300">Tokens</p>
                        <p class="text-2xl font-black text-cyan-950 dark:text-cyan-100">{{ number_format($this->aiUsageThisMonth['tokens']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
