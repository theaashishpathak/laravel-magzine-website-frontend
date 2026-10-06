<?php

declare(strict_types=1);

namespace App\Notifications\Editorial;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the author and opted-in followers when a post goes live.
 */
class PostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post, public ?User $publisher = null) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $translation = $this->post->translation();
        $title = (string) ($translation?->title ?? '#' . $this->post->id);
        $slug = $translation?->slug ?? (string) $this->post->id;
        $authorName = $this->post->author?->name ?? 'Unknown Author';
        $isAuthor = ($notifiable->id === $this->post->author_id);
        $excerpt = $translation?->summary ?? $translation?->meta_description ?? '';
        $coverImage = $this->post->featuredImage?->url();
        $categoryName = $this->post->category?->name;
        $postUrl = route('frontend.posts.show', $slug);

        $subject = $isAuthor
            ? 'Your article has been published: ' . $title
            : 'New article by ' . $authorName . ': ' . $title;

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.editorial.post-published', [
                'isAuthor' => $isAuthor,
                'title' => $title,
                'authorName' => $authorName,
                'excerpt' => $excerpt,
                'coverImage' => $coverImage,
                'categoryName' => $categoryName,
                'postUrl' => $postUrl,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = (string) ($this->post->translation()?->title ?? '#'.$this->post->id);
        $publishedAt = $this->post->published_at?->diffForHumans() ?? 'just now';
        $by = $this->publisher?->name ?? 'The system';

        return [
            'type' => 'post.published',
            'post_id' => $this->post->id,
            'post_title' => $title,
            'publisher_id' => $this->publisher?->id,
            'publisher_name' => $this->publisher?->name,
            'published_at' => $this->post->published_at?->toIso8601String(),
            'icon' => 'sparkles',
            'color' => 'emerald',
            'title' => 'Post published',
            'message' => "\"{$title}\" went live {$publishedAt} — published by {$by}.",
            'url' => route('admin.posts.edit', $this->post),
        ];
    }
}
