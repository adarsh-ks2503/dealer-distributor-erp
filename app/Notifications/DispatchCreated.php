<?php

namespace App\Notifications;

use App\Models\Dispatch;
use App\Models\CompanySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Notifications\Messages\MailMessage;
use App\Helpers\NumberHelper;

class DispatchCreated extends Notification
{
    use Queueable;

    protected $dispatch;
    protected $placer;

    public function __construct(Dispatch $dispatch, $placer = null)
    {
        $this->dispatch = $dispatch;
        $this->placer = $placer;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     *  DATABASE NOTIFICATION
     */
    public function toDatabase($notifiable)
    {
        $placerName = $this->placer ? $this->placer->name : 'Unknown';
        $placerCode = $this->placer->code ?? null;

        return [
            'title'           => 'New Dispatch Created',
            'dispatch_id'     => $this->dispatch->id,
            'dispatch_number' => $this->dispatch->dispatch_number,
            'created_by'      => $this->dispatch->created_by,
            'message'         => "A new dispatch has been placed for {$placerName}" .
                                 ($placerCode ? " ({$placerCode})" : "") .
                                 " [#{$this->dispatch->dispatch_number}]",
        ];
    }


    /**
     *  EMAIL NOTIFICATION
     */
    public function toMail($notifiable)
    {
        $placerName = $this->placer ? $this->placer->name : 'Unknown';

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

        $company_settings = CompanySetting::firstOrCreate([]);

        // ----- PDF Data -----
        $pdf = \PDF::loadView('pdf.dispatch', [
            'dispatch'      => $this->dispatch,
            'totalQty'      => $totalQty,
            'amountInWords' => $amountInWords,
            'logoBase64'    => $logoBase64,
            'sealBase64'    => $sealBase64,
            'company_settings' => $company_settings,
        ])->output();


        // ===== SEND EMAIL =====
        return (new MailMessage)
            ->subject('New Dispatch Created')
            ->greeting("Hello {$notifiable->name} {$notifiable->last_name},")
            ->line("A new dispatch has been created: [#{$this->dispatch->dispatch_number}] for {$placerName}.")
            ->action('View Dispatch', url("/dispatch/show/{$this->dispatch->id}"))
            ->attachData($pdf, "Dispatch-{$this->dispatch->dispatch_number}.pdf", [
                'mime' => 'application/pdf',
            ])
            ->line('The dispatch PDF is attached for your reference.');
    }
}
