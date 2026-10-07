<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\AdCreative;
use App\Models\AdZone;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Comment;
use App\Models\ImportSource;
use App\Models\Language;
use App\Models\LoginLog;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\User;
use App\Notifications\Editorial\PostApproved;
use App\Notifications\Editorial\PostPublishedNotification;
use App\Notifications\Editorial\PostSubmittedForReview;
use App\Notifications\Newsletter\NewSubscriberNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Rich demo seed for Blogger4U — populates a fresh install with
 * everything a buyer needs to see the product working at a glance:
 *
 *   • 8 staff users (one per role, plus 3 prolific authors)
 *   • 6 categories with EN + BN translations
 *   • 50 tags with EN + BN translations
 *   • 30 posts spanning every status (published, draft, scheduled,
 *     pending, approved, archived) with breaking / featured / trending
 *     flags sprinkled in
 *   • 4 static pages (About / Contact / Privacy / Terms)
 *   • ~30 comments (mostly approved, some pending + spam)
 *   • 10 newsletter subscribers (mix of confirmed/pending/unsubscribed)
 *   • 4 ad zones with 8 creatives
 *   • 3 RSS import sources
 *
 * Idempotent within reason: re-running the seeder updates the 8 named
 * staff users in place but adds a fresh batch of content rows. For a
 * truly clean re-seed, run `migrate:fresh --seed`.
 */
class RupantrixDemoSeeder extends Seeder
{
    /** @var array<string, User> */
    protected array $staff = [];

    /** @var array<string, Category> */
    protected array $categories = [];

    /** @var array<int, Tag> */
    protected array $tags = [];

    /** @var Language */
    protected Language $english;

    /** @var Language */
    protected Language $bangla;

    public function run(): void
    {
        $this->english = Language::query()->where('code', 'en')->firstOrFail();
        $this->bangla = Language::query()->where('code', 'bn')->firstOrFail();

        $this->command?->info('Blogger4U demo seeder');
        $this->command?->info('─────────────────────────────────────────────');

        $this->step('staff users',     fn () => $this->seedStaff());
        $this->step('media library',   fn () => $this->seedMedia());
        $this->step('categories',      fn () => $this->seedCategories());
        $this->step('tags',            fn () => $this->seedTags());
        $this->step('static pages',    fn () => $this->seedPages());
        $this->step('posts',           fn () => $this->seedPosts());
        $this->step('comments',        fn () => $this->seedComments());
        $this->step('newsletter',      fn () => $this->seedNewsletter());
        $this->step('ad zones',        fn () => $this->seedAds());
        $this->step('RSS sources',     fn () => $this->seedRssSources());
        $this->step('login logs',      fn () => $this->seedLoginLogs());
        $this->step('notifications',   fn () => $this->seedSampleNotifications());

        $this->command?->info('─────────────────────────────────────────────');
        $this->command?->info('Blogger4U demo seeding complete.');
    }

    /**
     * Run a labelled seeder step with timing + a uniform log prefix.
     * Keeps the seed output legible during long runs.
     */
    private function step(string $label, \Closure $fn): void
    {
        $start = microtime(true);
        $fn();
        $elapsed = number_format((microtime(true) - $start) * 1000, 0);
        $this->command?->info(sprintf('  ✓ %-18s %sms', $label, $elapsed));
    }

    // -----------------------------------------------------------------
    // Staff users — one per role + a handful of prolific authors.
    // -----------------------------------------------------------------

    private function seedStaff(): void
    {
        // Platform Super Administrators
        $this->staff['aashish_1'] = $this->staffUser(
            'mr.aashishpathak.official@gmail.com', 'Aashish Pathak', 'Super Admin',
            'Platform Super Administrator & Lead Engineer.',
            'media/2021/07/business-blog-team-member-img-4.png',
            '$2y$12$H4LPA97zq1BLbsOdO/k.4usCh19E4AoRwOb/5nQx6UCmIhhoMaqja',
            '08595319558',
            'aashishpathak'
        );
        $this->staff['aashish_2'] = $this->staffUser(
            'intruder.known.aashu@gmail.com', 'Aashish Pathak', 'Super Admin',
            'Platform Super Administrator.',
            'media/2021/07/business-blog-team-member-img-1.png',
            '$2y$12$nlyGp/v0oa.J4hUN8PxrQerLk.416HL5JeVi4unJu5.KgqJeQQs2i',
            '08595319558',
            'aashishpathak-admin'
        );

        $this->staff['super_admin'] = $this->staffUser(
            'superadmin@blogger4u.test', 'Super Admin', 'Super Admin',
            'AI-powered editor-in-chief running the show.',
            'media/2021/07/business-blog-team-member-img-2.png'
        );
        $this->staff['admin'] = $this->staffUser(
            'admin@blogger4u.test', 'Aaliyah Khan', 'Admin',
            'Newsroom administrator overseeing day-to-day operations.',
            'media/2021/07/business-blog-team-member-img-3.png'
        );
        $this->staff['editor'] = $this->staffUser(
            'editor@blogger4u.test', 'Marcus Hale', 'Editor',
            'Senior editor — politics & business desk.',
            'media/2021/07/business-blog-team-member-img-5.png'
        );
        $this->staff['author_1'] = $this->staffUser(
            'jane.reporter@blogger4u.test', 'Jane Reporter', 'Author',
            'Tech & AI reporter. Always shipping.',
            'media/2021/07/business-blog-team-member-img-6.png'
        );
        $this->staff['author_2'] = $this->staffUser(
            'sami.writer@blogger4u.test', 'Sami Wright', 'Author',
            'Culture and lifestyle features writer.',
            'media/2021/07/business-blog-team-member-img-7.png'
        );
        $this->staff['author_3'] = $this->staffUser(
            'leo.chen@blogger4u.test', 'Leo Chen', 'Author',
            'Business and markets correspondent.',
            'media/2021/07/business-blog-team-member-img-8.png'
        );
        $this->staff['ad_manager'] = $this->staffUser(
            'ads@blogger4u.test', 'Priya Sharma', 'Ad Manager',
            'Runs sponsorships and monetization.',
            'media/2021/07/business-blog-team-member-img-1.png'
        );
        $this->staff['seo'] = $this->staffUser(
            'seo@blogger4u.test', 'Diego Ortiz', 'SEO Manager',
            'Owns sitemaps, schema, and search ranking.',
            'media/2021/07/business-blog-team-member-img-2.png'
        );

        // Additional reporters to flesh out the leaderboard, kanban, and
        // author leaderboard widgets. Each gets the Author role.
        $extraAuthors = [
            ['email' => 'olivia.brooks@blogger4u.test', 'name' => 'Olivia Brooks', 'bio' => 'Climate + science correspondent.', 'avatar' => 'media/2021/07/business-blog-team-member-img-3.png'],
            ['email' => 'rafiq.hassan@blogger4u.test', 'name' => 'Rafiq Hassan', 'bio' => 'Politics desk, South Asia bureau.', 'avatar' => 'media/2021/07/business-blog-team-member-img-4.png'],
            ['email' => 'nora.kowalski@blogger4u.test', 'name' => 'Nora Kowalski', 'bio' => 'EU economics + trade.', 'avatar' => 'media/2021/07/business-blog-team-member-img-5.png'],
            ['email' => 'kenji.tanaka@blogger4u.test', 'name' => 'Kenji Tanaka', 'bio' => 'Tokyo-based tech and gaming reporter.', 'avatar' => 'media/2021/07/business-blog-team-member-img-6.png'],
            ['email' => 'amara.okafor@blogger4u.test', 'name' => 'Amara Okafor', 'bio' => 'Lagos correspondent — African business.', 'avatar' => 'media/2021/07/business-blog-team-member-img-7.png'],
            ['email' => 'sofia.lopez@blogger4u.test', 'name' => 'Sofia Lopez', 'bio' => 'Latin America politics.', 'avatar' => 'media/2021/07/business-blog-team-member-img-8.png'],
            ['email' => 'tomas.berger@blogger4u.test', 'name' => 'Tomas Berger', 'bio' => 'Sports reporter, multilingual.', 'avatar' => 'media/2021/07/business-blog-team-member-img-1.png'],
        ];
        foreach ($extraAuthors as $idx => $a) {
            $this->staff['author_extra_'.$idx] = $this->staffUser(
                $a['email'], $a['name'], 'Author', $a['bio'], $a['avatar'],
            );
        }
    }

    private function staffUser(
        string $email,
        string $name,
        string $roleName,
        string $bio = '',
        ?string $avatar = null,
        ?string $passwordHash = null,
        ?string $phone = null,
        ?string $publicSlug = null
    ): User {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $passwordHash ?: Hash::make('password'),
                'email_verified_at' => now(),
                'bio' => $bio,
                'avatar' => $avatar,
                'phone' => $phone,
                'portal_type' => in_array($roleName, ['Super Admin', 'Admin']) ? 'admin' : 'author',
                'public_slug' => $publicSlug ?: Str::slug($name),
                'show_in_team' => true,
                'social_links' => [
                    'twitter' => '@'.Str::slug($name, ''),
                    'linkedin' => Str::slug($name),
                ],
            ],
        );

        $role = Role::query()->firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);
        $roles = [$role->name];
        if ($roleName === 'Super Admin') {
            Role::query()->firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $roles[] = 'Admin';
        }
        $user->syncRoles($roles);

        return $user->fresh();
    }

    // -----------------------------------------------------------------
    // Categories — 6 top-level, each with EN + BN names.
    // -----------------------------------------------------------------

    private function seedCategories(): void
    {
        $blueprint = [
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

        $mediaIds = Media::query()->pluck('id')->all();
        $mediaCount = count($mediaIds);

        foreach ($blueprint as $index => $entry) {
            $category = Category::query()->updateOrCreate(
                ['icon' => $entry['icon']],
                [
                    'image_id' => $mediaCount > 0 ? $mediaIds[$index % $mediaCount] : null,
                    'show_in_menu' => true,
                    'show_on_homepage' => true,
                    'is_featured' => $entry['is_featured'],
                    'sort_order' => $index + 1,
                    'color' => $entry['color'],
                    'layout' => Category::LAYOUT_GRID,
                ],
            );

            $this->upsertCategoryTranslation($category, $this->english, ['name' => $entry['name'], 'slug' => $entry['slug']]);

            $this->categories[$entry['key']] = $category->fresh('translations');
        }
    }

    /**
     * @param  array{name: string, slug: string}  $data
     */
    private function upsertCategoryTranslation(Category $category, Language $lang, array $data): void
    {
        CategoryTranslation::query()->updateOrCreate(
            ['category_id' => $category->id, 'language_id' => $lang->id],
            [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => 'Latest '.$data['name'].' coverage from Blogger4U.',
                'meta_title' => $data['name'].' — Blogger4U',
                'meta_description' => 'Read the latest '.$data['name'].' news, analysis, and opinion on Blogger4U.',
            ],
        );
    }

    // -----------------------------------------------------------------
    // Tags — 50 tags spanning trending + evergreen topics.
    // -----------------------------------------------------------------

    private function seedTags(): void
    {
        $names = [
            'AI', 'OpenAI', 'Gemini', 'LLM', 'Robotics', 'Cybersecurity',
            'Climate', 'Renewable Energy', 'Electric Vehicles', 'Space',
            'Apple', 'Google', 'Microsoft', 'Meta', 'NVIDIA',
            'Bitcoin', 'Ethereum', 'Stocks', 'Startups', 'Venture Capital',
            'Elections', 'Diplomacy', 'EU', 'United Nations', 'Trade',
            'Cinema', 'Music', 'Books', 'Theatre', 'Art',
            'Football', 'Cricket', 'Tennis', 'Olympics', 'Formula 1',
            'Health', 'Mental Health', 'Fitness', 'Nutrition', 'Vaccines',
            'Travel', 'Food', 'Fashion', 'Education', 'Universities',
            'Bangladesh', 'India', 'USA', 'China', 'Middle East',
        ];

        $adminId = $this->staff['admin']->id;

        foreach ($names as $idx => $name) {
            $slug = Str::slug($name);
            $tag = Tag::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'code' => Str::upper(substr($slug, 0, 3)).'-'.str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT),
                    'type' => Tag::TYPE_GENERAL,
                    'status' => Tag::STATUS_PUBLISHED,
                    'color' => fake()->hexColor(),
                    // tags.created_by / updated_by are NOT NULL FKs to users.
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ],
            );

            TagTranslation::query()->updateOrCreate(
                ['tag_id' => $tag->id, 'language_id' => $this->english->id],
                ['name' => $name, 'slug' => $slug],
            );

            $this->tags[] = $tag;
        }
    }

    // -----------------------------------------------------------------
    // Pages — About / Contact / Privacy / Terms.
    // -----------------------------------------------------------------

    private function seedPages(): void
    {
        $blueprint = [
            ['slug' => 'about', 'title' => 'About Blogger4U', 'menu' => true, 'order' => 1, 'body' => $this->aboutPageBody()],
            ['slug' => 'contact', 'title' => 'Contact Us', 'menu' => true, 'order' => 2, 'body' => $this->contactPageBody()],
            ['slug' => 'privacy', 'title' => 'Privacy Policy', 'menu' => false, 'order' => 3, 'body' => $this->privacyPageBody()],
            ['slug' => 'terms', 'title' => 'Terms of Service', 'menu' => false, 'order' => 4, 'body' => $this->termsPageBody()],
        ];

        foreach ($blueprint as $entry) {
            $existing = PageTranslation::query()
                ->where('slug', $entry['slug'])
                ->where('language_id', $this->english->id)
                ->first();

            $page = $existing?->page ?? Page::query()->create([
                'status' => PageStatus::Published->value,
                'template' => Page::TEMPLATE_DEFAULT,
                'show_in_menu' => $entry['menu'],
                'sort_order' => $entry['order'],
            ]);

            PageTranslation::query()->updateOrCreate(
                ['page_id' => $page->id, 'language_id' => $this->english->id],
                [
                    'title' => $entry['title'],
                    'slug' => $entry['slug'],
                    'content' => $entry['body'],
                    'meta_title' => $entry['title'].' — Blogger4U',
                    'meta_description' => Str::limit(strip_tags($entry['body']), 155),
                    'is_published' => true,
                ],
            );
        }
    }

    private function aboutPageBody(): string
    {
        return "<h2>About Blogger4U</h2>\n".
            "<p>Blogger4U is a modern, AI-assisted news and magazine CMS built on Laravel 13 and Livewire 4. ".
            "We help editorial teams ship faster — from brainstorming a headline to publishing the article — with ".
            "the help of OpenAI and Gemini integrations baked into every step of the workflow.</p>\n".
            "<p>This demo content is fully editable. Sign in as <code>admin@blogger4u.test</code> (password: <code>password</code>) ".
            "to explore the admin.</p>";
    }

    private function contactPageBody(): string
    {
        return "<h2>Get in touch</h2>\n".
            "<p>Editorial: <a href=\"mailto:editor@blogger4u.test\">editor@blogger4u.test</a></p>\n".
            "<p>Advertising: <a href=\"mailto:ads@blogger4u.test\">ads@blogger4u.test</a></p>";
    }

    private function privacyPageBody(): string
    {
        return "<h2>Privacy Policy</h2>\n".
            "<p>This is a placeholder Privacy Policy shipped with the Blogger4U demo. Replace it with the wording your ".
            "lawyers approve before going live.</p>";
    }

    private function termsPageBody(): string
    {
        return "<h2>Terms of Service</h2>\n".
            "<p>This is a placeholder Terms of Service shipped with the Blogger4U demo.</p>";
    }

    // -----------------------------------------------------------------
    // Posts — 30 posts, mixed statuses + flags. Each is anchored to a
    // hand-picked headline so screenshots don't look like Lorem Ipsum.
    // -----------------------------------------------------------------

    private function seedPosts(): void
    {
        // Idempotency guard — if we already have a healthy catalogue
        // skip re-creating posts. Use `migrate:fresh --seed` for a
        // truly clean reset.
        $existingPosts = Post::query()->count();
        if ($existingPosts >= 60) {
            $this->command?->getOutput()?->writeln(sprintf(
                '    (skipped — %d posts already present)', $existingPosts,
            ));

            return;
        }

        // Pull every staff member that has the Author role plus the
        // editor so the leaderboards have a healthy spread of authors.
        $authors = collect($this->staff)
            ->filter(fn (User $u) => $u->roles->pluck('name')->intersect(['Author', 'Editor', 'Admin'])->isNotEmpty())
            ->values()
            ->all();

        $categoryKeys = array_keys($this->categories);
        $mediaIds = Media::query()->pluck('id')->all();
        $mediaCount = count($mediaIds);
        $getMediaId = fn (int $offset) => $mediaCount > 0 ? $mediaIds[$offset % $mediaCount] : null;

        // 20 hand-written published headlines — the ones that fill the
        // homepage. Each one is deterministic so screenshots are stable.
        foreach ($this->publishedHeadlines() as $i => $entry) {
            $post = Post::factory()
                ->published()
                ->withAuthor($authors[$i % count($authors)]->id)
                ->state([
                    'category_id' => $this->categories[$entry['cat']]->id,
                    'featured_image_id' => $getMediaId($i),
                    'type' => $entry['type'] ?? PostType::Post,
                    'is_featured' => $entry['featured'] ?? false,
                    'is_breaking' => $entry['breaking'] ?? false,
                    'is_trending' => $entry['trending'] ?? false,
                    'is_editors_pick' => $entry['pick'] ?? false,
                    'published_at' => now()->subHours($i * 6 + 1),
                    'breaking_expires_at' => ($entry['breaking'] ?? false) ? now()->addHours(12) : null,
                    'view_count' => fake()->numberBetween(200, 25_000),
                ])
                ->withoutTranslations()
                ->create();

            $this->createPostTranslations($post, $entry, true);
            $this->attachRandomTags($post);
        }

        // 30 extra factory-generated published posts (bumped from 20)
        // so the catalogue crosses ~50 published items — content
        // analytics, calendar, and trending widgets all look healthy.
        // Publish times are spread across the last 90 days, with a
        // weighted recency bias so the latest tab is always fresh.
        for ($i = 0; $i < 30; $i++) {
            $cat = $categoryKeys[$i % count($categoryKeys)];
            $author = $authors[($i + 7) % count($authors)];
            // Mix close-in and back-catalogue dates: first half within
            // the past 5 days, second half stretching back 90 days.
            $publishedAt = $i < 15
                ? now()->subHours(2 + $i * 6)
                : now()->subDays(5 + ($i - 15) * 6 + fake()->numberBetween(0, 4));
            $post = Post::factory()
                ->published()
                ->withAuthor($author->id)
                ->state([
                    'category_id' => $this->categories[$cat]->id,
                    'featured_image_id' => $getMediaId($i + 20),
                    'is_trending' => $i % 7 === 0,
                    'is_featured' => $i % 11 === 0,
                    'is_editors_pick' => $i % 13 === 0,
                    'published_at' => $publishedAt,
                    'view_count' => fake()->numberBetween(100, 12_000),
                ])
                ->create();
            $this->attachRandomTags($post);
        }

        // 8 scheduled posts spread across the next 3 weeks — populates
        // the calendar with stuff to look at.
        for ($i = 0; $i < 8; $i++) {
            $cat = $categoryKeys[array_rand($categoryKeys)];
            $author = $authors[$i % count($authors)];
            $post = Post::factory()
                ->scheduled(now()->addDays($i + 1)->setTime(8 + ($i % 9), 0))
                ->withAuthor($author->id)
                ->state([
                    'category_id' => $this->categories[$cat]->id,
                    'featured_image_id' => $getMediaId($i + 50),
                ])
                ->create();
            $this->attachRandomTags($post);
        }

        // 6 pending review — fills the Kanban "In Review" column from
        // multiple authors so the queue looks like real teamwork.
        for ($i = 0; $i < 6; $i++) {
            $author = $authors[$i % count($authors)];
            $cat = $categoryKeys[$i % count($categoryKeys)];
            $post = Post::factory()
                ->pendingReview()
                ->withAuthor($author->id)
                ->state([
                    'category_id' => $this->categories[$cat]->id,
                    'featured_image_id' => $getMediaId($i + 58),
                ])
                ->create();
            $this->attachRandomTags($post);
        }

        // 5 drafts per the prolific authors — feeds the "My drafts" widget.
        $draftAuthors = [
            $this->staff['author_1'],
            $this->staff['author_2'],
            $this->staff['author_3'],
            $this->staff['author_extra_0'],
            $this->staff['author_extra_1'],
        ];
        foreach ($draftAuthors as $idx => $author) {
            Post::factory()
                ->draft()
                ->withAuthor($author->id)
                ->state([
                    'category_id' => $this->categories[$categoryKeys[$idx % count($categoryKeys)]]->id,
                    'featured_image_id' => $getMediaId($idx + 64),
                ])
                ->create();
        }

        // 3 archived — proves the archive screen has content.
        for ($i = 0; $i < 3; $i++) {
            Post::factory()
                ->state([
                    'status' => PostStatus::Archived->value,
                    'category_id' => $this->categories['software-development']->id,
                    'featured_image_id' => $getMediaId($i + 70),
                ])
                ->withAuthor($authors[$i % count($authors)]->id)
                ->create();
        }
    }

    /**
     * @return list<array{cat: string, en: array{title: string, excerpt: string}, bn?: array{title: string}, featured?: bool, breaking?: bool, trending?: bool, pick?: bool, type?: PostType}>
     */
    private function publishedHeadlines(): array
    {
        return [
            ['cat' => 'generative-ai', 'en' => ['title' => 'OpenAI unveils GPT-5 with multimodal reasoning', 'excerpt' => 'The new flagship model adds real-time vision, longer context windows, and tool-use that finally rivals human reasoning.'], 'bn' => ['title' => 'ওপেনএআই উন্মোচন করল মাল্টিমোডাল রিজনিং সম্পন্ন GPT-5'], 'breaking' => true, 'featured' => true, 'trending' => true],
            ['cat' => 'emerging-technology', 'en' => ['title' => 'Quantum error correction reaches threshold milestone for scalable computing', 'excerpt' => 'Forty-three physical qubits were successfully entangled in a fault-tolerant logical architecture.'], 'featured' => true, 'pick' => true],
            ['cat' => 'artificial-intelligence', 'en' => ['title' => 'Apple expands on-device Neural Engine chips across entire M5 hardware lineup', 'excerpt' => 'Investors cheered the Cupertino hardware roadmap bringing local 70B parameter models to consumer laptops.'], 'trending' => true],
            ['cat' => 'web-development', 'en' => ['title' => 'Modern Web Architecture: Building edge-rendered reactive apps in 2026', 'excerpt' => 'From server components to sub-millisecond edge hydration, how production teams are rethinking web apps.'], 'pick' => true],
            ['cat' => 'ai-agents', 'en' => ['title' => 'Autonomous Coding Agents: Moving from toy demonstrations to production CI/CD loops', 'excerpt' => 'Self-healing deployments and automated pull request generation are reshaping developer throughput.'], 'breaking' => true, 'trending' => true],
            ['cat' => 'generative-ai', 'en' => ['title' => 'Google merges Search and Gemini into unified multimodal interface', 'excerpt' => 'The rebranded experience replaces traditional link lists with dynamic generative synthesis panels.']],
            ['cat' => 'software-development', 'en' => ['title' => 'Designing Resilient Distributed Systems: Lessons from 10 years of scale', 'excerpt' => 'Event sourcing, CQRS, and zero-downtime database migrations unpacked with practical architectural patterns.']],
            ['cat' => 'blockchain-web3', 'en' => ['title' => 'Zero-Knowledge rollups achieve sub-cent settlement on Ethereum Layer 2', 'excerpt' => 'Decentralized liquidity protocols celebrate massive throughput jumps as proving time drops 90%.']],
            ['cat' => 'artificial-intelligence', 'en' => ['title' => 'EU drafts AI Safety and Frontier Model Directive ahead of parliament vote', 'excerpt' => 'The legislation establishes safety evaluations and reporting requirements for frontier training runs.']],
            ['cat' => 'ai-engineering', 'en' => ['title' => 'NVIDIA Blackwell Ultra GPUs accelerate mixture-of-experts inference by 4x', 'excerpt' => 'Hyperscalers ramp up cluster deployments with custom liquid cooling and NVLink 5 switches.'], 'trending' => true],
            ['cat' => 'cloud-devops', 'en' => ['title' => 'Platform Engineering with Kubernetes: Ephemeral preview environments at scale', 'excerpt' => 'How leading engineering teams enable instant per-commit preview environments in seconds.']],
            ['cat' => 'cybersecurity', 'en' => ['title' => 'Post-Quantum Cryptography: Migrating enterprise TLS to Kyber and Dilithium', 'excerpt' => 'NIST standardizes quantum-resistant algorithms as cloud providers roll out hybrid key exchange.']],
            ['cat' => 'blockchain-web3', 'en' => ['title' => 'Bitcoin and decentralized timestamping protocols gain institutional adoption', 'excerpt' => 'Sovereign wealth funds and corporate treasuries allocate to decentralized ledger infrastructure.']],
            ['cat' => 'data-analytics', 'en' => ['title' => 'Real-Time Vector Databases: Benchmarking Milvus, Pinecone, and pgvector at 100M scales', 'excerpt' => 'HNSW vs IVFFlat indexing strategies examined under extreme query concurrency.'], 'pick' => true],
            ['cat' => 'software-development', 'en' => ['title' => 'Clean Code and Modern PHP: Leveraging Strict Types and Enums in Enterprise Apps', 'excerpt' => 'How modern static analysis and type systems eliminate runtime regressions before production.']],
            ['cat' => 'artificial-intelligence', 'en' => ['title' => 'Anthropic releases Claude 4.5 Sonnet with extended cognitive deliberation', 'excerpt' => 'Benchmark scores establish new state-of-the-art results across SWE-bench and coding competitions.']],
            ['cat' => 'ai-agents', 'en' => ['title' => 'Multi-Agent Orchestration with LangGraph and CrewAI: Patterns for real-world reliability', 'excerpt' => 'Designing deterministic supervision loops for non-deterministic LLM agents in production.']],
            ['cat' => 'web-development', 'en' => ['title' => 'CSS in 2026: Subgrid, Anchor Positioning, and View Transitions without JavaScript', 'excerpt' => 'A showcase of modern CSS capabilities that make bulky animation libraries obsolete.']],
            ['cat' => 'emerging-technology', 'en' => ['title' => 'Neuromorphic chips demonstrate 100x efficiency gains for edge robotics', 'excerpt' => 'Spiking neural networks running on event-based silicon enable real-time robotic navigation with milliwatts.']],
            ['cat' => 'llms-nlp', 'en' => ['title' => 'Context Caching and FlashAttention 3: Slashing inference latency in long-form generation', 'excerpt' => 'How memory-efficient attention algorithms enable million-token contexts without quadratic slowdowns.']],
        ];
    }

    /**
     * Create EN + (optionally) BN translations for a freshly minted post.
     */
    private function createPostTranslations(Post $post, array $entry, bool $published): void
    {
        $enSlug = Str::slug($entry['en']['title']);
        PostTranslation::query()->create([
            'post_id' => $post->id,
            'language_id' => $this->english->id,
            'title' => $entry['en']['title'],
            'slug' => $enSlug.'-'.fake()->unique()->numerify('###'),
            'excerpt' => $entry['en']['excerpt'],
            'content' => $this->generatePostBody($entry['en']['title'], $entry['en']['excerpt']),
            'reading_time' => fake()->numberBetween(3, 10).' min read',
            'is_published' => $published,
            'translation_status' => 'manual',
        ]);

        if (! empty($entry['bn'])) {
            $bnSlug = Str::slug($entry['bn']['title']);
            PostTranslation::query()->create([
                'post_id' => $post->id,
                'language_id' => $this->bangla->id,
                'title' => $entry['bn']['title'],
                'slug' => ($bnSlug !== '' ? $bnSlug : 'post-bn-'.$post->id).'-'.fake()->unique()->numerify('###'),
                'excerpt' => $entry['en']['excerpt'],
                'content' => "<p>{$entry['bn']['title']}</p>\n<p>".$entry['en']['excerpt'].'</p>',
                'is_published' => $published,
                'translation_status' => 'ai_generated',
            ]);
        }
    }

    private function generatePostBody(string $title, string $excerpt): string
    {
        return "<h2>{$title}</h2>\n".
            "<p><em>{$excerpt}</em></p>\n".
            '<p>'.fake()->paragraph(6)."</p>\n".
            '<h3>Key takeaways</h3>'."\n".
            '<ul>'."\n".
            '  <li>'.fake()->sentence(12).'</li>'."\n".
            '  <li>'.fake()->sentence(12).'</li>'."\n".
            '  <li>'.fake()->sentence(12).'</li>'."\n".
            '</ul>'."\n".
            '<p>'.fake()->paragraph(8).'</p>'."\n".
            '<blockquote>'.fake()->sentence(16).'</blockquote>'."\n".
            '<p>'.fake()->paragraph(5).'</p>';
    }

    private function attachRandomTags(Post $post): void
    {
        $picks = collect($this->tags)->random(fake()->numberBetween(2, 5));
        $post->tags()->syncWithoutDetaching(
            $picks->mapWithKeys(fn (Tag $tag): array => [$tag->id => ['created_at' => now()]])->all()
        );
    }

    // -----------------------------------------------------------------
    // Comments — ~30 across the 20 published posts.
    // -----------------------------------------------------------------

    private function seedComments(): void
    {
        // Idempotency guard — skip when we already have a healthy
        // comment volume so the seeder is safe to re-run.
        $existing = Comment::query()->count();
        if ($existing >= 60) {
            $this->command?->getOutput()?->writeln(sprintf(
                '    (skipped — %d comments already present)', $existing,
            ));

            return;
        }

        $publishedPosts = Post::query()
            ->where('status', PostStatus::Published->value)
            ->take(50)
            ->get();

        if ($publishedPosts->isEmpty()) {
            return;
        }

        // ~80 comments total (bumped from 50), weighted to feel like a
        // healthy thread:
        //   60 approved (~75%)
        //   14 pending (~17%)
        //    6 spam     (~8%)
        // Wrap in a transaction so factory writes are committed in one
        // go — noticeably faster than 80 individual statements.
        \DB::transaction(function () use ($publishedPosts): void {
            Comment::factory()
                ->count(60)
                ->approved()
                ->state(fn () => ['post_id' => $publishedPosts->random()->id])
                ->create();

            Comment::factory()
                ->count(14)
                ->pending()
                ->state(fn () => ['post_id' => $publishedPosts->random()->id])
                ->create();

            Comment::factory()
                ->count(6)
                ->spam()
                ->state(fn () => ['post_id' => $publishedPosts->random()->id])
                ->create();
        });

        // Backfill each post's denormalised comment_count so the
        // dashboard tiles and "top by comments" widget render real
        // numbers instead of zero.
        foreach ($publishedPosts as $post) {
            $post->comment_count = Comment::query()->where('post_id', $post->id)->count();
            $post->save();
        }
    }

    // -----------------------------------------------------------------
    // Newsletter — 10 subscribers across all statuses.
    // -----------------------------------------------------------------

    private function seedNewsletter(): void
    {
        // Idempotency guard — top up only if we're below the target.
        $existing = NewsletterSubscriber::query()->count();
        if ($existing >= 70) {
            $this->command?->getOutput()?->writeln(sprintf(
                '    (skipped — %d subscribers already present)', $existing,
            ));

            return;
        }

        // ~80 subscribers (bumped from 50). Same healthy-list shape:
        //   56 confirmed   (70%)
        //   14 pending     (18%)
        //   10 unsubscribed (12%)
        \DB::transaction(function (): void {
            NewsletterSubscriber::factory()->count(56)->confirmed()->create();
            NewsletterSubscriber::factory()->count(14)->create();
            NewsletterSubscriber::factory()->count(10)->unsubscribed()->create();
        });
    }

    // -----------------------------------------------------------------
    // Ad zones + creatives.
    // -----------------------------------------------------------------

    private function seedAds(): void
    {
        $zones = [
            ['key' => 'header_leaderboard',  'name' => 'Header Leaderboard',    'position' => AdZone::POSITION_TOP,     'w' => 728, 'h' => 90,  'max' => 1],
            ['key' => 'header_mobile',       'name' => 'Header Mobile Banner',  'position' => AdZone::POSITION_TOP,     'w' => 320, 'h' => 50,  'max' => 1],
            ['key' => 'post_inline',         'name' => 'In-Article Banner',     'position' => AdZone::POSITION_INLINE,  'w' => 728, 'h' => 90,  'max' => 2],
            ['key' => 'post_inline_2',       'name' => 'In-Article #2',         'position' => AdZone::POSITION_INLINE,  'w' => 300, 'h' => 600, 'max' => 1],
            ['key' => 'sidebar_box',         'name' => 'Sidebar Medium Rect',   'position' => AdZone::POSITION_SIDEBAR, 'w' => 300, 'h' => 250, 'max' => 3],
            ['key' => 'sidebar_skyscraper',  'name' => 'Sidebar Skyscraper',    'position' => AdZone::POSITION_SIDEBAR, 'w' => 160, 'h' => 600, 'max' => 1],
            ['key' => 'footer_banner',       'name' => 'Footer Banner',         'position' => AdZone::POSITION_FOOTER,  'w' => 970, 'h' => 250, 'max' => 1],
            ['key' => 'popup_interstitial',  'name' => 'Interstitial Popup',    'position' => AdZone::POSITION_POPUP,   'w' => 600, 'h' => 400, 'max' => 1],
        ];

        $createdZones = [];
        foreach ($zones as $z) {
            $createdZones[] = AdZone::query()->updateOrCreate(
                ['key' => $z['key']],
                [
                    'name' => $z['name'],
                    'description' => $z['name'].' placement.',
                    'width' => $z['w'],
                    'height' => $z['h'],
                    'position' => $z['position'],
                    'is_active' => true,
                    'max_creatives' => $z['max'],
                ],
            );
        }

        // Idempotency guard — creatives accumulate quickly because we
        // don't have a stable upsert key. Skip when we've already
        // seeded a healthy number across the zones.
        if (AdCreative::query()->count() >= 30) {
            return;
        }

        // 5 creatives per zone (8 zones × 5 = 40 creatives) — mix of
        // active / paused / expired so admins see the full lifecycle.
        $sponsors = [
            ['name' => 'Blogger4U Cloud — Launch your AI newsroom in minutes', 'url' => 'https://cloud.blogger4u.test/launch'],
            ['name' => 'Anthropic API — Build with Claude',                    'url' => 'https://anthropic.example/api'],
            ['name' => 'Wired Magazine — Subscribe today',                     'url' => 'https://wired.example/subscribe'],
            ['name' => 'AWS — Free Tier for Startups',                         'url' => 'https://aws.example/startups'],
            ['name' => 'Coursera — Master AI in 6 weeks',                      'url' => 'https://coursera.example/ai'],
            ['name' => 'Stripe — Payments for content creators',               'url' => 'https://stripe.example/creators'],
            ['name' => 'GitHub Copilot — Code with AI',                        'url' => 'https://github.example/copilot'],
            ['name' => 'Bloomberg Terminal — 30-day trial',                    'url' => 'https://bloomberg.example/trial'],
            ['name' => 'Notion AI — Smarter notes for teams',                  'url' => 'https://notion.example/ai'],
            ['name' => 'DigitalOcean — Easy cloud servers',                    'url' => 'https://do.example/'],
            ['name' => 'Vercel — Frontend cloud',                              'url' => 'https://vercel.example/'],
            ['name' => 'Cloudflare — Speed up your site',                      'url' => 'https://cf.example/'],
            ['name' => 'Mailchimp — Email automation',                         'url' => 'https://mailchimp.example/'],
            ['name' => 'HubSpot — Marketing platform',                         'url' => 'https://hubspot.example/'],
            ['name' => 'Linear — Modern issue tracking',                       'url' => 'https://linear.example/'],
        ];
        $idx = 0;
        foreach ($createdZones as $zoneIndex => $zone) {
            for ($k = 0; $k < 5; $k++) {
                $sponsor = $sponsors[$idx % count($sponsors)];
                $idx++;

                // 70% active, 20% paused, 10% expired.
                $factory = match (true) {
                    $k === 4 && $zoneIndex % 3 === 0 => AdCreative::factory()->expired(),
                    $k >= 3 => AdCreative::factory()->paused(),
                    default => AdCreative::factory()->active(),
                };

                $factory->create([
                    'zone_id' => $zone->id,
                    'name' => $sponsor['name'],
                    'target_url' => $sponsor['url'],
                    'alt_text' => $sponsor['name'],
                    'priority' => 100 + $k * 25,
                    'impression_count' => fake()->numberBetween(500, 80_000),
                    'click_count' => fake()->numberBetween(10, 3_500),
                ]);
            }
        }
    }

    // -----------------------------------------------------------------
    // RSS import sources — 3 typical news feeds.
    // -----------------------------------------------------------------

    private function seedRssSources(): void
    {
        $sources = [
            ['name' => 'TechCrunch AI & Enterprise', 'url' => 'https://techcrunch.com/category/artificial-intelligence/feed/', 'auto' => true,  'cat' => 'artificial-intelligence', 'status' => ImportSource::STATUS_ACTIVE],
            ['name' => 'Hacker News RSS',           'url' => 'https://news.ycombinator.com/rss',                                 'auto' => false, 'cat' => 'software-development',      'status' => ImportSource::STATUS_PAUSED],
            ['name' => 'Wired Technology',          'url' => 'https://www.wired.com/feed/category/gear/latest/rss',             'auto' => false, 'cat' => 'emerging-technology',       'status' => ImportSource::STATUS_ACTIVE],
            ['name' => 'The Verge Tech',            'url' => 'https://www.theverge.com/rss/index.xml',                           'auto' => true,  'cat' => 'generative-ai',             'status' => ImportSource::STATUS_ACTIVE],
            ['name' => 'AWS Architecture Blog',     'url' => 'https://aws.amazon.com/blogs/architecture/feed/',                 'auto' => false, 'cat' => 'cloud-devops',              'status' => ImportSource::STATUS_PAUSED],
            ['name' => 'KrebsonSecurity',           'url' => 'https://krebsonsecurity.com/feed/',                               'auto' => false, 'cat' => 'cybersecurity',             'status' => ImportSource::STATUS_ACTIVE],
            ['name' => 'Towards Data Science',      'url' => 'https://towardsdatascience.com/feed',                              'auto' => false, 'cat' => 'data-analytics',            'status' => ImportSource::STATUS_ACTIVE],
            ['name' => 'CoinDesk Web3',             'url' => 'https://www.coindesk.com/arc/outboundfeeds/rss/',                 'auto' => false, 'cat' => 'blockchain-web3',           'status' => ImportSource::STATUS_ACTIVE],
        ];

        foreach ($sources as $idx => $entry) {
            ImportSource::query()->updateOrCreate(
                ['feed_url' => $entry['url']],
                [
                    'name' => $entry['name'],
                    'category_id' => $this->categories[$entry['cat']]->id,
                    'default_language_id' => $this->english->id,
                    'status' => $entry['status'],
                    'auto_publish' => $entry['auto'],
                    'default_post_type' => 'news',
                    'fetch_interval_minutes' => $entry['auto'] ? 30 : 120,
                    'last_fetched_at' => $entry['status'] === ImportSource::STATUS_PAUSED ? null : now()->subMinutes($idx * 45 + 10),
                    'last_error' => $entry['status'] === ImportSource::STATUS_ERROR ? 'HTTP 503 Service Unavailable' : null,
                    'item_count' => $entry['status'] === ImportSource::STATUS_PAUSED ? 0 : fake()->numberBetween(12, 240),
                    'created_by' => $this->staff['admin']->id,
                ],
            );
        }
    }

    // -----------------------------------------------------------------
    // Media library — 40 rows of placeholder asset metadata so the
    // grid browser, media picker, and total-files tile all render
    // meaningfully on a fresh install.
    // -----------------------------------------------------------------

    private function seedMedia(): void
    {
        // Idempotency guard — keep the library at a stable size.
        $existing = Media::query()->count();
        if ($existing >= 50) {
            $this->command?->getOutput()?->writeln(sprintf(
                '    (skipped — %d media rows already present)', $existing,
            ));

            return;
        }

        // 1. Scan storage/media directory for real existing demo images
        $storageDir = public_path('storage/media');
        $realFiles = [];
        if (is_dir($storageDir)) {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($storageDir));
            foreach ($it as $f) {
                if ($f->isFile() && preg_match('/\.(jpg|jpeg|png|webp)$/i', $f->getFilename()) && $f->getSize() > 10000) {
                    $realFiles[] = $f;
                }
            }
        }

        if (! empty($realFiles)) {
            foreach ($realFiles as $f) {
                $relativePath = str_replace('\\', '/', str_replace(public_path('storage/'), '', $f->getPathname()));
                $filename = $f->getFilename();
                $imageInfo = @getimagesize($f->getPathname());
                $width = $imageInfo ? $imageInfo[0] : 1200;
                $height = $imageInfo ? $imageInfo[1] : 800;
                $mime = $imageInfo ? $imageInfo['mime'] : 'image/jpeg';
                $alt = ucwords(str_replace(['-', '_', '.jpg', '.png', '.webp', '.jpeg'], [' ', ' ', '', '', '', ''], $filename));

                Media::query()->create([
                    'disk' => 'public',
                    'path' => $relativePath,
                    'filename' => $filename,
                    'original_filename' => $filename,
                    'mime_type' => $mime,
                    'size' => $f->getSize(),
                    'width' => $width,
                    'height' => $height,
                    'alt_text' => $alt,
                    'caption' => $alt,
                    'uploaded_by' => $this->staff['admin']->id,
                ]);
            }

            return;
        }

        for ($i = 0; $i < 60; $i++) {
            Media::factory()->create([
                'uploaded_by' => $this->staff['admin']->id,
            ]);
        }
    }

    // -----------------------------------------------------------------
    // Login logs — distributed across the demo staff so the User
    // Activity dashboard and Recent Sign-ins widget have plenty to
    // chart. Idempotent re-runs skip when we're already past the
    // target sample size.
    // -----------------------------------------------------------------

    private function seedLoginLogs(): void
    {
        $existing = LoginLog::query()->count();
        if ($existing >= 80) {
            $this->command?->getOutput()?->writeln(sprintf(
                '    (skipped — %d login logs already present)', $existing,
            ));

            return;
        }

        $users = collect($this->staff)->values();
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Brave'];
        $platforms = ['Windows', 'macOS', 'Linux', 'iOS', 'Android'];
        $cities = [
            ['Dhaka', 'Bangladesh', 'BD'],
            ['London', 'United Kingdom', 'GB'],
            ['New York', 'United States', 'US'],
            ['Berlin', 'Germany', 'DE'],
            ['Singapore', 'Singapore', 'SG'],
            ['Tokyo', 'Japan', 'JP'],
            ['Sydney', 'Australia', 'AU'],
            ['Mumbai', 'India', 'IN'],
        ];

        // 100 rows (bumped from 50). Distributed across the last
        // 14 days so the analytics chart has a meaningful curve.
        // Status mix: ~80% success, ~12% failed, ~8% logout — matches a
        // realistic auth profile and makes the new status filter on
        // the Login Logs admin UI useful out of the box.
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $city = $cities[$i % count($cities)];
            $status = match (true) {
                $i % 12 === 0 => \App\Models\LoginLog::STATUS_FAILED,
                $i % 13 === 0 => \App\Models\LoginLog::STATUS_LOGOUT,
                default       => \App\Models\LoginLog::STATUS_SUCCESS,
            };
            LoginLog::query()->create([
                'user_id' => $status === \App\Models\LoginLog::STATUS_FAILED && $i % 4 === 0 ? null : $user->id,
                'attempted_email' => $status === \App\Models\LoginLog::STATUS_FAILED
                    ? ($i % 4 === 0 ? 'guess'.$i.'@example.com' : $user->email)
                    : null,
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'browser' => $browsers[$i % count($browsers)],
                'platform' => $platforms[$i % count($platforms)],
                'device' => $i % 4 === 0 ? 'iPhone 15' : 'Desktop',
                'device_type' => $i % 4 === 0 ? 'mobile' : 'desktop',
                'city' => $city[0],
                'country' => $city[1],
                'country_code' => $city[2],
                'status' => $status,
                'login_at' => now()->subMinutes(fake()->numberBetween(5, 60 * 24 * 14)),
            ]);
        }
    }

    // -----------------------------------------------------------------
    // Sample notifications — 30+ rows so the notification bell + index
    // page have realistic content on first login. Mix of read/unread,
    // editorial / newsletter / RSS types. Idempotent: skips when staff
    // already have a meaningful number of notifications.
    // -----------------------------------------------------------------

    private function seedSampleNotifications(): void
    {
        $existing = \DB::table('notifications')
            ->whereIn('notifiable_id', collect($this->staff)->pluck('id')->all())
            ->count();
        if ($existing >= 20) {
            $this->command?->getOutput()?->writeln(sprintf(
                '    (skipped — %d staff notifications already present)', $existing,
            ));

            return;
        }

        $publishedPosts = Post::query()
            ->where('status', PostStatus::Published->value)
            ->take(10)
            ->get();

        if ($publishedPosts->isEmpty()) {
            return;
        }

        $editor = $this->staff['editor'];
        $authors = collect([
            $this->staff['author_1'],
            $this->staff['author_2'],
            $this->staff['author_3'],
        ]);

        // 8 "post submitted for review" pings to the editor.
        foreach ($publishedPosts->take(8) as $post) {
            $author = $authors->random();
            Notification::send($editor, new PostSubmittedForReview($post, $author));
        }

        // 8 "post approved" / "post published" pings to authors.
        foreach ($publishedPosts->take(8) as $post) {
            $author = User::query()->find($post->author_id) ?? $authors->random();
            Notification::send($author, new PostApproved($post, $editor, 'Strong piece — nice angle.'));
            Notification::send($author, new PostPublishedNotification($post, $this->staff['admin']));
        }

        // 6 "new subscriber" pings to admins.
        $subscribers = NewsletterSubscriber::query()
            ->where('status', NewsletterSubscriber::STATUS_CONFIRMED)
            ->take(6)
            ->get();
        foreach ($subscribers as $sub) {
            Notification::send($this->staff['admin'], new NewSubscriberNotification($sub));
        }

        // Mark roughly 60% of all newly-created notifications as read so
        // the bell badge doesn't read 30 unread on a fresh install.
        \DB::table('notifications')
            ->inRandomOrder()
            ->limit((int) (\DB::table('notifications')->count() * 0.6))
            ->update(['read_at' => now()->subHours(fake()->numberBetween(1, 72))]);
    }
}
