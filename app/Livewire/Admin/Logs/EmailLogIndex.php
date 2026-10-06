<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Logs;

use App\Models\EmailDeliveryLog;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Email Delivery Logs')]
class EmailLogIndex extends Component
{
    use WithPagination;

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'from', except: '')]
    public string $from = '';

    #[Url(as: 'to', except: '')]
    public string $to = '';

    #[Url(as: 'mailer', except: '')]
    public string $mailer = '';

    public ?int $selectedLogId = null;

    public function clearFilters(): void
    {
        $this->reset(['status', 'search', 'from', 'to', 'mailer']);
        $this->resetPage();
    }

    public function setDatePreset(string $preset): void
    {
        $today = now()->toDateString();
        match ($preset) {
            'today' => [$this->from = $today, $this->to = $today],
            'yesterday' => [$this->from = now()->subDay()->toDateString(), $this->to = now()->subDay()->toDateString()],
            '7days' => [$this->from = now()->subDays(6)->toDateString(), $this->to = $today],
            '30days' => [$this->from = now()->subDays(29)->toDateString(), $this->to = $today],
            'this_month' => [$this->from = now()->startOfMonth()->toDateString(), $this->to = now()->endOfMonth()->toDateString()],
            'clear' => [$this->from = '', $this->to = ''],
            default => null,
        };
        $this->resetPage();
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    public function inspect(int $id): void
    {
        $this->selectedLogId = $id;
    }

    public function closeInspect(): void
    {
        $this->selectedLogId = null;
    }

    public function deleteLog(int $id): void
    {
        EmailDeliveryLog::destroy($id);
        $this->dispatch('toast.success', message: 'Email log deleted.');
    }

    public function purgeOldLogs(): void
    {
        $deleted = EmailDeliveryLog::where('created_at', '<', now()->subDays(30))->delete();
        $this->dispatch('toast.success', message: "Purged {$deleted} logs older than 30 days.");
    }

    public function render(): View
    {
        $query = EmailDeliveryLog::query()
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->mailer !== '', fn ($q) => $q->where('mailer', $this->mailer))
            ->when($this->from !== '', fn ($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to !== '', fn ($q) => $q->whereDate('created_at', '<=', $this->to))
            ->when($this->search !== '', function ($q): void {
                $q->where(function ($qq): void {
                    $qq->where('recipient_email', 'like', '%' . $this->search . '%')
                        ->orWhere('recipient_name', 'like', '%' . $this->search . '%')
                        ->orWhere('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('error_message', 'like', '%' . $this->search . '%');
                });
            });

        $totalCount = EmailDeliveryLog::count();
        $deliveredCount = EmailDeliveryLog::delivered()->count();
        $failedCount = EmailDeliveryLog::failed()->count();
        $pendingCount = EmailDeliveryLog::pending()->count();
        $successRate = $totalCount > 0 ? round(($deliveredCount / $totalCount) * 100, 1) : 100.0;

        $selectedLog = $this->selectedLogId ? EmailDeliveryLog::find($this->selectedLogId) : null;

        return view('admin.logs.email-index', [
            'logs' => $query->latest('id')->paginate(20),
            'totalCount' => $totalCount,
            'deliveredCount' => $deliveredCount,
            'failedCount' => $failedCount,
            'pendingCount' => $pendingCount,
            'successRate' => $successRate,
            'selectedLog' => $selectedLog,
        ]);
    }
}
