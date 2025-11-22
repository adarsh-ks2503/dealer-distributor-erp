<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordOtp extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
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
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your OTP for Password Reset')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your One-Time Password (OTP) is:')
            ->line('**' . $this->otp . '**')
            ->line('This OTP is valid for the next 10 minutes.')
            ->line('If you did not request this, please ignore this email.');
    }

    public function toArray($notifiable)
    {
        return [
            'otp' => $this->otp,
        ];
    }
}
