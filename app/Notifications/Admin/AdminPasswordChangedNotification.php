<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPasswordChangedNotification extends Notification implements ShouldQueue
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
            ->subject('[Security Audit] User Password Changed: ' . $this->user->name)
            ->view('emails.admin.password-changed-alert', [
                'user' => $this->user,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin.user_password_changed',
            'user_id' => $this->user->id,
            'title' => 'User Password Updated',
            'message' => "Password was changed for user {$this->user->name} ({$this->user->email}).",
            'icon' => 'key',
            'color' => 'amber',
            'url' => route('admin.staff.index'),
        ];
    }
}
