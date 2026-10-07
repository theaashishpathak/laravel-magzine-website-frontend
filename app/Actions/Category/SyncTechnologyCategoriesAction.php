<?php

declare(strict_types=1);

namespace App\Actions\Category;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use App\Models\NavigationItem;
use App\Models\Post;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SyncTechnologyCategoriesAction
{
    /**
     * The 12 Technology categories definitions.
     */
    public const TECH_CATEGORIES = [
        ['key' => 'artificial-intelligence', 'name' => 'Artificial Intelligence', 'slug' => 'artificial-intelligence', 'icon' => 'cpu', 'color' => '#6366f1', 'is_featured' => true],
        ['key' => 'generative-ai', 'name' => 'Generative AI', 'slug' => 'generative-ai', 'icon' => 'sparkles', 'color' => '#a855f7', 'is_featured' => true],
        ['key' => 'llms-nlp', 'name' => 'LLMs & NLP', 'slug' => 'llms-nlp', 'icon' => 'message-square-code', 'color' => '#ec4899', 'is_featured' => true],
        ['key' => 'ai-agents', 'name' => 'AI Agents', 'slug' => 'ai-agents', 'icon' => 'bot', 'color' => '#8b5cf6', 'is_featured' => true],
        ['key' => 'ai-engineering', 'name' => 'AI Engineering', 'slug' => 'ai-engineering', 'icon' => 'binary', 'color' => '#3b82f6', 'is_featured' => true],
        ['key' => 'software-development', 'name' => 'Software Development', 'slug' => 'software-development', 'icon' => 'code-2', 'color' => '#06b6d4', 'is_featured' => true],
        ['key' => 'web-development', 'name' => 'Web Development', 'slug' => 'web-development', 'icon' => 'globe', 'color' => '#10b981', 'is_featured' => false],
        ['key' => 'cloud-devops', 'name' => 'Cloud & DevOps', 'slug' => 'cloud-devops', 'icon' => 'cloud', 'color' => '#f59e0b', 'is_featured' => false],
        ['key' => 'cybersecurity', 'name' => 'Cybersecurity', 'slug' => 'cybersecurity', 'icon' => 'shield-check', 'color' => '#ef4444', 'is_featured' => false],
        ['key' => 'data-analytics', 'name' => 'Data & Analytics', 'slug' => 'data-analytics', 'icon' => 'bar-chart-3', 'color' => '#14b8a6', 'is_featured' => false],
        ['key' => 'blockchain-web3', 'name' => 'Blockchain & Web3', 'slug' => 'blockchain-web3', 'icon' => 'blocks', 'color' => '#f97316', 'is_featured' => false],
        ['key' => 'emerging-technology', 'name' => 'Emerging Technology', 'slug' => 'emerging-technology', 'icon' => 'zap', 'color' => '#84cc16', 'is_featured' => false],
    ];

    public function execute(): array
    {
        return DB::transaction(function () {
            $english = Language::firstOrCreate(
                ['code' => 'en'],
                [
                    'name' => 'English',
                    'native_name' => 'English',
                    'flag_emoji' => '🇺🇸',
                    'direction' => 'ltr',
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            $syncedCategories = [];
            $newCategoryIds = [];

            foreach (self::TECH_CATEGORIES as $index => $item) {
                $category = null; // ALWAYS reset per iteration to prevent overwriting

                // 1. Check if category exists by translation slug or name
                $existingTranslation = CategoryTranslation::where('language_id', $english->id)
                    ->where(function ($q) use ($item) {
                        $q->where('slug', $item['slug'])
                          ->orWhere('name', $item['name']);
                    })
                    ->first();

                if ($existingTranslation) {
                    $category = Category::withTrashed()->find($existingTranslation->category_id);
                    if ($category && $category->trashed()) {
                        $category->restore();
                    }
                }

                // 2. If not found, create new category
                if (! $category) {
                    $category = Category::create([
                        'icon' => $item['icon'],
                        'color' => $item['color'],
                        'show_in_menu' => true,
                        'show_on_homepage' => true,
                        'is_featured' => $item['is_featured'],
                        'sort_order' => $index + 1,
                        'layout' => Category::LAYOUT_GRID,
                    ]);
                } else {
                    $category->update([
                        'icon' => $item['icon'],
                        'color' => $item['color'],
                        'show_in_menu' => true,
                        'show_on_homepage' => true,
                        'is_featured' => $item['is_featured'],
                        'sort_order' => $index + 1,
                        'layout' => Category::LAYOUT_GRID,
                    ]);
                }

                // 3. Upsert English translation
                CategoryTranslation::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'language_id' => $english->id,
                    ],
                    [
                        'name' => $item['name'],
                        'slug' => $item['slug'],
                        'description' => "Deep dives, industry analysis, and practical insights into {$item['name']}.",
                    ]
                );

                $newCategoryIds[] = $category->id;
                $syncedCategories[] = $item['name'];
            }

            // Clean up any remaining legacy categories outside the 12 tech categories
            $legacyCategories = Category::whereNotIn('id', $newCategoryIds)->get();
            $firstCatId = $newCategoryIds[0];

            foreach ($legacyCategories as $legacy) {
                Post::where('category_id', $legacy->id)->update(['category_id' => $firstCatId]);
                Post::where('subcategory_id', $legacy->id)->update(['subcategory_id' => null]);
                CategoryTranslation::where('category_id', $legacy->id)->delete();
                $legacy->forceDelete();
            }

            // Distribute all existing posts across the 12 categories evenly
            $posts = Post::orderBy('id')->get();
            if ($posts->isNotEmpty()) {
                foreach ($posts as $pIdx => $post) {
                    $targetCatId = $newCategoryIds[$pIdx % count($newCategoryIds)];
                    $post->update(['category_id' => $targetCatId]);
                }
            }

            // Sync Header Navigation Dropdown Items
            $catDropdown = NavigationItem::where('location', 'header')
                ->where('title', 'Categories')
                ->first();

            if (! $catDropdown) {
                $catDropdown = NavigationItem::create([
                    'title' => 'Categories',
                    'url' => '#',
                    'type' => 'custom',
                    'target' => '_self',
                    'order' => 2,
                    'is_active' => true,
                    'location' => 'header',
                ]);
            }

            // Clear old child items under Categories dropdown
            NavigationItem::where('parent_id', $catDropdown->id)->delete();

            // Insert all 12 categories as dropdown children
            $categories = Category::whereIn('id', $newCategoryIds)
                ->orderBy('sort_order')
                ->get();

            $subOrder = 1;
            foreach ($categories as $cat) {
                $slug = $cat->translate('slug');
                $name = $cat->translate('name');
                if ($slug && $name) {
                    NavigationItem::create([
                        'parent_id' => $catDropdown->id,
                        'title' => $name,
                        'url' => '/category/' . $slug,
                        'type' => 'category',
                        'icon' => $cat->icon,
                        'order' => $subOrder++,
                        'is_active' => true,
                        'location' => 'header',
                    ]);
                }
            }

            // Clear optimization cache
            Artisan::call('optimize:clear');

            return [
                'status' => 'success',
                'categories_synced' => count($syncedCategories),
                'list' => $syncedCategories,
            ];
        });
    }
}
