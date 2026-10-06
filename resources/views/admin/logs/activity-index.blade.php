<div x-data="{ openLog: null }">
    <x-admin.page-header
        eyebrow="Audit"
        icon="history"
        title="Activity Logs"
        description="Every model change — created, updated, deleted — captured with the actor, IP, browser, and approximate location.">
        <x-slot:actions>
            <button type="button" wire:click="clearFilters"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                <i data-lucide="filter-x" class="h-3.5 w-3.5"></i>
                Clear filters
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.section title="Filters" description="Narrow the audit trail by date, user, model, event, or channel." class="mb-6">
        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-7">
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

            {{-- User --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">User</label>
                <select wire:model.live="userId"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">All users</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Model --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Model</label>
                <select wire:model.live="model"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">All models</option>
                    @foreach ($models as $modelClass)
                        <option value="{{ $modelClass }}">{{ class_basename($modelClass) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Event --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Event</label>
                <select wire:model.live="event"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">All events</option>
                    @foreach ($events as $e)
                        <option value="{{ $e }}">{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Channel --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Channel</label>
                <select wire:model.live="logName"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">All channels</option>
                    @foreach ($channels as $ch)
                        <option value="{{ $ch }}">{{ ucfirst($ch) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Search --}}
            <div>
                <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Search</label>
                <input wire:model.live.debounce.400ms="search" type="text" placeholder="Description / properties..."
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 dark:border-slate-700 dark:bg-slate-950">
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

    <x-admin.table-shell title="Audit history" description="Latest first. Click View to inspect a row's full diff.">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
            <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:bg-slate-950/50 dark:text-slate-300">
                <tr>
                    <th class="px-4 py-3">SL</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Affected Item</th>
                    <th class="px-4 py-3">Channel</th>
                    <th class="px-4 py-3">Changed</th>
                    <th class="px-4 py-3">IP & Location</th>
                    <th class="px-4 py-3">Timestamp</th>
                    <th class="px-4 py-3 text-right">Details / Diff</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($logs as $i => $log)
                    @php
                        $badge = match ($log->event) {
                            'created' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'updated' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                            'deleted' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                        };

                        $props = collect($log->properties);
                        $context = (array) ($props->get('context') ?? []);
                        $newAttrs = (array) ($props->get('attributes') ?? []);
                        $oldAttrs = (array) ($props->get('old') ?? []);

                        $rows = [];
                        if ($log->event === 'updated') {
                            foreach ($newAttrs as $k => $v) {
                                $rows[$k] = ['old' => $oldAttrs[$k] ?? null, 'new' => $v];
                            }
                        } elseif ($log->event === 'created') {
                            foreach ($newAttrs as $k => $v) {
                                $rows[$k] = ['old' => null, 'new' => $v];
                            }
                        } elseif ($log->event === 'deleted') {
                            foreach ($oldAttrs as $k => $v) {
                                $rows[$k] = ['old' => $v, 'new' => null];
                            }
                        }

                        $ip = $context['ip_address'] ?? null;
                        $browser = $context['browser'] ?? null;
                        $country = $context['country'] ?? null;
                        $countryCode = $context['country_code'] ?? null;
                        $city = $context['city'] ?? null;

                        $causerName = $log->causer?->name ?? 'System';
                        $causerEmail = $log->causer?->email;

                        $subjectTitle = null;
                        if ($log->subject) {
                            $subjectTitle = $log->subject->title ?? $log->subject->name ?? (method_exists($log->subject, 'translation') ? $log->subject->translation()?->title : null);
                        }

                        $subjectLabel = $log->subject_type
                            ? class_basename($log->subject_type).($log->subject_id ? ' #'.$log->subject_id : '')
                            : '—';

                        $logStatus = $props->get('status') ?? 'Success';
                    @endphp
                    <tr wire:key="activity-log-{{ $log->id }}" class="align-top transition hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                        <td class="px-4 py-3 text-slate-400">{{ $logs->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ match(strtolower((string) $logStatus)) {
                                'failed', 'error' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300',
                                'warning' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                default => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300'
                            } }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ match(strtolower((string) $logStatus)) {
                                    'failed', 'error' => 'bg-rose-500',
                                    'warning' => 'bg-amber-500',
                                    default => 'bg-emerald-500'
                                } }}"></span>
                                {{ ucfirst((string) $logStatus) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($log->event ?? '—') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $causerName }}</div>
                            <div class="text-xs text-slate-500">{{ $causerEmail }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-mono text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $subjectLabel }}</div>
                            @if ($subjectTitle)
                                <div class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate max-w-[200px]" title="{{ $subjectTitle }}">{{ $subjectTitle }}</div>
                            @endif
                            @if ($log->description)
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $log->description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 font-mono text-[11px] uppercase tracking-wider text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                {{ $log->log_name ?? 'default' }}
                            </span>
                        </td>
                        <td class="max-w-xs px-4 py-3">
                            <div class="space-y-0.5 text-xs">
                                @php($shown = 0)
                                @foreach ($rows as $field => $diff)
                                    @if ($shown >= 4)
                                        <div class="text-slate-400">+ {{ count($rows) - 4 }} more…</div>
                                        @break
                                    @endif
                                    @php($shown++)
                                    <div class="flex flex-wrap items-baseline gap-1">
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $field }}:</span>
                                        @if ($log->event === 'updated' && $diff['old'] !== null)
                                            <span class="rounded bg-rose-50 px-1 text-rose-700 line-through dark:bg-rose-900/30 dark:text-rose-300">{{ \Illuminate\Support\Str::limit((string) (is_scalar($diff['old']) ? $diff['old'] : json_encode($diff['old'])), 30) }}</span>
                                            <span class="text-slate-400">→</span>
                                        @endif
                                        <span class="rounded bg-emerald-50 px-1 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">{{ \Illuminate\Support\Str::limit((string) (is_scalar($diff['new']) ? $diff['new'] : json_encode($diff['new'])), 30) }}</span>
                                    </div>
                                @endforeach
                                @if (empty($rows))
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-mono text-xs text-slate-700 dark:text-slate-300">{{ $ip ?? '—' }}</div>
                            @if ($country)
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $city ? "{$city}, " : '' }}{{ $country }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">
                            <div class="font-medium">{{ $log->created_at?->format('d M Y · h:i A') }}</div>
                            <div class="text-[11px] text-slate-400">{{ $log->created_at?->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button type="button"
                                @click='openLog = {{ json_encode([
                                    "id" => $log->id,
                                    "subject" => $subjectLabel,
                                    "event" => $log->event,
                                    "channel" => $log->log_name,
                                    "user" => $causerName,
                                    "ip" => $ip,
                                    "browser" => $browser,
                                    "date" => $log->created_at?->format("d M Y h:i A"),
                                    "old" => $oldAttrs,
                                    "new" => $newAttrs,
                                ]) }}'
                                class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100 dark:border-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                <i data-lucide="eye" class="h-3 w-3"></i>
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-0">
                            <x-admin.empty-state
                                icon="history"
                                title="No activity matches your filter."
                                description="Audit entries will appear here as users create, update, or delete records." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot:footer>
            {{ $logs->links() }}
        </x-slot:footer>
    </x-admin.table-shell>

    {{-- Diff modal — unchanged behaviourally, lightly polished. --}}
    <div x-show="openLog" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
         @keydown.escape.window="openLog = null">
        <div class="relative w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900" @click.outside="openLog = null">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-slate-800">
                <h3 class="text-base font-bold">
                    Diff: <span class="font-mono text-sm font-normal text-slate-500" x-text="openLog?.subject"></span>
                </h3>
                <button type="button" @click="openLog = null" class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <div class="max-h-[70vh] space-y-4 overflow-y-auto p-5 text-sm">
                <dl class="grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs">
                    <dt class="font-semibold text-slate-500">User</dt><dd x-text="openLog?.user"></dd>
                    <dt class="font-semibold text-slate-500">Event</dt><dd class="capitalize" x-text="openLog?.event"></dd>
                    <dt class="font-semibold text-slate-500">Channel</dt><dd x-text="openLog?.channel"></dd>
                    <dt class="font-semibold text-slate-500">IP</dt><dd class="font-mono" x-text="openLog?.ip ?? '—'"></dd>
                    <dt class="font-semibold text-slate-500">Browser</dt><dd x-text="openLog?.browser ?? '—'"></dd>
                    <dt class="font-semibold text-slate-500">Date</dt><dd x-text="openLog?.date"></dd>
                </dl>

                <template x-if="openLog?.old && Object.keys(openLog?.old || {}).length">
                    <div>
                        <h4 class="mb-1.5 text-[10px] font-bold uppercase tracking-[0.22em] text-rose-600">Before</h4>
                        <pre class="overflow-x-auto rounded-lg bg-rose-50 p-3 text-xs leading-relaxed text-rose-900 dark:bg-rose-900/20 dark:text-rose-200" x-text="JSON.stringify(openLog?.old, null, 2)"></pre>
                    </div>
                </template>

                <template x-if="openLog?.new && Object.keys(openLog?.new || {}).length">
                    <div>
                        <h4 class="mb-1.5 text-[10px] font-bold uppercase tracking-[0.22em] text-emerald-600">After</h4>
                        <pre class="overflow-x-auto rounded-lg bg-emerald-50 p-3 text-xs leading-relaxed text-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200" x-text="JSON.stringify(openLog?.new, null, 2)"></pre>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
