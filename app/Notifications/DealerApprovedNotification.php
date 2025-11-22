<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DealerApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(protected $dealer) {}

    public function via($notifiable): array
    {
        // Add 'mail' to send email notifications as well as 'database' for app notifications
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Dealer Approved!',
            'message' => "Your dealer '{$this->dealer->name}' (Code: {$this->dealer->code}) has been approved. Contact the Admin to add it to your team.",
            'dealer_id' => $this->dealer->id,
            'type' => 'dealer_approved',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Dealer Approved!')
            ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
            ->line("Your dealer '{$this->dealer->name}' (Code: {$this->dealer->code}) has been approved.")
            ->line('Contact the Admin to add it to your team.')
            ->line('Thank you for using our application!');
    }
}
