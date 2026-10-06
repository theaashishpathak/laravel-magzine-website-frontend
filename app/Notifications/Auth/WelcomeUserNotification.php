<?php

declare(strict_types=1);

namespace App\Notifications\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name', 'Blogger4U'))
            ->view('emails.auth.welcome', [
                'user' => $notifiable,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'auth.welcome',
            'title' => 'Welcome to ' . config('app.name', 'Blogger4U'),
            'message' => 'Your account is ready. Start creating and publishing articles!',
            'icon' => 'sparkles',
            'color' => 'indigo',
            'url' => route('login'),
        ];
    }
}
