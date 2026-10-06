<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post, public ?User $publisher = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $translation = $this->post->translation();
        $title = (string) ($translation?->title ?? '#' . $this->post->id);
        $slug = $translation?->slug ?? (string) $this->post->id;
        $authorName = $this->post->author?->name ?? 'Unknown Author';
        $publisherName = $this->publisher?->name ?? 'System';
        $categoryName = $this->post->category?->name;

        return (new MailMessage)
            ->subject('[Editorial Alert] Post Published: ' . $title)
            ->view('emails.admin.post-published-alert', [
                'title' => $title,
                'authorName' => $authorName,
                'publisherName' => $publisherName,
                'categoryName' => $categoryName,
                'postUrl' => route('frontend.posts.show', $slug),
                'adminEditUrl' => route('admin.posts.edit', $this->post),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $translation = $this->post->translation();
        $title = (string) ($translation?->title ?? '#' . $this->post->id);

        return [
            'type' => 'admin.post_published',
            'post_id' => $this->post->id,
            'title' => 'Post Published Live',
            'message' => "\"{$title}\" was published live by " . ($this->publisher?->name ?? 'System'),
            'icon' => 'globe',
            'color' => 'emerald',
            'url' => route('admin.posts.edit', $this->post),
        ];
    }
}
