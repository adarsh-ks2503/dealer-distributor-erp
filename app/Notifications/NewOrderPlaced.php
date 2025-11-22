<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Helpers\NumberHelper;

class NewOrderPlaced extends Notification
{
    use Queueable;

    protected $order;
    protected $placer;

    public function __construct($order, $placer)
    {
        $this->order = $order;
        $this->placer = $placer;
    }

    /**
     * Determine the notification channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Email representation.
     */
    public function toMail($notifiable): MailMessage
    {
        $placerName = $this->placer->name ?? 'Unknown';
        $placerCode = $this->placer->code ? " ({$this->placer->code})" : '';
        $formattedDate = Carbon::parse($this->order->order_date)->format('M d, Y');

        // ===== GRAND TOTAL CALCULATION =====
        $grandTotal = $this->calculateGrandTotal();
        $amountInWords = NumberHelper::amountInWords($grandTotal);

        // ===== Generate PDF =====
        $pdf = \PDF::loadView('pdf.order', [
            'order' => $this->order,
            'logoBase64' => $this->getBase64Logo(),
            'sealBase64' => $this->getBase64Seal(),
            'amountInWords' => $amountInWords,
            'company_settings' => \App\Models\CompanySetting::first(),
            'grandTotal' => $grandTotal,
        ])->output();

        return (new MailMessage)
            ->subject('New Order Placed')
            ->greeting('Hello ' . $notifiable->name . " " .$notifiable->last_name.  ',')
            ->line("A new order has been placed for {$this->order->type} : {$placerName}{$placerCode} - order number [#{$this->order->order_number}] on {$formattedDate}.")
            ->action('View Order', url('order-management/show/' . $this->order->id))
            ->attachData($pdf, "order-{$this->order->order_number}.pdf", [
                'mime' => 'application/pdf',
            ])
            ->line('Please check the attached order PDF for details.');
    }

    /**
     * Database representation.
     */
    public function toArray($notifiable): array
    {
        $formattedDate = Carbon::parse($this->order->order_date)->format('M d, Y');

        return [
            'title' => 'New Order Placed',
            'message' => "A new order has been placed for {$this->placer->name}" .
                ($this->placer->code ? " ({$this->placer->code})" : "") .
                " - order number [#{$this->order->order_number}] on {$formattedDate}.",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => $this->order->type,
            'placer_id' => $this->placer->id,
            'placer_type' => $this->order->type,
            'formatted_date' => $formattedDate,
        ];
    }


    /**
     * Convert company logo to Base64.
     */
    private function getBase64Logo()
    {
        $path = public_path('assets/img/logo.png');

        if (!File::exists($path)) return null;

        return 'data:' . File::mimeType($path) . ';base64,' . base64_encode(File::get($path));
    }

    /**
     * Convert company seal to Base64.
     */
    private function getBase64Seal()
    {
        $path = public_path('assets/img/singhal -Stamp.png');

        if (!File::exists($path)) return null;

        return 'data:' . File::mimeType($path) . ';base64,' . base64_encode(File::get($path));
    }

    /**
     * Calculate Grand Total for order.
     */
    private function calculateGrandTotal()
    {
        return $this->order->allocations->sum(function ($allocation) {
            $pricePerUnit =
                $allocation->agreed_basic_price +
                $this->order->loading_charge +
                $this->order->insurance_charge;

            return $allocation->qty * $pricePerUnit;
        });
    }
}
