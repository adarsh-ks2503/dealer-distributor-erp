<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use App\Helpers\NumberHelper;

class DispatchApproved extends Notification
{
    use Queueable;

    protected $dispatch;

    public function __construct($dispatch)
    {
        $this->dispatch = $dispatch;
    }

    // Add 'mail' along with 'database'
    public function via($notifiable): array
    {
        return ['database', 'mail']; // store in-app + send email
    }

    public function toArray($notifiable): array
    {
        $placerName = null;

        if ($this->dispatch->type === 'dealer' && $this->dispatch->dealer_id) {
            $placer = \App\Models\Dealer::find($this->dispatch->dealer_id);
            $placerName = $placer ? $placer->name : 'Dealer';
        } elseif ($this->dispatch->type === 'distributor' && $this->dispatch->distributor_id) {
            $placer = \App\Models\Distributor::find($this->dispatch->distributor_id);
            $placerName = $placer ? $placer->name : 'Distributor';
        }

        $formattedDate = Carbon::parse($this->dispatch->approved_at ?? now())
            ->setTimezone('Asia/Kolkata')
            ->format('M d, Y \a\t h:i A');

        return [
            'title' => 'Dispatch Approved',
            'message' => "Your dispatch [#{$this->dispatch->dispatch_number}] has been approved on {$formattedDate}.",
            'dispatch_id' => $this->dispatch->id,
            'dispatch_number' => $this->dispatch->dispatch_number,
            'type' => $this->dispatch->type,
            'placer_name' => $placerName,
            'total_amount' => $this->dispatch->total_amount,
            'approved_at' => $formattedDate,
        ];
    }

    public function toMail($notifiable)
    {
        $formattedDate = Carbon::parse($this->dispatch->approved_at ?? now())
            ->setTimezone('Asia/Kolkata')
            ->format('M d, Y \a\t h:i A');
            // ----- Logo -----
        $logoPath = public_path('assets/img/logo.png');
        $logoBase64 = null;
        if (File::exists($logoPath)) {
            $logoBase64 = 'data:' . File::mimeType($logoPath) . ';base64,' . base64_encode(File::get($logoPath));
        }

        // ----- Seal -----
        $sealPath = public_path('assets/img/singhal -Stamp.png');
        $sealBase64 = null;
        if (File::exists($sealPath)) {
            $sealBase64 = 'data:' . File::mimeType($sealPath) . ';base64,' . base64_encode(File::get($sealPath));
        }

        // ----- Calculations -----
        $totalQty = $this->dispatch->dispatchItems->sum('dispatch_qty');
        $grandTotal = $this->dispatch->dispatchItems->sum('total_amount');
        $amountInWords = NumberHelper::amountInWords($grandTotal);

        // ----- PDF Data -----
        $pdf = \PDF::loadView('pdf.dispatch', [
            'dispatch'      => $this->dispatch,
            'totalQty'      => $totalQty,
            'amountInWords' => $amountInWords,
            'logoBase64'    => $logoBase64,
            'sealBase64'    => $sealBase64,
            'company_settings' => \App\Models\CompanySetting::first(),
        ])->output();

        return (new MailMessage)
            ->subject('Dispatch Approved')
            ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
            ->line("Your dispatch [#{$this->dispatch->dispatch_number}] has been approved on {$formattedDate}.")
            ->line('Please check your dispatch details in the portal.')
            ->line('Thank you for using our platform!')
            ->attachData($pdf, "Dispatch-{$this->dispatch->dispatch_number}.pdf", [
                'mime' => 'application/pdf',
            ])
            ->line('The dispatch PDF is attached for your reference.');
    }
}
