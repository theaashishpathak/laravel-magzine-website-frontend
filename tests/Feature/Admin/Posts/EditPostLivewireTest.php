<?php

declare(strict_types=1);

use App\Enums\PostStatus;
use App\Livewire\Admin\Posts\Edit;
use App\Models\EditorialNote;
use App\Models\Language;
use App\Models\Media;
use App\Models\Post;
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

function editUser(string $roleName): User
{
    $user = User::factory()->create(['email_verified_at' => now()]);
    $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->firstOrFail();
    $user->assignRole($role);

    return $user->fresh();
}

test('mounts with existing post values populated into form fields', function (): void {
    $admin = editUser('Admin');
    $post = Post::factory()->draft()->create();
    $post->translations()->first()->update([
        'title' => 'Original Title',
        'content' => '<p>Original body</p>',
    ]);

    Livewire::actingAs($admin)
        ->test(Edit::class, ['post' => $post->fresh()])
        ->assertSet('title', 'Original Title')
        ->assertSet('content', '<p>Original body</p>');
});

test('save() updates the post and creates a revision', function (): void {
    $admin = editUser('Admin');
    $post = Post::factory()->draft()->create();

    expect($post->revisions()->count())->toBe(0);

    Livewire::actingAs($admin)
        ->test(Edit::class, ['post' => $post])
        ->set('title', 'Renamed Title')
        ->set('content', '<p>Edited body</p>')
        ->call('save');

    $post->refresh();
    expect($post->translate('title'))->toBe('Renamed Title');
    expect($post->translate('content'))->toBe('<p>Edited body</p>');
    expect($post->revisions()->count())->toBe(1);
});

test('author can submit own draft for review with a note', function (): void {
    $author = editUser('Author');
    $media = Media::factory()->create();
    $post = Post::factory()->draft()->withAuthor($author->id)->create([
        'featured_image_id' => $media->id,
    ]);

    Livewire::actingAs($author)
        ->test(Edit::class, ['post' => $post])
        ->set('editorialNote', 'Ready for review!')
        ->call('submitForReview');

    $post->refresh();
    expect($post->status)->toBe(PostStatus::PendingReview);
    expect($post->editorialNotes()->count())->toBe(1);
    expect($post->editorialNotes()->first()->body)->toBe('Ready for review!');
});

test('author submitForReview auto-saves pending form edits and SEO fields to database', function (): void {
    $author = editUser('Author');
    $media = Media::factory()->create();
    $post = Post::factory()->draft()->withAuthor($author->id)->create([
        'featured_image_id' => $media->id,
    ]);

    Livewire::actingAs($author)
        ->test(Edit::class, ['post' => $post])
        ->set('title', 'Updated Title for Review')
        ->set('seoMetaTitle', 'Brand New SEO Title')
        ->set('seoMetaDescription', 'Brand New SEO Description for search engines.')
        ->set('seoFocusKeyword', 'New Focus Keyword')
        ->set('editorialNote', 'Ready for editorial review!')
        ->call('submitForReview');

    $post->refresh();
    expect($post->status)->toBe(PostStatus::PendingReview);
    expect($post->translate('title'))->toBe('Updated Title for Review');
    expect($post->translate('meta_title'))->toBe('Brand New SEO Title');
    expect($post->translate('meta_description'))->toBe('Brand New SEO Description for search engines.');
    expect($post->translate('focus_keyword'))->toBe('New Focus Keyword');
});

test('submitForReview opens missing SEO modal and rejects submission when SEO is missing', function (): void {
    $author = editUser('Author');
    $post = Post::factory()->draft()->withAuthor($author->id)->create();
    $post->translations()->update([
        'meta_title' => null,
        'meta_description' => null,
        'focus_keyword' => null,
    ]);

    Livewire::actingAs($author)
        ->test(Edit::class, ['post' => $post])
        ->call('submitForReview')
        ->assertSet('showMissingSeoModal', true)
        ->assertHasErrors(['seoMetaTitle', 'seoMetaDescription', 'seoFocusKeyword', 'featuredImageId']);

    expect($post->fresh()->status)->toBe(PostStatus::Draft);
});

test('editor can approve a pending post with note creating an approve EditorialNote', function (): void {
    $editor = editUser('Editor');
    $post = Post::factory()->pendingReview()->create();

    Livewire::actingAs($editor)
        ->test(Edit::class, ['post' => $post])
        ->set('editorialNote', 'Looks great!')
        ->call('approve');

    $post->refresh();
    expect($post->status)->toBe(PostStatus::Approved);
    expect($post->editorialNotes()->ofType(EditorialNote::TYPE_APPROVE)->count())->toBe(1);
});

test('editor request-changes requires feedback note', function (): void {
    $editor = editUser('Editor');
    $post = Post::factory()->pendingReview()->create();

    Livewire::actingAs($editor)
        ->test(Edit::class, ['post' => $post])
        ->set('editorialNote', '')                       // intentionally blank
        ->call('requestChanges');

    // Status should NOT change because RequestChangesAction throws ValidationException.
    expect($post->fresh()->status)->toBe(PostStatus::PendingReview);
});

test('editor reject moves to Rejected when reason supplied', function (): void {
    $editor = editUser('Editor');
    $post = Post::factory()->pendingReview()->create();

    Livewire::actingAs($editor)
        ->test(Edit::class, ['post' => $post])
        ->set('editorialNote', 'Off-brand content.')
        ->call('reject');

    expect($post->fresh()->status)->toBe(PostStatus::Rejected);
});

test('admin publish moves Approved post to Published', function (): void {
    $admin = editUser('Admin');
    $media = Media::factory()->create();
    $post = Post::factory()->state([
        'status' => PostStatus::Approved,
        'featured_image_id' => $media->id,
    ])->create();

    Livewire::actingAs($admin)
        ->test(Edit::class, ['post' => $post])
        ->call('publish');

    expect($post->fresh()->status)->toBe(PostStatus::Published);
});

test('admin archive moves Published post to Archived', function (): void {
    $admin = editUser('Admin');
    $post = Post::factory()->published()->create();

    Livewire::actingAs($admin)
        ->test(Edit::class, ['post' => $post])
        ->call('archive');

    expect($post->fresh()->status)->toBe(PostStatus::Archived);
});

test('author cannot edit another author\'s draft', function (): void {
    $author = editUser('Author');
    $other = editUser('Author');
    $post = Post::factory()->draft()->withAuthor($other->id)->create();

    Livewire::actingAs($author)
        ->test(Edit::class, ['post' => $post])
        ->assertForbidden();
});

test('author can edit own approved post but cannot publish it directly', function (): void {
    $author = editUser('Author');
    $post = Post::factory()->state(['status' => PostStatus::Approved])->withAuthor($author->id)->create();

    // Author CAN mount and save edits to their own approved post
    Livewire::actingAs($author)
        ->test(Edit::class, ['post' => $post])
        ->assertSuccessful()
        ->set('title', 'Approved Post Edited Title')
        ->call('save')
        ->assertHasNoErrors();

    expect($post->fresh()->translate('title'))->toBe('Approved Post Edited Title');

    // But author cannot publish it directly without publish permission
    Livewire::actingAs($author)
        ->test(Edit::class, ['post' => $post])
        ->call('publish')
        ->assertForbidden();
});

test('author can submit own edited approved post for review', function (): void {
    $author = editUser('Author');
    $media = Media::factory()->create();
    $post = Post::factory()->state(['status' => PostStatus::Approved])->withAuthor($author->id)->create([
        'featured_image_id' => $media->id,
    ]);

    Livewire::actingAs($author)
        ->test(Edit::class, ['post' => $post])
        ->assertSuccessful()
        ->assertSee('Submit for Review')
        ->set('title', 'Approved Post Edited Title')
        ->call('submitForReview')
        ->assertHasNoErrors();

    expect($post->fresh()->status)->toBe(PostStatus::PendingReview);
    expect($post->fresh()->translate('title'))->toBe('Approved Post Edited Title');
});

test('admin sees Publish Now and Save Changes on edit page for draft post', function (): void {
    $admin = editUser('Admin');
    $post = Post::factory()->draft()->create();

    Livewire::actingAs($admin)
        ->test(Edit::class, ['post' => $post])
        ->assertSuccessful()
        ->assertSee('Publish Now')
        ->assertSee('Save Changes');
});

