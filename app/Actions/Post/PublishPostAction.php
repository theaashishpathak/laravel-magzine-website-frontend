<?php

declare(strict_types=1);

namespace App\Actions\Post;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Notifications\Editorial\PostPublishedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;

/**
 * Publish a Post — transition to status=Published.
 *
 * Allowed source states by default: Approved, Scheduled, Unpublished.
 * With $allowDirectPublish=true: Draft is also accepted (super-admin / admin
 * direct-publish workflow that skips the review pipeline).
 *
 * Side effects:
 *   - Sets published_at = now() if currently null.
 *   - If $cascadeTranslations=true, marks every translation row is_published=true.
 *
 * Note that the PostObserver redundantly backfills published_at on save
 * — this Action handles the case explicitly so callers can reason about
 * the transition without relying on the observer.
 */
class PublishPostAction
{
    public function handle(
        Post $post,
        bool $cascadeTranslations = false,
        bool $allowDirectPublish = false,
        ?User $publisher = null,
    ): Post {
        $this->assertCanPublish($post, $allowDirectPublish);
        $this->assertHasRequiredSeo($post);

        $wasAlreadyPublished = $post->status === PostStatus::Published;

        $fresh = DB::transaction(function () use ($post, $cascadeTranslations): Post {
            $payload = ['status' => PostStatus::Published->value];

            if ($post->published_at === null) {
                $payload['published_at'] = now();
            }

            $post->forceFill($payload)->save();

            if ($cascadeTranslations) {
                $post->translations()->update([
                    'is_published' => true,
                    'translation_status' => \App\Models\PostTranslation::TRANSLATION_STATUS_PUBLISHED,
                ]);
            }

            return $post->fresh(['translations']);
        });

        // Only notify on the actual transition into published — calling
        // publish twice on an already-live post stays silent.
        if (! $wasAlreadyPublished && $fresh->author_id) {
            $author = User::query()->find($fresh->author_id);
            // Don't notify the author when they published their own post.
            if ($author && (! $publisher || $publisher->id !== $author->id)) {
                Notification::send($author, new PostPublishedNotification($fresh, $publisher));
            }

            // Fan-out to author followers who opted in to publish pings.
            // chunkById keeps memory flat for authors with many followers.
            if ($author) {
                \App\Models\AuthorFollow::query()
                    ->where('author_id', $author->id)
                    ->where('notify_on_publish', true)
                    ->with('follower:id,email,name')
                    ->chunkById(200, function ($follows) use ($fresh, $publisher): void {
                        foreach ($follows as $f) {
                            $follower = $f->follower;
                            if ($follower) {
                                $follower->notify(new PostPublishedNotification($fresh, $publisher));
                            }
                        }
                    });
            }

            // Alert Super Admin and Admin recipients
            $admins = \App\Support\AdminNotificationRecipient::allActiveAdmins();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new \App\Notifications\Admin\AdminPostPublishedNotification($fresh, $publisher));
            }
        }

        return $fresh;
    }

    private function assertCanPublish(Post $post, bool $allowDirectPublish): void
    {
        if ($post->status === PostStatus::Published) {
            return;   // idempotent — already there
        }

        // Draft → Published is technically a legal transition in the
        // enum's allowed-next-states map, but it is admin-only. Require
        // the explicit direct-publish flag so accidental UI clicks from
        // less-privileged users can't bypass the editorial review queue.
        if ($post->status === PostStatus::Draft) {
            if (! $allowDirectPublish) {
                throw new InvalidArgumentException(
                    'Cannot publish a Draft post without the direct-publish flag.'
                );
            }

            return;
        }

        if ($post->status->canTransitionTo(PostStatus::Published)) {
            return;
        }

        throw new InvalidArgumentException(
            "Cannot publish a post in status [{$post->status->value}]."
        );
    }

    private function assertHasRequiredSeo(Post $post): void
    {
        if ($post->status === PostStatus::Published) {
            return;
        }

        $translation = $post->translations()->where('language_id', $post->default_language_id)->first()
            ?? $post->translations()->first();

        $metaTitle = trim((string) ($translation?->meta_title ?? ''));
        $metaDescription = trim((string) ($translation?->meta_description ?? ''));
        $focusKeyword = trim((string) ($translation?->focus_keyword ?? ''));
        $metaKeywords = trim((string) ($post->seoMetas()->first()?->meta_keywords ?? ''));

        if ($metaTitle === '' || mb_strlen($metaTitle) > 60) {
            throw new InvalidArgumentException(
                'The article cannot be published until a valid Meta Title (between 1 and 60 characters) is provided.'
            );
        }

        if ($metaDescription === '' || mb_strlen($metaDescription) > 160) {
            throw new InvalidArgumentException(
                'The article cannot be published until a valid Meta Description (between 1 and 160 characters) is provided.'
            );
        }

        if ($focusKeyword === '' && $metaKeywords === '') {
            throw new InvalidArgumentException(
                'The article cannot be published until Keywords (focus keyword or meta keywords) are provided.'
            );
        }
    }
}
