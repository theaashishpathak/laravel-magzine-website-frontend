<?php

declare(strict_types=1);

namespace App\Livewire\Contributor;

use App\Enums\PostStatus;
use App\Models\AIUsageLog;
use App\Models\EditorialNote;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Contributor Portal Dashboard
 *
 * Dedicated dashboard for contributors to manage drafts, track submissions
 * in the review queue, and review feedback notes left by editors.
 */
#[Layout('layouts.app')]
#[Title('Contributor Dashboard')]
class Dashboard extends Component
{
    public function mount(): void
    {
        abort_unless(
            auth()->user()?->hasRole('Contributor') || (auth()->user()?->can('posts.create') ?? false),
            403,
            'You do not have contributor privileges.',
        );
    }

    #[Computed]
    public function user(): User
    {
        /** @var User $u */
        $u = auth()->user();

        return $u;
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function counts(): array
    {
        $baseQuery = Post::query()->where('author_id', $this->user->id);

        return [
            'total' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', PostStatus::Draft->value)->count(),
            'pending' => (clone $baseQuery)->whereIn('status', [
                PostStatus::PendingReview->value,
                PostStatus::InReview->value,
            ])->count(),
            'changes_requested' => (clone $baseQuery)->where('status', PostStatus::ChangesRequested->value)->count(),
            'published' => (clone $baseQuery)->where('status', PostStatus::Published->value)->count(),
        ];
    }

    #[Computed]
    public function totalViews(): int
    {
        return (int) Post::query()
            ->where('author_id', $this->user->id)
            ->sum('view_count');
    }

    /**
     * @return array{calls:int, tokens:int, cost:float}
     */
    #[Computed]
    public function aiUsageThisMonth(): array
    {
        $stats = AIUsageLog::query()
            ->forUser($this->user->id)
            ->thisMonth()
            ->successful()
            ->selectRaw('COUNT(*) as calls, COALESCE(SUM(total_tokens), 0) as tokens, COALESCE(SUM(estimated_cost_usd), 0) as cost')
            ->first();

        return [
            'calls' => (int) ($stats?->calls ?? 0),
            'tokens' => (int) ($stats?->tokens ?? 0),
            'cost' => (float) ($stats?->cost ?? 0),
        ];
    }

    /**
     * @return Collection<int, Post>
     */
    #[Computed]
    public function recentSubmissions(): Collection
    {
        return Post::query()
            ->with(['translations', 'category.translations'])
            ->where('author_id', $this->user->id)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();
    }

    /**
     * Editorial notes left by editors on this contributor's submissions.
     *
     * @return Collection<int, EditorialNote>
     */
    #[Computed]
    public function editorialNotes(): Collection
    {
        $postIds = Post::query()
            ->where('author_id', $this->user->id)
            ->pluck('id');

        return EditorialNote::query()
            ->with(['post.translations', 'author:id,name'])
            ->whereIn('post_id', $postIds)
            ->latest()
            ->limit(8)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.contributor.dashboard');
    }
}
