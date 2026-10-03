<?php

use App\Http\Controllers\Auth\ProfileAvatarController;
use App\Http\Controllers\Frontend\FeedController;
use App\Http\Controllers\Frontend\RobotsController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Livewire\Admin\Dashboards\MyDashboard;
use App\Livewire\Admin\Settings\SettingsIndex;
use App\Livewire\Frontend\AuthorShow;
use App\Livewire\Frontend\BlogIndex;
use App\Livewire\Frontend\CategoryShow;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\PageShow;
use App\Livewire\Frontend\PostShow;
use App\Livewire\Frontend\Search;
use App\Livewire\Frontend\TagShow;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

// -------------------------------------------------------------------------
// Blogger4U — Frontend (Phase 5)
// -------------------------------------------------------------------------

// Sitemap + robots + feeds — always at root regardless of locale
Route::get('/sitemap.xml', SitemapController::class)->name('frontend.sitemap');
Route::get('/robots.txt', RobotsController::class)->name('frontend.robots');
Route::get('/feed.xml', [FeedController::class, 'global'])->name('frontend.feed.rss');
Route::get('/category/{slug}.rss', [FeedController::class, 'category'])->name('frontend.feed.category');

// Newsletter confirm + unsubscribe — locale-agnostic since tokens are unique
Route::get('/newsletter/confirm/{token}', [\App\Http\Controllers\Frontend\NewsletterController::class, 'confirm'])
    ->where('token', '[A-Za-z0-9]{20,80}')
    ->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\Frontend\NewsletterController::class, 'unsubscribe'])
    ->where('token', '[A-Za-z0-9]{20,80}')
    ->name('newsletter.unsubscribe');

// Ad click tracking — short flat URL outside the locale group so the
// regex on /{slug} doesn't accidentally catch /ads/click/123.
Route::get('/ads/click/{creative}', \App\Http\Controllers\Frontend\AdClickController::class)
    ->whereNumber('creative')
    ->name('ads.click');

// -------------------------------------------------------------------------
// Blogger4U — Clean Frontend Routes (Default Locale)
// -------------------------------------------------------------------------
Route::get('/', Home::class)->name('frontend.home');
Route::get('/blogs', BlogIndex::class)->name('frontend.blogs');
Route::get('/blog', fn () => redirect()->route('frontend.blogs'));
Route::get('/search', Search::class)->name('frontend.search');
Route::get('/author/{user}', AuthorShow::class)->whereNumber('user')->name('frontend.author');
Route::get('/tags/{tag:slug}', TagShow::class)->name('frontend.tag');
Route::get('/categories', CategoryShow::class)->name('frontend.categories');
Route::get('/category/{slug}', CategoryShow::class)->name('frontend.category');
Route::get('/page/{slug}', fn (string $slug) => redirect('/' . $slug, 301))->where('slug', '[a-z0-9\-]+')->name('frontend.page');

// -------------------------------------------------------------------------
// Blogger4U — Multi-language Localized Routes (Secondary Locales)
// -------------------------------------------------------------------------
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-z]{2}(-[A-Z]{2})?'],
], function (): void {
    Route::get('/', Home::class);
    Route::get('/blogs', BlogIndex::class);
    Route::get('/blog', fn () => redirect()->route('frontend.blogs'));
    Route::get('/search', Search::class);
    Route::get('/feed.xml', [FeedController::class, 'global'])->name('frontend.feed.rss.localized');
    Route::get('/category/{slug}.rss', [FeedController::class, 'category'])->name('frontend.feed.category.localized');
    Route::get('/author/{user}', AuthorShow::class)->whereNumber('user');
    Route::get('/tags/{tag:slug}', TagShow::class);
    Route::get('/categories', CategoryShow::class);
    Route::get('/category/{slug}', CategoryShow::class);
    Route::get('/page/{slug}', fn (string $locale, string $slug) => redirect("/{$locale}/{$slug}", 301))->where('slug', '[a-z0-9\-]+');
});

// -------------------------------------------------------------------------
// Authenticated app routes (admin/dashboard)
// -------------------------------------------------------------------------

Route::redirect('/admin', '/dashboard');

Route::middleware('auth')->group(function (): void {
    Route::redirect('/profile', '/user/profile');
    Route::get('/user/profile', function (): View {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        return view('auth.profile', [
            'activityLogs' => $user->profileActivityLogs()->latest()->paginate(10),
        ]);
    })->name('profile');
    Route::post('/user/profile/avatar', [ProfileAvatarController::class, 'update'])->name('profile.avatar.update');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    // /dashboard dispatches:
    //   - Visitor\Dashboard → for frontend readers (portal_type=visitor)
    //   - Author\Dashboard  → for content creators (has posts.create, not platform admin)
    //   - MyDashboard       → for everyone else (admin/staff)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // 1. Visitor / Subscriber portal
        if ($user->portal_type === 'visitor' || $user->hasRole('Subscriber')) {
            return redirect()->route('visitor.dashboard');
        }

        // 2. SEO Manager
        if ($user->hasRole('SEO Manager') || ($user->can('seo.view') && ! $user->can('posts.publish') && ! $user->can('staff.view'))) {
            return redirect()->route('admin.dashboards.seo');
        }

        // 3. Ad Manager
        if ($user->hasRole('Ad Manager') || ($user->can('ads.view') && ! $user->can('posts.create') && ! $user->can('staff.view'))) {
            return redirect()->route('admin.dashboards.revenue');
        }

        // 4. Contributor
        if ($user->hasRole('Contributor')) {
            return redirect()->route('dashboard.contributor');
        }

        // 5. Author
        if ($user->hasRole('Author')) {
            return redirect()->route('dashboard.author');
        }

        // 6. Newsroom Editor
        if ($user->hasRole('Editor')) {
            return redirect()->route('admin.dashboards.overview');
        }

        // 7. General Employee / Operations Manager
        if ($user->hasRole(['Manager', 'Employee'])) {
            return redirect()->route('dashboard.my');
        }

        // 8. Super Admin & Admin (Platform central command)
        if ($user->hasAnyRole(['Super Admin', 'Super-Admin', 'super_admin', 'Admin', 'admin'])) {
            return redirect()->route('admin.dashboard.super');
        }

        return redirect()->route('dashboard.my');
    })->name('dashboard');

    Route::get('/dashboard/my', MyDashboard::class)->name('dashboard.my');
    Route::get('/dashboard/author', \App\Livewire\Author\Dashboard::class)
        ->middleware('permission:posts.create')
        ->name('dashboard.author');
    Route::get('/dashboard/contributor', \App\Livewire\Contributor\Dashboard::class)
        ->name('dashboard.contributor');

    // Author profile editor
    Route::get('/author/profile', \App\Livewire\Author\Profile::class)
        ->middleware('permission:posts.create')
        ->name('author.profile');

    Route::get('/settings', SettingsIndex::class)->middleware('permission:settings.view')->name('settings');

    Route::get('/notifications', \App\Livewire\NotificationsIndex::class)
        ->middleware('permission:notifications.view')
        ->name('notifications.index');
});

// -------------------------------------------------------------------------
// Visitor portal — logged-in reader area (separate from admin/author).
// Locked to users with portal_type='visitor' via the `visitor` middleware
// alias declared in bootstrap/app.php. Staff get bounced back to /dashboard.
// -------------------------------------------------------------------------
Route::middleware(['auth', 'verified', 'visitor'])
    ->prefix('visitor')
    ->name('visitor.')
    ->group(function (): void {
        Route::get('/dashboard', \App\Livewire\Visitor\Dashboard::class)->name('dashboard');

        // ── My Library ───────────────────────────────────────────────────
        Route::get('/bookmarks',       \App\Livewire\Visitor\Bookmarks\Index::class)->name('bookmarks');
        Route::get('/reading-list',    \App\Livewire\Visitor\ReadingList\Index::class)->name('reading-list');
        Route::get('/reading-history', \App\Livewire\Visitor\ReadingHistory\Index::class)->name('reading-history');
        Route::get('/highlights',      \App\Livewire\Visitor\Highlights\Index::class)->name('highlights');

        // ── Engagement ───────────────────────────────────────────────────
        Route::get('/comments',  \App\Livewire\Visitor\Comments\Index::class)->name('comments');
        Route::get('/reactions', \App\Livewire\Visitor\Reactions\Index::class)->name('reactions');
        Route::get('/for-you',   \App\Livewire\Visitor\Recommendations\Index::class)->name('recommendations');

        // ── Following ────────────────────────────────────────────────────
        Route::get('/following/topics',  \App\Livewire\Visitor\Following\Topics::class)->name('following.topics');
        Route::get('/following/authors', \App\Livewire\Visitor\Following\Authors::class)->name('following.authors');
        Route::get('/following/users',   \App\Livewire\Visitor\Following\Users::class)->name('following.users');

        // ── Notifications ────────────────────────────────────────────────
        Route::get('/notifications', \App\Livewire\Visitor\Notifications\Index::class)->name('notifications');

        // ── Email & Newsletter ───────────────────────────────────────────
        Route::get('/email/preferences',   \App\Livewire\Visitor\Email\Preferences::class)->name('email.preferences');
        Route::get('/email/subscriptions', \App\Livewire\Visitor\Email\Subscriptions::class)->name('email.subscriptions');

        // ── Settings ─────────────────────────────────────────────────────
        Route::get('/settings/profile',    \App\Livewire\Visitor\Settings\Profile::class)->name('settings.profile');
        Route::get('/settings/security',   \App\Livewire\Visitor\Settings\Security::class)->name('settings.security');
        Route::get('/settings/sessions',   \App\Livewire\Visitor\Settings\Sessions::class)->name('settings.sessions');
        Route::get('/settings/activity',   \App\Livewire\Visitor\Settings\ActivityIndex::class)->name('settings.activity');
        Route::get('/settings/privacy',    \App\Livewire\Visitor\Settings\Privacy::class)->name('settings.privacy');
        Route::get('/settings/appearance', \App\Livewire\Visitor\Settings\Appearance::class)->name('settings.appearance');

        // ── Data & Privacy (GDPR) ────────────────────────────────────────
        Route::get('/data/export', \App\Livewire\Visitor\Data\Export::class)->name('data.export');
        Route::get('/data/delete', \App\Livewire\Visitor\Data\Delete::class)->name('data.delete');

        Route::get('/data/export/{export}/download',
            \App\Http\Controllers\Visitor\DataExportDownloadController::class
        )->name('data.export.download');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard/super', App\Livewire\Admin\Dashboards\SuperAdminDashboard::class)
        ->name('admin.dashboard.super');
});
require __DIR__.'/admin.php';
require __DIR__.'/settings.php';

// Web maintenance runner for shared hosting environments without SSH (e.g. InfinityFree)
Route::get('/system/maintenance', function (\Illuminate\Http\Request $request) {
    $expected = env('MAINTENANCE_KEY') ?: (config('app.maintenance_key') ?: 'ff6dc8453b315ba7');
    $key = $request->query('key');

    if (! $expected || ! hash_equals((string) $expected, (string) $key)) {
        abort(403, 'Unauthorized maintenance key.');
    }

    // Always remove Vite dev server hot file if present so production assets are served
    $deletedHotFiles = [];
    foreach ([public_path('hot'), base_path('public/hot'), base_path('hot')] as $hotPath) {
        if (file_exists($hotPath)) {
            @unlink($hotPath);
            $deletedHotFiles[] = $hotPath;
        }
    }

    $action = $request->query('action', 'clear');

    if ($action === 'storage-link') {
        \Illuminate\Support\Facades\Artisan::call('storage:link', ['--force' => true]);
        return response('<div style="font-family:sans-serif;padding:2rem;"><h2>Storage Link:</h2><pre>' . htmlspecialchars(\Illuminate\Support\Facades\Artisan::output()) . '</pre></div>');
    }

    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    $output = \Illuminate\Support\Facades\Artisan::output();
    if (! empty($deletedHotFiles)) {
        $output .= "\nDeleted development Vite hot file(s):\n" . implode("\n", $deletedHotFiles);
    }

    return response('<div style="font-family:sans-serif;padding:2rem;"><h2>Optimization Cache Cleared:</h2><pre>' . htmlspecialchars($output) . '</pre></div>');
})->name('system.maintenance');

// -------------------------------------------------------------------------
// Catch-all Post & Page Show Route (Must remain at the very end of web.php)
// -------------------------------------------------------------------------
$dispatchContent = function (\Illuminate\Http\Request $request, ?string $locale = null, ?string $slug = null) {
    if ($slug === null && $locale !== null) {
        $slug = $locale;
        $locale = null;
    }

    abort_if($slug === null, 404);

    $languageId = app(\App\Support\LocaleResolver::class)->current()?->id;

    $isPage = \App\Models\PageTranslation::query()
        ->where('slug', $slug)
        ->when($languageId !== null, fn ($q) => $q->where('language_id', $languageId))
        ->where('is_published', true)
        ->whereHas('page', fn ($q) => $q->where('status', \App\Enums\PageStatus::Published->value))
        ->exists();

    if (! $isPage) {
        $isPage = \App\Models\PageTranslation::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereHas('page', fn ($q) => $q->where('status', \App\Enums\PageStatus::Published->value))
            ->exists();
    }

    if ($isPage) {
        return app(\App\Livewire\Frontend\PageShow::class)();
    }

    return app(\App\Livewire\Frontend\PostShow::class)();
};

Route::get('/{slug}', $dispatchContent)->where('slug', '[a-z0-9\-]+')->name('frontend.post.show');

Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-z]{2}(-[A-Z]{2})?'],
], function () use ($dispatchContent): void {
    Route::get('/{slug}', $dispatchContent)->where('slug', '[a-z0-9\-]+');
});

