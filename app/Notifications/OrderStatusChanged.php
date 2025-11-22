<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class OrderStatusChanged extends Notification
{
    use Queueable;

    protected $order;
    protected $status;
    protected $remarks;

    public function __construct($order, $status, $remarks = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->remarks = $remarks;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database']; // send email + store in DB
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => "Order {$this->status}",
            'message' => "Your order [#{$this->order->order_number}] has been {$this->status}.",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'placer_code' => $this->order->type === 'dealer'
                ? $this->order->placed_by_dealer_id
                : $this->order->placed_by_distributor_id,
        ];
    }

    public function toMail($notifiable)
    {
        $formattedDate = Carbon::parse($this->order->updated_at)
            ->setTimezone('Asia/Kolkata')
            ->format('M d, Y \a\t h:i A');

        $mail = (new MailMessage)
            ->subject("Order [#{$this->order->order_number}] Status: {$this->status}")
            ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
            ->line("Your order [#{$this->order->order_number}] has been {$this->status} on {$formattedDate}.");

        if ($this->remarks) {
            $mail->line("Remarks: {$this->remarks}");
        }

        return $mail->line('Thank you for using our system!');
    }
}
