<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Post\PublishPostAction;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Language;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use App\Notifications\Admin\AdminNewUserNotification;
use App\Notifications\Admin\AdminPasswordChangedNotification;
use App\Notifications\Admin\AdminPostPublishedNotification;
use App\Notifications\Auth\PasswordChangedNotification;
use App\Notifications\Auth\WelcomeUserNotification;
use App\Notifications\Editorial\PostPublishedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailNotificationEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Author', 'guard_name' => 'web']);
    }

    public function test_new_user_registration_dispatches_notifications_to_user_and_admins(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->assignRole('Super Admin');

        $action = new CreateNewUser();
        $user = $action->create([
            'name' => 'Test Writer',
            'email' => 'writer@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        Notification::assertSentTo($user, WelcomeUserNotification::class);
        Notification::assertSentTo($admin, AdminNewUserNotification::class);
    }

    public function test_password_change_dispatches_notifications_to_user_and_admins(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->assignRole('Admin');

        $user = User::factory()->create([
            'password' => bcrypt('OldPassword123!'),
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->actingAs($user);

        $action = new UpdateUserPassword();
        $action->update($user, [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        Notification::assertSentTo($user, PasswordChangedNotification::class);
        Notification::assertSentTo($admin, AdminPasswordChangedNotification::class);
    }

    public function test_post_publishing_dispatches_notifications_to_author_and_admins(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->assignRole('Super Admin');

        $author = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $author->assignRole('Author');

        $lang = Language::query()->first() ?? Language::create([
            'name' => 'English',
            'native_name' => 'English',
            'code' => 'en',
            'locale' => 'en_US',
            'is_default' => true,
            'is_active' => true,
        ]);

        $category = Category::factory()->create();

        $post = Post::create([
            'author_id' => $author->id,
            'category_id' => $category->id,
            'default_language_id' => $lang->id,
            'status' => PostStatus::Approved,
            'visibility' => Post::VISIBILITY_PUBLIC,
        ]);

        PostTranslation::create([
            'post_id' => $post->id,
            'language_id' => $lang->id,
            'title' => 'Breaking News Test',
            'slug' => 'breaking-news-test-' . uniqid(),
            'meta_title' => 'Breaking News Test',
            'meta_description' => 'Valid test meta description for article publishing.',
            'focus_keyword' => 'breaking',
            'is_published' => false,
        ]);

        $publisher = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $publisher->assignRole('Admin');

        $publishAction = new PublishPostAction();
        $publishAction->handle($post, false, false, $publisher);

        Notification::assertSentTo($author, PostPublishedNotification::class);
        Notification::assertSentTo($admin, AdminPostPublishedNotification::class);
    }

    public function test_forgot_password_reset_dispatches_notifications_to_user_and_admins(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->assignRole('Super Admin');

        $user = User::factory()->create([
            'password' => bcrypt('OldPassword123!'),
            'status' => User::STATUS_ACTIVE,
        ]);

        $action = new ResetUserPassword();
        $action->reset($user, [
            'password' => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        Notification::assertSentTo($user, PasswordChangedNotification::class);
        Notification::assertSentTo($admin, AdminPasswordChangedNotification::class);
    }

    public function test_admin_staff_creation_with_invite_dispatches_notifications(): void
    {
        Notification::fake();

        $superAdmin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->assignRole('Admin');

        $this->actingAs($superAdmin);

        \Livewire\Livewire::test(\App\Livewire\Admin\Staff\Create::class)
            ->set('name', 'Invited Editor')
            ->set('email', 'invited.editor@example.com')
            ->set('status', User::STATUS_ACTIVE)
            ->set('portalType', 'author')
            ->set('selectedRoles', ['Author'])
            ->set('sendInvite', true)
            ->call('save')
            ->assertHasNoErrors();

        $createdUser = User::where('email', 'invited.editor@example.com')->first();
        $this->assertNotNull($createdUser);

        Notification::assertSentTo($createdUser, \App\Notifications\Auth\StaffInviteNotification::class);
        Notification::assertSentTo($admin, AdminNewUserNotification::class);
    }
}
