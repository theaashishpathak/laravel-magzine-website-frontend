<div
    x-data="{
        isOpen: false,
        nature: 'danger', // 'danger' | 'warning' | 'info' | 'success'
        title: '',
        message: '',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        badge: '',
        onConfirmCallback: null,
        onCancelCallback: null,

        open(payload) {
            this.nature = payload.nature || this.detectNature(payload.message || '');
            this.title = payload.title || this.defaultTitle(this.nature);
            this.message = payload.message || 'Are you sure you want to proceed?';
            this.confirmText = payload.confirmText || this.defaultConfirmText(this.nature);
            this.cancelText = payload.cancelText || 'Cancel';
            this.badge = payload.badge || this.defaultBadge(this.nature);
            this.onConfirmCallback = typeof payload.onConfirm === 'function' ? payload.onConfirm : null;
            this.onCancelCallback = typeof payload.onCancel === 'function' ? payload.onCancel : null;
            this.isOpen = true;

            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons({ root: this.$el });
                }
            });
        },

        detectNature(text) {
            const lower = (text || '').toLowerCase();
            if (lower.includes('delete') || lower.includes('trash') || lower.includes('remove') || lower.includes('destroy') || lower.includes('wipe') || lower.includes('purge') || lower.includes('permanently')) {
                return 'danger';
            }
            if (lower.includes('archive') || lower.includes('reset') || lower.includes('clear') || lower.includes('warning') || lower.includes('revert') || lower.includes('unsubscribe') || lower.includes('merge')) {
                return 'warning';
            }
            if (lower.includes('promote') || lower.includes('publish') || lower.includes('restore') || lower.includes('toggle') || lower.includes('info')) {
                return 'info';
            }
            return 'danger';
        },

        defaultTitle(nature) {
            switch (nature) {
                case 'danger': return 'Delete Confirmation';
                case 'warning': return 'Warning & Confirmation';
                case 'info': return 'Confirm Action';
                case 'success': return 'Confirm Success Action';
                default: return 'Please Confirm';
            }
        },

        defaultConfirmText(nature) {
            switch (nature) {
                case 'danger': return 'Yes, Delete';
                case 'warning': return 'Yes, Proceed';
                case 'info': return 'Confirm';
                case 'success': return 'Accept';
                default: return 'Confirm';
            }
        },

        defaultBadge(nature) {
            switch (nature) {
                case 'danger': return 'Destructive Action';
                case 'warning': return 'Attention Required';
                case 'info': return 'Notice';
                case 'success': return 'Verification';
                default: return 'Confirmation';
            }
        },

        confirm() {
            const cb = this.onConfirmCallback;
            this.close(false);
            if (cb) {
                try { cb(); } catch (err) { console.error('Confirm callback error:', err); }
            }
        },

        cancel() {
            const cb = this.onCancelCallback;
            this.close(false);
            if (cb) {
                try { cb(); } catch (err) { console.error('Cancel callback error:', err); }
            }
        },

        close(triggerCancel = true) {
            if (triggerCancel && this.onCancelCallback) {
                try { this.onCancelCallback(); } catch (err) {}
            }
            this.isOpen = false;
            this.onConfirmCallback = null;
            this.onCancelCallback = null;
        }
    }"
    x-cloak
    x-show="isOpen"
    @keydown.escape.window="if (isOpen) cancel()"
    @system-confirm.window="open($event.detail)"
    @confirm-bulk-delete-alert.window="
        open({
            nature: 'danger',
            title: 'Delete Selected Posts',
            message: 'Are you sure you want to delete ' + ($event.detail.count || 'the selected') + ' post(s)? This will move them to trash.',
            confirmText: 'Delete Selected',
            badge: 'Bulk Destructive Action',
            onConfirm: () => {
                $dispatch('confirm-bulk-delete');
            }
        })
    "
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="system-confirm-title"
>
    {{-- Backdrop with blur and subtle darken --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity dark:bg-slate-950/80"
        @click="cancel()"
    ></div>

    {{-- Dialog Card --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl transition-all dark:border-slate-800 dark:bg-slate-900"
        :class="{
            'ring-1 ring-rose-500/20': nature === 'danger',
            'ring-1 ring-amber-500/20': nature === 'warning',
            'ring-1 ring-indigo-500/20': nature === 'info',
            'ring-1 ring-emerald-500/20': nature === 'success'
        }"
    >
        {{-- Top Accent Nature Line --}}
        <div
            class="absolute inset-x-0 top-0 h-1"
            :class="{
                'bg-gradient-to-r from-rose-500 via-red-500 to-rose-600': nature === 'danger',
                'bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600': nature === 'warning',
                'bg-gradient-to-r from-indigo-500 via-blue-500 to-indigo-600': nature === 'info',
                'bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600': nature === 'success'
            }"
        ></div>

        {{-- Header Section with Nature Icon Badge and Close Button --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                {{-- Nature Icon Container --}}
                <div
                    class="grid h-12 w-12 shrink-0 place-items-center rounded-xl shadow-sm transition-colors"
                    :class="{
                        'bg-rose-500/10 text-rose-600 ring-1 ring-rose-500/20 dark:bg-rose-500/20 dark:text-rose-400': nature === 'danger',
                        'bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20 dark:bg-amber-500/20 dark:text-amber-400': nature === 'warning',
                        'bg-indigo-500/10 text-indigo-600 ring-1 ring-indigo-500/20 dark:bg-indigo-500/20 dark:text-indigo-400': nature === 'info',
                        'bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-400': nature === 'success'
                    }"
                >
                    <template x-if="nature === 'danger'">
                        <i data-lucide="trash-2" class="h-6 w-6"></i>
                    </template>
                    <template x-if="nature === 'warning'">
                        <i data-lucide="alert-triangle" class="h-6 w-6"></i>
                    </template>
                    <template x-if="nature === 'info'">
                        <i data-lucide="info" class="h-6 w-6"></i>
                    </template>
                    <template x-if="nature === 'success'">
                        <i data-lucide="check-circle" class="h-6 w-6"></i>
                    </template>
                </div>

                {{-- Nature Badge & Subtitle --}}
                <div>
                    <span
                        class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[11px] font-semibold tracking-wide uppercase"
                        :class="{
                            'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400': nature === 'danger',
                            'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400': nature === 'warning',
                            'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400': nature === 'info',
                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400': nature === 'success'
                        }"
                        x-text="badge"
                    ></span>
                </div>
            </div>

            {{-- Close Button --}}
            <button
                type="button"
                @click="cancel()"
                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                aria-label="Close dialog"
            >
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        {{-- Content Body --}}
        <div class="mt-4">
            <h3
                id="system-confirm-title"
                class="text-lg font-bold tracking-tight text-slate-900 dark:text-slate-100"
                x-text="title"
            ></h3>

            <p
                class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300"
                x-text="message"
            ></p>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button
                type="button"
                @click="cancel()"
                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 sm:w-auto"
                x-text="cancelText"
            ></button>

            <button
                type="button"
                @click="confirm()"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto"
                :class="{
                    'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500 shadow-rose-600/25': nature === 'danger',
                    'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500 shadow-amber-600/25': nature === 'warning',
                    'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 shadow-indigo-600/25': nature === 'info',
                    'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 shadow-emerald-600/25': nature === 'success'
                }"
            >
                <template x-if="nature === 'danger'">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                </template>
                <template x-if="nature === 'warning'">
                    <i data-lucide="alert-triangle" class="h-4 w-4"></i>
                </template>
                <template x-if="nature === 'info'">
                    <i data-lucide="check" class="h-4 w-4"></i>
                </template>
                <template x-if="nature === 'success'">
                    <i data-lucide="check" class="h-4 w-4"></i>
                </template>
                <span x-text="confirmText"></span>
            </button>
        </div>
    </div>
</div>
