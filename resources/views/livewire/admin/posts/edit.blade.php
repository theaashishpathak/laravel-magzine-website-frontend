<div class="space-y-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.posts.index') }}" wire:navigate class="hover:text-indigo-600">Posts</a>
        <i data-lucide="chevron-right" class="h-3 w-3"></i>
        <span class="font-semibold text-slate-700 dark:text-slate-200">
            Edit — {{ $title !== '' ? $title : ('#'.$post->id) }}
        </span>

        @php($status = $post->status)
        @php($statusColor = match ($status->value) {
            'published' => 'bg-emerald-100 text-emerald-700',
            'draft' => 'bg-slate-100 text-slate-700',
            'pending_review','in_review' => 'bg-amber-100 text-amber-700',
            'changes_requested' => 'bg-orange-100 text-orange-700',
            'approved' => 'bg-indigo-100 text-indigo-700',
            'scheduled' => 'bg-sky-100 text-sky-700',
            'rejected' => 'bg-rose-100 text-rose-700',
            'archived' => 'bg-zinc-100 text-zinc-700',
            'unpublished' => 'bg-stone-100 text-stone-700',
            default => 'bg-slate-100 text-slate-700',
        })
        <span class="ml-2 rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusColor }}">
            {{ $status->label() }}
        </span>
    </nav>

    {{-- AI Assistant launch bar --}}
    @canany(['ai.use_writer', 'ai.use_seo', 'ai.use_rewrite'])
        <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-violet-200 bg-gradient-to-r from-violet-50 to-indigo-50 px-4 py-3 dark:border-violet-500/30 dark:from-violet-500/10 dark:to-indigo-500/10">
            <span class="grid h-8 w-8 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-500 text-white">
                <i data-lucide="sparkles" class="h-4 w-4"></i>
            </span>
            <p class="mr-auto text-sm font-semibold text-violet-900 dark:text-violet-200">AI Assistant</p>

            @can('ai.use_writer')
                <button type="button" wire:click="openAIAssistant('article')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-violet-700">
                    <i data-lucide="file-text" class="h-3.5 w-3.5"></i>
                    Generate Article
                </button>
            @endcan
            @can('ai.use_seo')
                <button type="button" wire:click="openAIAssistant('seo')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                    <i data-lucide="search" class="h-3.5 w-3.5"></i>
                    Generate SEO
                </button>
            @endcan
            @can('ai.use_rewrite')
                <button type="button" wire:click="openAIAssistant('rewrite')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-orange-700">
                    <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                    Rewrite
                </button>
            @endcan
        </div>
    @endcanany

    <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
        {{-- Form column --}}
        <div class="space-y-6">
            @include('livewire.admin.posts._translation-tabs')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                @include('livewire.admin.posts._form-fields')
            </div>

            @include('livewire.admin.posts._seo-panel')
        </div>

        {{-- Sidebar — actions + workflow --}}
        <aside class="space-y-4">
            {{-- Author / Creator Info card for reviewer / admin --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-100 bg-slate-50/60 px-5 py-3 dark:border-slate-800 dark:bg-slate-950/40">
                    <h3 class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-slate-500">
                        <i data-lucide="user-check" class="h-3.5 w-3.5 text-indigo-500"></i>
                        Post Author
                    </h3>
                </div>
                <div class="space-y-3.5 p-4">
                    @php($author = $post->author)
                    @if ($author)
                        <div class="flex items-center gap-3">
                            <div class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 font-bold text-white shadow-sm">
                                {{ strtoupper(substr($author->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800 dark:text-slate-100">
                                    {{ $author->name }}
                                </p>
                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ $author->email }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2 border-t border-slate-100 pt-3 text-xs dark:border-slate-800">
                            <div class="flex items-center justify-between text-slate-500">
                                <span>Role</span>
                                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                                    {{ $author->roles->first()?->name ?? 'Author' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500">
                                <span>Submitted</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $post->created_at?->format('M d, Y · h:i A') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-xs italic text-slate-400">No author assigned to this post.</p>
                    @endif

                    @can('posts.publish')
                        {{-- Admins / Editors can reassign the author --}}
                        <div class="border-t border-slate-100 pt-3 dark:border-slate-800">
                            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                Reassign Author
                            </label>
                            <select wire:model="authorId"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                                @foreach ($this->authors as $eligibleAuthor)
                                    <option value="{{ $eligibleAuthor->id }}">
                                        {{ $eligibleAuthor->name }} ({{ $eligibleAuthor->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endcan
                </div>
            </div>

            @include('livewire.admin.posts._featured-image-card')

            {{-- Unified Publishing & Editorial Workflow Panel --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-5 py-3 dark:border-slate-800 dark:bg-slate-950/40">
                    <h3 class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.16em] text-slate-500">
                        <i data-lucide="send-horizontal" class="h-3.5 w-3.5 text-indigo-500"></i>
                        Publish &amp; Workflow
                    </h3>
                    <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $statusColor }}">
                        {{ $status->label() }}
                    </span>
                </div>

                <div class="space-y-3.5 p-4">
                    {{-- 1. Primary Publishing & Save Actions --}}
                    @if ($post->status->value === 'published')
                        {{-- Live post: Save Changes is the primary action --}}
                        <button type="button" wire:click="save"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:from-indigo-700 hover:to-indigo-600 hover:shadow disabled:cursor-not-allowed disabled:opacity-60">
                            <i data-lucide="save" class="h-4 w-4" wire:loading.remove wire:target="save"></i>
                            <i data-lucide="loader-2" class="h-4 w-4 animate-spin" wire:loading wire:target="save"></i>
                            <span wire:loading.remove wire:target="save">Save Changes</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    @elseif ($this->canPublish)
                        {{-- Direct publish for Super Admin / Admin / Editor --}}
                        <button type="button" wire:click="publish"
                                wire:loading.attr="disabled"
                                wire:target="publish"
                                class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:from-emerald-700 hover:to-emerald-600 hover:shadow disabled:cursor-not-allowed disabled:opacity-60">
                            <i data-lucide="zap" class="h-4 w-4" wire:loading.remove wire:target="publish"></i>
                            <i data-lucide="loader-2" class="h-4 w-4 animate-spin" wire:loading wire:target="publish"></i>
                            <span wire:loading.remove wire:target="publish">{{ in_array($post->status->value, ['pending_review', 'in_review'], true) ? 'Approve & Publish' : 'Publish Now' }}</span>
                            <span wire:loading wire:target="publish">Publishing…</span>
                        </button>

                        <button type="button" wire:click="save"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                            <i data-lucide="save" class="h-3.5 w-3.5" wire:loading.remove wire:target="save"></i>
                            <i data-lucide="loader-2" class="h-3.5 w-3.5 animate-spin" wire:loading wire:target="save"></i>
                            <span wire:loading.remove wire:target="save">Save Changes</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    @elseif ($this->canSubmitForReview)
                        {{-- Authors without direct publishing rights --}}
                        <button type="button" wire:click="submitForReview"
                                wire:loading.attr="disabled"
                                wire:target="submitForReview"
                                class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:from-indigo-700 hover:to-indigo-600 hover:shadow disabled:cursor-not-allowed disabled:opacity-60">
                            <i data-lucide="send-horizontal" class="h-4 w-4 transition group-hover:translate-x-0.5" wire:loading.remove wire:target="submitForReview"></i>
                            <i data-lucide="loader-2" class="h-4 w-4 animate-spin" wire:loading wire:target="submitForReview"></i>
                            <span wire:loading.remove wire:target="submitForReview">Submit for Review</span>
                            <span wire:loading wire:target="submitForReview">Submitting…</span>
                        </button>

                        <button type="button" wire:click="save"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                            <i data-lucide="save" class="h-3.5 w-3.5" wire:loading.remove wire:target="save"></i>
                            <i data-lucide="loader-2" class="h-3.5 w-3.5 animate-spin" wire:loading wire:target="save"></i>
                            <span wire:loading.remove wire:target="save">Save Draft</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    @else
                        {{-- Standard fallback save --}}
                        <button type="button" wire:click="save"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <i data-lucide="save" class="h-4 w-4" wire:loading.remove wire:target="save"></i>
                            <i data-lucide="loader-2" class="h-4 w-4 animate-spin" wire:loading wire:target="save"></i>
                            <span wire:loading.remove wire:target="save">Save Changes</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    @endif

                    {{-- 2. Editorial Review Decisions (Only shown during active review flow to authorized reviewers) --}}
                    @if (in_array($post->status->value, ['pending_review', 'in_review', 'changes_requested'], true) && ($this->canApprove || $this->canReject || $this->canRequestChanges))
                        <div class="space-y-3 rounded-xl border border-amber-200/70 bg-amber-50/50 p-3.5 dark:border-amber-500/20 dark:bg-amber-500/5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">
                                    Editorial Review
                                </span>
                                <span class="text-[10px] text-amber-600 dark:text-amber-400">Reviewer options</span>
                            </div>

                            <textarea
                                wire:model="editorialNote"
                                rows="2"
                                placeholder="Feedback / note for author (required for reject & request changes)…"
                                class="w-full rounded-lg border border-amber-200 bg-white px-3 py-2 text-xs outline-none focus:border-amber-400 dark:border-amber-500/30 dark:bg-slate-950 dark:text-slate-100"
                            ></textarea>

                            <div class="grid grid-cols-2 gap-2">
                                @if ($this->canApprove && $post->status->value !== 'approved')
                                    <button type="button" wire:click="approve"
                                            wire:loading.attr="disabled" wire:target="approve"
                                            class="col-span-2 inline-flex items-center justify-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 dark:border-indigo-500/30 dark:bg-indigo-950/40 dark:text-indigo-300 cursor-pointer">
                                        <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                        Approve (Mark Approved)
                                    </button>
                                @endif

                                @if ($this->canRequestChanges)
                                    <button type="button" wire:click="requestChanges"
                                            wire:loading.attr="disabled" wire:target="requestChanges"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-orange-200 bg-orange-50 px-2.5 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-100 dark:border-orange-500/30 dark:bg-orange-950/40 dark:text-orange-300 cursor-pointer">
                                        <i data-lucide="message-square" class="h-3.5 w-3.5"></i>
                                        Request Changes
                                    </button>
                                @endif

                                @if ($this->canReject)
                                    <button type="button" wire:click="reject"
                                            wire:loading.attr="disabled" wire:target="reject"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-950/40 dark:text-rose-300 cursor-pointer">
                                        <i data-lucide="x-circle" class="h-3.5 w-3.5"></i>
                                        Reject
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- 3. Post Management & Lifecycle (Unpublish / Archive) --}}
                    @if (($this->canPublish && $post->status->value === 'published') || ($this->canArchive && $post->status->value !== 'archived'))
                        <div class="flex items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                            @if ($this->canPublish && $post->status->value === 'published')
                                <button type="button" wire:click="unpublish"
                                        wire:loading.attr="disabled" wire:target="unpublish"
                                        class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 cursor-pointer">
                                    <i data-lucide="eye-off" class="h-3.5 w-3.5"></i>
                                    Unpublish
                                </button>
                            @endif

                            @if ($this->canArchive && $post->status->value !== 'archived')
                                <button type="button" wire:click="archive"
                                        wire:loading.attr="disabled" wire:target="archive"
                                        class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-rose-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 cursor-pointer">
                                    <i data-lucide="archive" class="h-3.5 w-3.5"></i>
                                    Archive
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Revision badge --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Revisions</span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        {{ $this->revisionCount }}
                    </span>
                </div>
                <p class="mt-2 text-xs text-slate-500">A new revision is recorded automatically every time you Save.</p>
                @can('editorial.revisions')
                    <a href="{{ route('admin.posts.revisions', $post) }}" wire:navigate
                       class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <i data-lucide="history" class="h-3.5 w-3.5"></i> View history
                    </a>
                @endcan
            </div>

            {{-- Editorial notes thread (read-only) --}}
            @if ($this->editorialNotes->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Editorial Notes</h3>
                    <div class="space-y-3">
                        @foreach ($this->editorialNotes as $note)
                            @php($typeColor = match ($note->type) {
                                \App\Models\EditorialNote::TYPE_APPROVE => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                \App\Models\EditorialNote::TYPE_REJECT => 'border-rose-200 bg-rose-50 text-rose-800',
                                \App\Models\EditorialNote::TYPE_REQUEST_CHANGES => 'border-orange-200 bg-orange-50 text-orange-800',
                                \App\Models\EditorialNote::TYPE_AI_SUGGESTION => 'border-violet-200 bg-violet-50 text-violet-800',
                                default => 'border-slate-200 bg-slate-50 text-slate-700',
                            })
                            <div class="rounded-lg border px-3 py-2 text-xs {{ $typeColor }}">
                                <div class="mb-1 flex items-center justify-between text-[10px] uppercase tracking-wider">
                                    <span class="font-bold">{{ str($note->type)->replace('_', ' ')->title() }}</span>
                                    <span>{{ $note->created_at?->diffForHumans() }}</span>
                                </div>
                                <p>{{ $note->body }}</p>
                                <p class="mt-1 text-[10px] opacity-75">— {{ $note->author?->name ?? 'system' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </aside>
    </div>

    {{-- AI Assistant slide-over (opens via openAIAssistant() dispatched event) --}}
    <livewire:admin.posts.ai-assistant-drawer />

    {{-- Media picker modal (opens via openFeaturedImagePicker() dispatched event) --}}
    <livewire:admin.media.media-picker-modal />

    {{-- Missing SEO details alert modal --}}
    @include('livewire.admin.posts._missing-seo-modal')
</div>
