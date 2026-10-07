<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('frontend.layouts.app')]
class CategoryShow extends Component
{
    use WithPagination;

    public ?Category $category = null;

    #[Url(as: 'c', except: 'all')]
    public string $selectedCategory = 'all';

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'latest')]
    public string $sortBy = 'latest';

    /**
     * Resolve the category from URL slug, route param, or component mount argument.
     */
    public function mount(?string $locale = null, ?string $slug = null, ?Category $category = null): void
    {
        if ($category !== null) {
            $this->category = $category;
            $this->selectedCategory = $category->translate('slug') ?? ('cat-' . $category->id);
            return;
        }

        if (isset($this->category) && $this->category !== null) {
            $this->selectedCategory = $this->category->translate('slug') ?? ('cat-' . $this->category->id);
            return;
        }

        if ($slug !== null && $slug !== '' && $slug !== 'all') {
            $languageId = app(\App\Support\LocaleResolver::class)->current()?->id;

            $categoryId = \DB::table('category_translations')
                ->where('slug', $slug)
                ->when($languageId !== null, fn ($q) => $q->where('language_id', $languageId))
                ->value('category_id');

            if ($categoryId === null) {
                $categoryId = \DB::table('category_translations')
                    ->where('slug', $slug)
                    ->value('category_id');
            }

            if ($categoryId !== null) {
                $this->category = Category::query()->find($categoryId);
                if ($this->category) {
                    $this->selectedCategory = $slug;
                } else {
                    abort(404);
                }
            } else {
                abort(404);
            }
        } elseif ($this->selectedCategory !== 'all') {
            $this->category = Category::query()
                ->whereHas('translations', fn ($q) => $q->where('slug', $this->selectedCategory))
                ->first();
        }
    }

    public function selectCategory(string $slug): void
    {
        if ($this->selectedCategory === $slug && $slug !== 'all') {
            $this->selectedCategory = 'all';
            $this->category = null;
        } else {
            $this->selectedCategory = $slug;
            if ($slug === 'all') {
                $this->category = null;
            } else {
                $this->category = Category::query()
                    ->whereHas('translations', fn ($q) => $q->where('slug', $slug))
                    ->first();
            }
        }
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = 'all';
        $this->category = null;
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->withCount(['posts' => fn ($q) => $q->where('status', \App\Enums\PostStatus::Published->value)])
            ->with('translations')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, Post>
     */
    #[Computed]
    public function popularPosts(): Collection
    {
        return Post::query()
            ->with(['translations', 'featuredImage'])
            ->where('status', \App\Enums\PostStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('view_count')
            ->take(5)
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
                'category:id,icon,color',
                'category.translations',
            ])
            ->where('status', \App\Enums\PostStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->when($this->category !== null, function ($query): void {
                $query->where('category_id', $this->category->id);
            }, function ($query): void {
                if ($this->selectedCategory !== 'all') {
                    $query->whereHas('category.translations', fn ($q) => $q->where('slug', $this->selectedCategory));
                }
            })
            ->when($this->search !== '', function ($query): void {
                $term = '%' . trim($this->search) . '%';
                $query->whereHas('translations', function ($q) use ($term): void {
                    $q->where('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term)
                        ->orWhere('content', 'like', $term);
                });
            })
            ->when($this->sortBy === 'views', fn ($q) => $q->orderByDesc('view_count'))
            ->when($this->sortBy === 'oldest', fn ($q) => $q->orderBy('published_at'))
            ->when($this->sortBy === 'latest' || !in_array($this->sortBy, ['views', 'oldest'], true), fn ($q) => $q->orderByDesc('published_at'))
            ->paginate(8);
    }

    public function render(): View
    {
        $catName = $this->category?->translate('name');
        if (!$catName && $this->selectedCategory !== 'all') {
            $matchedCat = $this->categories->first(fn ($c) => ($c->translate('slug') ?? ('cat-' . $c->id)) === $this->selectedCategory);
            $catName = $matchedCat?->translate('name');
        }

        $isIndividual = $this->category !== null || ($this->selectedCategory !== 'all');
        $title = $catName ? "{$catName} — Categories" : "Explore by Category";
        $description = $this->category?->translate('description')
            ?: "Find the latest articles, tutorials and insights on your favorite topics. Browse through our curated categories and dive deeper into the world of AI.";

        return view('livewire.frontend.category-show', [
            'metaTitle' => $title,
            'metaDescription' => $description,
            'activeCategoryName' => $catName ?: 'All Categories',
            'isIndividual' => $isIndividual,
            'currentCategory' => $this->category,
        ]);
    }
}
