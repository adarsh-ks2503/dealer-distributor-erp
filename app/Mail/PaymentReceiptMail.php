<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data, string $pdf)
    {
        $this->data = $data;
        $this->pdf = $pdf;

        Log::info('Payment receipt mail initialized', [
            'student_id' => $data['student'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Receipt Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment_receipt',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $mail = $this->markdown('emails.payment_receipt')
            ->subject('Payment Receipt - Mitra Group Of Company')
            ->with([
                'company' => $this->data['company'],
                'student' => $this->data['student'],
                'amount' => $this->data['amount'],
                'date' => $this->data['date'],
                'transaction_id' => $this->data['transaction_id'],
                'payment_mode' => $this->data['payment_mode'],
                'status' => $this->data['status']
            ]);

        $mail->attachData($this->pdf, 'receipt_' . $this->data['transaction_id'] . '.pdf', [
            'mime' => 'application/pdf',
        ]);

        return $mail;
    }
}
