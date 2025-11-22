<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderLimitRequested extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Notification channels
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // send email and store in DB
    }

    /**
     * Email representation
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Limit Change Request')
            ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
            ->line("A new order limit change was requested for {$this->data['type']} '{$this->data['name']}'.")
            ->line("Current Limit: {$this->data['order_limit']}")
            ->line("Requested Limit: {$this->data['desired_order_limit']}")
            ->action('View Request', url("/order-limit-requests/{$this->data['request_id']}"))
            ->line('Please review this request at your earliest convenience.');
    }

    /**
     * Database representation
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Order Limit Change Request',
            'message' => "A new order limit change was requested for {$this->data['type']} '{$this->data['name']}'.",
            'current_order_limit' => $this->data['order_limit'],
            'desired_order_limit' => $this->data['desired_order_limit'],
            'request_id' => $this->data['request_id'],
        ];
    }
}
