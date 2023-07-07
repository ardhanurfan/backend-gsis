<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementEmailLink extends Notification
{
    use Queueable;

    private $messages;
    private $link;
    private $name;

    /**
     * Create a new notification instance.
     */
    public function __construct($messages, $link)
    {
        $this->messages = $messages;
        $this->link = $link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Ganesha Student Innovation Summit')
                    ->greeting('Dear '.$notifiable->name.',')
                    ->line(nl2br($this->messages))
                    ->action('Link', url($this->link));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
