<div>
    <x-admin.page-header
        eyebrow="Audit"
        icon="mail"
        title="Email Delivery Logs"
        description="Monitor outgoing email deliveries, verify SMTP handshake status, and inspect errors or delivery failures.">
        <x-slot:actions>
            <button type="button" wire:click="purgeOldLogs"
                    wire:confirm="Are you sure you want to delete email delivery logs older than 30 days?"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                <i data-lucide="trash-2" class="h-3.5 w-3.5 text-rose-500"></i>
                Purge >30d
            </button>
            <button type="button" wire:click="clearFilters"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                <i data-lucide="filter-x" class="h-3.5 w-3.5"></i>
                Clear filters
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    {{-- Metric Stat Cards --}}
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Outgoing</div>
            <div class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($totalCount) }}</div>
            <div class="mt-1 text-[11px] text-slate-400">All recorded emails</div>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 shadow-sm dark:border-emerald-950 dark:bg-emerald-950/20">
            <div class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Delivered</div>
            <div class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ number_format($deliveredCount) }}</div>
            <div class="mt-1 text-[11px] text-emerald-600/70 dark:text-emerald-500">Successfully sent</div>
        </div>

        <div class="rounded-xl border border-rose-200 bg-rose-50/40 p-4 shadow-sm dark:border-rose-950 dark:bg-rose-950/20">
            <div class="text-xs font-medium text-rose-700 dark:text-rose-400">Failed / Rejected</div>
            <div class="mt-2 text-2xl font-bold text-rose-700 dark:text-rose-400">{{ number_format($failedCount) }}</div>
            <div class="mt-1 text-[11px] text-rose-600/70 dark:text-rose-500">Errors or timeouts</div>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4 shadow-sm dark:border-amber-950 dark:bg-amber-950/20">
            <div class="text-xs font-medium text-amber-700 dark:text-amber-400">Pending</div>
            <div class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-400">{{ number_format($pendingCount) }}</div>
            <div class="mt-1 text-[11px] text-amber-600/70 dark:text-amber-500">In queue / sending</div>
        </div>

        <div class="rounded-xl border border-indigo-200 bg-indigo-50/40 p-4 shadow-sm dark:border-indigo-950 dark:bg-indigo-950/20">
            <div class="text-xs font-medium text-indigo-700 dark:text-indigo-400">Delivery Rate</div>
            <div class="mt-2 text-2xl font-bold text-indigo-700 dark:text-indigo-400">{{ $successRate }}%</div>
            <div class="mt-1 text-[11px] text-indigo-600/70 dark:text-indigo-500">Success percentage</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <x-admin.section title="Filters" description="Search by recipient, subject, status, or date range." class="mb-6">
        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
            {{-- Search --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Search</label>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Recipient, subject, error..."
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
            </div>

            {{-- Status --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Status</label>
                <select wire:model.live="status"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">All Statuses</option>
                    <option value="delivered">Delivered</option>
                    <option value="failed">Failed</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            {{-- Mailer --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Mailer</label>
                <select wire:model.live="mailer"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">All Mailers</option>
                    <option value="smtp">SMTP</option>
                    <option value="log">Log</option>
                </select>
            </div>

            {{-- From Date --}}
            <div>
                <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    <i data-lucide="calendar" class="h-3.5 w-3.5 text-indigo-500"></i>
                    <span>From Date</span>
                </label>
                <div class="relative">
                    <input wire:model.live="from" type="date"
                           onclick="this.showPicker && this.showPicker()"
                           title="Select starting date"
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 cursor-pointer">
                </div>
            </div>

            {{-- To Date --}}
            <div>
                <label class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    <i data-lucide="calendar" class="h-3.5 w-3.5 text-indigo-500"></i>
                    <span>To Date</span>
                </label>
                <div class="relative">
                    <input wire:model.live="to" type="date"
                           onclick="this.showPicker && this.showPicker()"
                           title="Select ending date"
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 cursor-pointer">
                </div>
            </div>
        </div>

        {{-- Quick Date Range Presets --}}
        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-800 text-xs">
            <span class="font-medium text-slate-400 mr-1">Quick date range:</span>
            <button type="button" wire:click="setDatePreset('today')" class="rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-600 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500/40 dark:hover:text-indigo-300 transition cursor-pointer">Today</button>
            <button type="button" wire:click="setDatePreset('yesterday')" class="rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-600 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500/40 dark:hover:text-indigo-300 transition cursor-pointer">Yesterday</button>
            <button type="button" wire:click="setDatePreset('7days')" class="rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-600 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500/40 dark:hover:text-indigo-300 transition cursor-pointer">Last 7 Days</button>
            <button type="button" wire:click="setDatePreset('30days')" class="rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-600 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500/40 dark:hover:text-indigo-300 transition cursor-pointer">Last 30 Days</button>
            <button type="button" wire:click="setDatePreset('this_month')" class="rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-600 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-indigo-500/40 dark:hover:text-indigo-300 transition cursor-pointer">This Month</button>
            @if ($from || $to)
                <button type="button" wire:click="setDatePreset('clear')" class="ml-auto inline-flex items-center gap-1 rounded-md border border-rose-200 bg-rose-50 px-2.5 py-1 font-semibold text-rose-600 hover:bg-rose-100 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-400 transition cursor-pointer">
                    <i data-lucide="x" class="h-3 w-3"></i>
                    <span>Clear dates</span>
                </button>
            @endif
        </div>
    </x-admin.section>

    {{-- Main Logs Table --}}
    <x-admin.table-shell title="Delivery Log History" description="Chronological list of all attempted email transmissions.">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
            <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:bg-slate-950/50 dark:text-slate-300">
                <tr>
                    <th class="px-4 py-3">SL</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Recipient</th>
                    <th class="px-4 py-3">Subject</th>
                    <th class="px-4 py-3">Mailer</th>
                    <th class="px-4 py-3">Sent At</th>
                    <th class="px-4 py-3">Error / Note</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($logs as $i => $log)
                    @php
                        $badgeClass = match ($log->status) {
                            'delivered' => 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
                            'failed' => 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
                            'pending' => 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
                            default => 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300',
                        };

                        $statusIcon = match ($log->status) {
                            'delivered' => 'check-circle',
                            'failed' => 'alert-triangle',
                            'pending' => 'clock',
                            default => 'info',
                        };
                    @endphp
                    <tr wire:key="email-log-{{ $log->id }}" class="align-top transition hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                        <td class="px-4 py-3 text-slate-400">{{ $logs->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badgeClass }}">
                                <i data-lucide="{{ $statusIcon }}" class="h-3 w-3"></i>
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $log->recipient_email }}</div>
                            @if($log->recipient_name)
                                <div class="text-xs text-slate-500">{{ $log->recipient_name }}</div>
                            @endif
                        </td>
                        <td class="max-w-xs px-4 py-3">
                            <div class="truncate font-medium text-slate-800 dark:text-slate-200" title="{{ $log->subject }}">
                                {{ $log->subject }}
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs uppercase text-slate-600 dark:text-slate-400">
                            {{ $log->mailer }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500">
                            <div>{{ $log->sent_at ? $log->sent_at->format('M d, Y H:i:s') : $log->created_at->format('M d, Y H:i:s') }}</div>
                            <div class="text-[11px] text-slate-400">{{ ($log->sent_at ?? $log->created_at)->diffForHumans() }}</div>
                        </td>
                        <td class="max-w-xs px-4 py-3 text-xs">
                            @if($log->error_message)
                                <span class="line-clamp-2 text-rose-600 dark:text-rose-400" title="{{ $log->error_message }}">
                                    {{ $log->error_message }}
                                </span>
                            @else
                                <span class="text-slate-400">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" wire:click="inspect({{ $log->id }})"
                                        class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                    Inspect
                                </button>
                                <button type="button" wire:click="deleteLog({{ $log->id }})"
                                        wire:confirm="Delete this email log entry?"
                                        class="rounded-lg p-1 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/30">
                                    <i data-lucide="trash" class="h-3.5 w-3.5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="inbox" class="h-10 w-10 text-slate-300 dark:text-slate-600"></i>
                                <p class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-300">No email delivery logs found</p>
                                <p class="text-xs text-slate-400">Emails dispatched by the application will be recorded here automatically.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <div class="border-t border-slate-200 p-4 dark:border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </x-admin.table-shell>

    {{-- Inspection Modal / Slide-Over --}}
    @if ($selectedLog)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
             wire:keydown.escape.window="closeInspect">
            <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-xl dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Email Delivery Details</h3>
                        <p class="text-xs text-slate-500">Log Record #{{ $selectedLog->id }}</p>
                    </div>
                    <button type="button" wire:click="closeInspect"
                            class="rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-950">
                            <div class="text-xs font-semibold text-slate-500">Status</div>
                            <div class="mt-1 font-semibold">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ match($selectedLog->status) {
                                    'delivered' => 'bg-emerald-100 text-emerald-800',
                                    'failed' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-amber-100 text-amber-800'
                                } }}">
                                    {{ ucfirst($selectedLog->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-950">
                            <div class="text-xs font-semibold text-slate-500">Mailer Driver</div>
                            <div class="mt-1 font-mono text-sm text-slate-800 dark:text-slate-200 uppercase">
                                {{ $selectedLog->mailer }}
                            </div>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-950">
                            <div class="text-xs font-semibold text-slate-500">Recipient</div>
                            <div class="mt-1 font-medium text-slate-900 dark:text-white">
                                {{ $selectedLog->recipient_email }}
                                @if($selectedLog->recipient_name)
                                    <span class="text-xs text-slate-400">({{ $selectedLog->recipient_name }})</span>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-3 dark:bg-slate-950">
                            <div class="text-xs font-semibold text-slate-500">Sender</div>
                            <div class="mt-1 font-medium text-slate-900 dark:text-white">
                                {{ $selectedLog->sender_email ?: config('mail.from.address') }}
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-3 text-sm dark:bg-slate-950">
                        <div class="text-xs font-semibold text-slate-500">Subject</div>
                        <div class="mt-1 font-bold text-slate-900 dark:text-white">{{ $selectedLog->subject }}</div>
                    </div>

                    @if ($selectedLog->error_message)
                        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/50 dark:bg-rose-950/30">
                            <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">
                                <i data-lucide="alert-octagon" class="h-4 w-4"></i>
                                Error Details
                            </div>
                            <pre class="mt-2 whitespace-pre-wrap font-mono text-xs text-rose-800 dark:text-rose-300">{{ $selectedLog->error_message }}</pre>
                        </div>
                    @endif

                    @if(!empty($selectedLog->metadata))
                        <div class="rounded-lg bg-slate-50 p-3 text-xs dark:bg-slate-950">
                            <div class="font-semibold text-slate-500">Metadata</div>
                            <pre class="mt-1 overflow-x-auto rounded bg-slate-900 p-2 text-slate-200">{{ json_encode($selectedLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endif

                    <div class="flex justify-between text-xs text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span>Queued: {{ $selectedLog->created_at->format('M d, Y H:i:s') }}</span>
                        <span>Delivered: {{ $selectedLog->sent_at ? $selectedLog->sent_at->format('M d, Y H:i:s') : 'Pending / Not sent' }}</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" wire:click="closeInspect"
                            class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
