{{--
    Missing SEO details alert modal
    Displayed when an author tries to submit a post for review without filling required SEO fields.
--}}
<div x-data="{ open: @entangle('showMissingSeoModal') }"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        {{-- Background backdrop --}}
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             wire:click="closeMissingSeoModal"
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        {{-- Modal dialog --}}
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative inline-block w-full max-w-lg transform overflow-hidden rounded-2xl bg-white p-6 text-left align-bottom shadow-2xl transition-all sm:my-8 sm:align-middle dark:bg-slate-900 border border-slate-200 dark:border-slate-800">

            <div class="flex items-start gap-4">
                <div class="grid h-12 w-12 flex-shrink-0 place-items-center rounded-2xl bg-amber-100 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                    <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100" id="modal-title">
                        Missing Required Details
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Before submitting this article for editorial review, the following mandatory fields must be completed:
                    </p>

                    @if (!empty($missingSeoFields))
                        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50/70 p-3.5 dark:border-amber-900/40 dark:bg-amber-950/20">
                            <ul class="space-y-1.5 text-xs text-amber-900 dark:text-amber-200">
                                @foreach ($missingSeoFields as $field)
                                    <li class="flex items-center gap-2 font-medium">
                                        <i data-lucide="x-circle" class="h-3.5 w-3.5 text-rose-500 flex-shrink-0"></i>
                                        <span>{{ $field }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="button"
                        wire:click="closeMissingSeoModal"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700">
                    <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                    Complete Required Details
                </button>
            </div>
        </div>
    </div>
</div>
