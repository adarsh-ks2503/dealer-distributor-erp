<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;  // <-- Import this

class NewDealerAdded extends Notification
{
    use Queueable;

    public function __construct(protected $dealer) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];  // Add 'mail' channel
    }

    public function toArray($notifiable): array
    {
        return [
            'title'     => 'New Dealer Added',
            'message'   => "Dealer '{$this->dealer->name}' (Code: {$this->dealer->code}) has been created with status pending kindly approve/reject.",
            'dealer_id' => $this->dealer->id,
            'type'      => 'dealer',
        ];
    }

    // New method for email notification
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Dealer Added: ' . $this->dealer->name)
                    ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
                    ->line("A new dealer '{$this->dealer->name}' (Code: {$this->dealer->code}) has been created.")
                    ->line('Please review and approve or reject the request.')
                    ->action('View Dealer', url(route('dealers.show', $this->dealer->id)))
                    ->line('Thank you for using our application!');
    }
}
