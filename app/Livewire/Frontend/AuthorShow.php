<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('frontend.layouts.app')]
class AuthorShow extends Component
{
    use WithPagination;

    public User $author;

    #[Url(as: 'sort')]
    public string $sort = 'latest';

    #[Url(as: 'topic')]
    public string $selectedCategory = 'all';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(?User $user = null): void
    {
        // /author/{user} route-binding hands us $user; Livewire::test()
        // hands us $author directly via the public-property array.
        if ($user !== null) {
            $this->author = $user;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function selectCategory(string $slug): void
    {
        $this->selectedCategory = $slug;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = 'all';
        $this->sort = 'latest';
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<Post>
     */
    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        $query = Post::query()
            ->with(['translations', 'featuredImage:id,disk,path,mime_type,alt_text', 'category.translations', 'author'])
            ->where('status', PostStatus::Published->value)
            ->where('author_id', $this->author->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        if ($this->selectedCategory !== 'all') {
            $catSlug = $this->selectedCategory;
            $query->whereHas('category', function ($q) use ($catSlug) {
                $q->whereHas('translations', function ($qt) use ($catSlug) {
                    $qt->where('slug', $catSlug);
                });
            });
        }

        if (trim($this->search) !== '') {
            $s = trim($this->search);
            $query->whereHas('translations', function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('excerpt', 'like', "%{$s}%");
            });
        }

        match ($this->sort) {
            'popular' => $query->orderByDesc('view_count')->orderByDesc('published_at'),
            'oldest' => $query->orderBy('published_at'),
            default => $query->orderByDesc('published_at'),
        };

        return $query->paginate(perPage: 8);
    }

    /**
     * Total published posts by this author.
     */
    #[Computed]
    public function totalArticlesCount(): int
    {
        return Post::query()
            ->where('status', PostStatus::Published->value)
            ->where('author_id', $this->author->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->count();
    }

    /**
     * Total reads / views across this author's posts.
     */
    #[Computed]
    public function totalReadsCount(): int
    {
        return (int) Post::query()
            ->where('status', PostStatus::Published->value)
            ->where('author_id', $this->author->id)
            ->sum('view_count');
    }

    /**
     * Nicely formatted reads count (e.g., 1.2M, 45K, 350).
     */
    #[Computed]
    public function formattedTotalReads(): string
    {
        $reads = $this->totalReadsCount;

        if ($reads >= 1_000_000) {
            return round($reads / 1_000_000, 1).'M';
        }

        if ($reads >= 1_000) {
            return round($reads / 1_000, 1).'K';
        }

        return (string) $reads;
    }

    /**
     * Number of distinct topics / categories covered by this author.
     */
    #[Computed]
    public function topicsCoveredCount(): int
    {
        return Post::query()
            ->where('status', PostStatus::Published->value)
            ->where('author_id', $this->author->id)
            ->whereNotNull('category_id')
            ->distinct('category_id')
            ->count('category_id');
    }

    /**
     * Years of experience or active tenure.
     */
    #[Computed]
    public function yearsOfExperience(): string
    {
        $startDate = $this->author->hire_date ?? $this->author->created_at;
        $diff = $startDate ? now()->diffInYears($startDate) : 0;

        return max(1, (int) $diff).'+';
    }

    /**
     * Year active since.
     */
    #[Computed]
    public function activeSince(): string
    {
        return ($this->author->hire_date ?? $this->author->created_at)?->format('Y') ?? date('Y');
    }

    /**
     * Author's popular topics with published post counts.
     */
    #[Computed]
    public function authorTopics(): Collection
    {
        return Category::query()
            ->whereHas('posts', fn ($q) => $q->where('author_id', $this->author->id)
                ->where('status', PostStatus::Published->value)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now()))
            ->withCount(['posts' => fn ($q) => $q->where('author_id', $this->author->id)
                ->where('status', PostStatus::Published->value)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())])
            ->orderByDesc('posts_count')
            ->limit(6)
            ->get();
    }

    /**
     * Top category name for Author Highlights.
     */
    #[Computed]
    public function topCategoryName(): string
    {
        $topCat = $this->authorTopics->first();

        if ($topCat) {
            return html_entity_decode((string) ($topCat->translate('name') ?? $topCat->name), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return 'Technology & AI';
    }

    /**
     * Most read article for Author Highlights.
     */
    #[Computed]
    public function mostReadArticle(): ?Post
    {
        return Post::query()
            ->with(['translations'])
            ->where('status', PostStatus::Published->value)
            ->where('author_id', $this->author->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('view_count')
            ->first();
    }

    /**
     * Other active authors for the sidebar author rows.
     */
    #[Computed]
    public function otherAuthors(): Collection
    {
        return User::query()
            ->where('id', '!=', $this->author->id)
            ->where('status', User::STATUS_ACTIVE)
            ->whereHas('posts', fn ($q) => $q->where('status', PostStatus::Published->value))
            ->withCount(['posts' => fn ($q) => $q->where('status', PostStatus::Published->value)])
            ->orderByDesc('posts_count')
            ->limit(4)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.frontend.author-show', [
            'metaTitle' => $this->author->name.' - Author Profile',
            'metaDescription' => $this->author->bio ?: 'Articles, insights and publications by '.$this->author->name,
        ]);
    }
}
