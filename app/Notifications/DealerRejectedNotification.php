<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DealerRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(protected $dealer) {}

    // Add 'mail' to the via channels
    public function via($notifiable): array
    {
        return ['database', 'mail']; // Database + Email
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Dealer Rejected!',
            'message' => "Your dealer '{$this->dealer->name}' (Code: {$this->dealer->code}) has been rejected. Contact the Admin for further details.",
            'dealer_id' => $this->dealer->id,
            'type' => 'dealer_rejected',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Dealer Rejected')
            ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
            ->line("Your dealer '{$this->dealer->name}' (Code: {$this->dealer->code}) has been rejected.")
            ->line('Please contact the Admin for further details.')
            ->line('Thank you for using our platform!');
    }
}
