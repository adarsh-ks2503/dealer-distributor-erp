<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewDistributorAdded extends Notification
{
    use Queueable;

    public function __construct(protected $distributor)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'          => 'New Distributor Added',
            'message'        => "Distributor '{$this->distributor->name}' (Code: {$this->distributor->code}) has been created.",
            'distributor_id' => $this->distributor->id,
            'type'           => 'distributor',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Distributor Added: {$this->distributor->name}')
                    ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
                    ->line("A new distributor '{$this->distributor->name}' (Code: {$this->distributor->code}) has been created.")
                    ->action('View Distributor', url(route('distributors.show', $this->distributor->id)))
                    ->line('Thank you for using our application!');
    }
}
