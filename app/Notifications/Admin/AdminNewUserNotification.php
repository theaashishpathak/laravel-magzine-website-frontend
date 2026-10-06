<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[Alert] New User Registered: ' . $this->user->name)
            ->view('emails.admin.user-created-alert', [
                'user' => $this->user,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin.user_created',
            'user_id' => $this->user->id,
            'title' => 'New User Registered',
            'message' => "{$this->user->name} ({$this->user->email}) joined " . config('app.name', 'Blogger4U'),
            'icon' => 'user-plus',
            'color' => 'blue',
            'url' => route('admin.staff.index'),
        ];
    }
}
