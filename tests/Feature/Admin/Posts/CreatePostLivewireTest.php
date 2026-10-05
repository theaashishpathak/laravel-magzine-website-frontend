<?php

declare(strict_types=1);

use App\Enums\PostStatus;
use App\Livewire\Admin\Posts\Create;
use App\Models\Category;
use App\Models\Language;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Support\LocaleResolver;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Language::factory()->english()->default()->create();
    app(LocaleResolver::class)->flush();
    app(PermissionSeeder::class)->run();
});

function createUser(string $roleName = 'Admin'): User
{
    $user = User::factory()->create(['email_verified_at' => now()]);
    $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->firstOrFail();
    $user->assignRole($role);

    return $user->fresh();
}

test('saveDraft creates a post with default-language translation and tags', function (): void {
    $admin = createUser('Admin');
    $category = Category::factory()->create();
    $tag1 = Tag::factory()->create();
    $tag2 = Tag::factory()->create();
    $language = Language::query()->default()->firstOrFail();

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('title', 'My First AI-Written Article')
        ->set('content', '<p>Body content here.</p>')
        ->set('excerpt', 'Short summary.')
        ->set('categoryId', $category->id)
        ->set('defaultLanguageId', $language->id)
        ->set('tagIds', [$tag1->id, $tag2->id])
        ->set('isFeatured', true)
        ->call('saveDraft')
        ->assertHasNoErrors()
        ->assertRedirect();

    $post = Post::query()->latest('id')->first();

    expect($post)->not->toBeNull();
    expect($post->status)->toBe(PostStatus::Draft);
    expect($post->author_id)->toBe($admin->id);
    expect($post->is_featured)->toBeTrue();
    expect($post->translate('title', 'en'))->toBe('My First AI-Written Article');
    expect($post->category_id)->toBe($category->id);
    expect($post->tags()->pluck('tags.id')->all())->toContain($tag1->id, $tag2->id);
});

test('title is required', function (): void {
    $admin = createUser('Admin');

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('title', '')
        ->call('saveDraft')
        ->assertHasErrors(['title' => 'required']);
});

test('slug auto-derives from title when blank', function (): void {
    $admin = createUser('Admin');

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('title', 'Breaking AI News Update')
        ->assertSet('slug', 'breaking-ai-news-update');
});

test('slug stays user-edited once manually typed', function (): void {
    $admin = createUser('Admin');

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('slug', 'custom-slug')
        ->set('title', 'New Title That Should Not Override Slug')
        ->assertSet('slug', 'custom-slug');
});

test('saveAndSubmit transitions to PendingReview after creation', function (): void {
    $author = createUser('Author');
    $language = Language::query()->default()->firstOrFail();
    $media = Media::factory()->create();

    Livewire::actingAs($author)
        ->test(Create::class)
        ->set('title', 'Pitch idea')
        ->set('content', '<p>Body</p>')
        ->set('defaultLanguageId', $language->id)
        ->set('featuredImageId', $media->id)
        ->set('seoMetaTitle', 'Pitch idea SEO Title')
        ->set('seoMetaDescription', 'Pitch idea SEO meta description for review.')
        ->set('seoFocusKeyword', 'pitch idea')
        ->call('saveAndSubmit')
        ->assertRedirect();

    $post = Post::query()->latest('id')->first();
    expect($post->status)->toBe(PostStatus::PendingReview);
    expect($post->editorialNotes()->count())->toBe(0);   // no note was passed
});

test('saveAndSubmit rejects submission and opens missing SEO modal when SEO is missing', function (): void {
    $author = createUser('Author');
    $language = Language::query()->default()->firstOrFail();

    Livewire::actingAs($author)
        ->test(Create::class)
        ->set('title', 'Pitch idea without SEO')
        ->set('content', '<p>Body</p>')
        ->set('defaultLanguageId', $language->id)
        ->call('saveAndSubmit')
        ->assertSet('showMissingSeoModal', true)
        ->assertHasErrors(['seoMetaTitle', 'seoMetaDescription', 'seoFocusKeyword', 'featuredImageId']);

    expect(Post::query()->whereHas('translations', fn ($q) => $q->where('title', 'Pitch idea without SEO'))->exists())->toBeFalse();
});

test('savePublish is rejected for users without publish permission', function (): void {
    $author = createUser('Author');
    $language = Language::query()->default()->firstOrFail();

    Livewire::actingAs($author)
        ->test(Create::class)
        ->set('title', 'I should not be able to publish directly')
        ->set('content', '<p>Body</p>')
        ->set('defaultLanguageId', $language->id)
        ->call('savePublish')
        ->assertForbidden();
});

test('savePublish rejects publishing when SEO meta title, description, or keywords are missing', function (): void {
    $admin = createUser('Admin');
    $language = Language::query()->default()->firstOrFail();

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('title', 'Direct-publish test without SEO')
        ->set('content', '<p>Body</p>')
        ->set('defaultLanguageId', $language->id)
        ->call('savePublish')
        ->assertHasErrors(['seoMetaTitle', 'seoMetaDescription', 'seoFocusKeyword', 'featuredImageId']);
});

test('savePublish moves status to Published when user has permission and SEO fields are provided', function (): void {
    $admin = createUser('Admin');
    $language = Language::query()->default()->firstOrFail();
    $media = Media::factory()->create();

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('title', 'Direct-publish test with complete SEO')
        ->set('content', '<p>Body</p>')
        ->set('defaultLanguageId', $language->id)
        ->set('featuredImageId', $media->id)
        ->set('seoMetaTitle', 'Complete Meta Title for Publishing')
        ->set('seoMetaDescription', 'Complete meta description for publishing that meets the minimum length requirement.')
        ->set('seoFocusKeyword', 'publishing keyword')
        ->call('savePublish')
        ->assertRedirect();

    $post = Post::query()->latest('id')->first();
    expect($post->status)->toBe(PostStatus::Published);
    expect($post->published_at)->not->toBeNull();
});

test('savePublish rejects publishing when meta title exceeds 60 chars or meta description exceeds 160 chars', function (): void {
    $admin = createUser('Admin');
    $language = Language::query()->default()->firstOrFail();

    Livewire::actingAs($admin)
        ->test(Create::class)
        ->set('title', 'Direct-publish length boundary test')
        ->set('content', '<p>Body</p>')
        ->set('defaultLanguageId', $language->id)
        ->set('seoMetaTitle', str_repeat('a', 61))
        ->set('seoMetaDescription', str_repeat('b', 161))
        ->set('seoFocusKeyword', 'valid keyword')
        ->call('savePublish')
        ->assertHasErrors([
            'seoMetaTitle' => 'max',
            'seoMetaDescription' => 'max',
        ]);
});

test('subscribers cannot mount the Create component', function (): void {
    $subscriber = createUser('Subscriber');

    Livewire::actingAs($subscriber)
        ->test(Create::class)
        ->assertForbidden();
});

test('admin sees only Publish Now and Save Draft buttons on create page, while author sees Submit for Review and Save Draft', function (): void {
    $admin = createUser('Admin');
    $author = createUser('Author');

    // Admin (or Super Admin) has publish rights: sees Publish Now & Save Draft, NOT Submit for Review
    Livewire::actingAs($admin)
        ->test(Create::class)
        ->assertSeeHtml('wire:click="savePublish"')
        ->assertSeeHtml('wire:click="saveDraft"')
        ->assertDontSeeHtml('wire:click="saveAndSubmit"');

    // Author does NOT have publish rights: sees Submit for Review & Save Draft, NOT Publish Now
    Livewire::actingAs($author)
        ->test(Create::class)
        ->assertSeeHtml('wire:click="saveAndSubmit"')
        ->assertSeeHtml('wire:click="saveDraft"')
        ->assertDontSeeHtml('wire:click="savePublish"');
});
