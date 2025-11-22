<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderLimitRequestStatusChanged extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // send via email and save in DB
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->data['request_id'],
            'status' => $this->data['status'],
            'remarks' => $this->data['remarks'] ?? null,
            'message' => $this->data['message'] ?? null,
        ];
    }

    /**
     * Get the email representation of the notification.
     */
    public function toMail($notifiable)
    {
        $status = ucfirst($this->data['status']);
        $message = $this->data['message'] ?? "The status of your order limit request has been updated.";

        return (new MailMessage)
            ->subject("Order Limit Request Status: {$status}")
            ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
            ->line($message)
            ->when(isset($this->data['remarks']), function ($mail) {
                $mail->line("Remarks: {$this->data['remarks']}");
            })
            ->line('Thank you for using our system!');
    }
}
