<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('frontend.layouts.app')]
#[Title('Blogs — AI, Tech & Innovation')]
class BlogIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'category', history: true)]
    public string $selectedCategory = 'all';

    #[Url(as: 'sort', history: true)]
    public string $sortBy = 'latest';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function selectCategory(string $slug): void
    {
        $this->selectedCategory = ($this->selectedCategory === $slug) ? 'all' : $slug;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = 'all';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<Post>
     */
    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return Post::query()
            ->with([
                'translations',
                'author:id,name',
                'featuredImage:id,disk,path,mime_type,alt_text',
                'category:id,icon',
                'category.translations',
            ])
            ->where('status', \App\Enums\PostStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->when($this->search !== '', function ($query): void {
                $term = '%' . trim($this->search) . '%';
                $query->whereHas('translations', function ($q) use ($term): void {
                    $q->where('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term)
                        ->orWhere('content', 'like', $term);
                });
            })
            ->when($this->selectedCategory !== 'all', function ($query): void {
                $query->whereHas('category.translations', function ($q): void {
                    $q->where('slug', $this->selectedCategory);
                });
            })
            ->when($this->sortBy === 'latest', fn ($q) => $q->orderByDesc('published_at'))
            ->when($this->sortBy === 'views', fn ($q) => $q->orderByDesc('view_count'))
            ->when($this->sortBy === 'oldest', fn ($q) => $q->orderBy('published_at'))
            ->paginate(6);
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->whereHas('posts', fn ($q) => $q->where('status', \App\Enums\PostStatus::Published->value))
            ->withCount(['posts' => fn ($q) => $q->where('status', \App\Enums\PostStatus::Published->value)])
            ->orderByDesc('posts_count')
            ->take(10)
            ->get();
    }

    /**
     * @return Collection<int, Post>
     */
    #[Computed]
    public function recentPosts(): Collection
    {
        return Post::query()
            ->with([
                'translations',
                'featuredImage:id,disk,path,mime_type,alt_text',
            ])
            ->where('status', \App\Enums\PostStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->take(4)
            ->get();
    }

    public function render(): View
    {
        $settings = app(\App\Services\SettingService::class);
        $siteName = (string) ($settings->get('site.name') ?? 'Revision');

        return view('livewire.frontend.blog-index', [
            'metaTitle' => 'Blogs — ' . $siteName,
            'metaDescription' => 'Explore the latest insights, practical guides, and articles on Artificial Intelligence, technology, innovation, and machine learning.',
        ]);
    }
}
