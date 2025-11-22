<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderManagementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return [
        //     'id' => $this->id,
        //     'order_number' => $this->order_number,
        //     'order_date' => $this->order_date,
        //     'type' => $this->type,
        //     'party' => $this->whenLoaded($this->type === 'dealer' ? 'dealer' : 'distributor'),
        //     'agreed_basic_price' => $this->agreed_basic_price,
        //     'order_qty' => $this->order_qty,
        //     'payment_term' => $this->payment_term,
        //     'status' => $this->status,
        //     'remarks' => $this->remarks,
        //     'created_by' => $this->created_by,
        //     'created_at' => $this->created_at->toDateTimeString(),
        //     'attachments' => $this->whenLoaded('attachments', function () {
        //         return $this->attachments->map(function ($attachment) {
        //             return [
        //                 'url' => url('storage/' . $attachment->attachment),
        //                 'remarks' => $attachment->remarks
        //             ];
        //         });
        //     }),
        // ];

        return [
            // --- Primary Order Details ---
            'id' => $this->id,
            'orderNumber' => $this->order_number,
            'orderDate' => $this->order_date,
            'type' => $this->type,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'termsAndConditions' => $this->terms_conditions,

            // --- Financials ---
            'paymentTerm' => $this->payment_term,
            'charges' => [
                'loading' => (float) $this->loading_charge,
                'insurance' => (float) $this->insurance_charge,
            ],
            'tokenAmount' => (float) $this->token_amount,

            // --- Meta Information ---
            'createdBy' => $this->created_by,
            'deletedBy' => $this->deleted_by,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),

            // --- Relationships (Eager-Loaded Data) ---

            // Conditionally loads the correct party (dealer or distributor) based on the 'type' field.
            'party' => $this->whenLoaded($this->type === 'dealer' ? 'dealer' : 'distributor'),

            // Formats attachments to include the full URL.
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'url' => url('storage/' . $attachment->attachment), // Assumes 'attachment' is the path column
                        'remarks' => $attachment->remarks
                    ];
                });
            }),

            'allocations' => $this->whenLoaded('allocations'),
        ];
    }
}
