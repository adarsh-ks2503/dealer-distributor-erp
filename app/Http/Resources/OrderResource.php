<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderAllocationResource;
use App\Http\Resources\OrderAttachmentResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // // Calculate the subtotal of all items (allocations)
        // $itemsTotal = $this->whenLoaded('allocations', function() {
        //     return $this->allocations->sum(function ($item) {
        //         return (float)$item->qty * (float)$item->agreed_basic_price;
        //     });
        // }, 0);

        // return [
        //     'id' => $this->id,
        //     'order_number' => $this->order_number,
        //     'order_date' => $this->order_date->format('d M, Y'), // e.g., 17 Oct, 2025
        //     'status' => ucfirst($this->status),

        //     // Get the name and code of who placed the order
        //     'placed_by' => [
        //         'name' => $this->placedByDealer->name ?? $this->placedByDistributor->name ?? 'N/A',
        //         'code' => $this->placedByDealer->code ?? $this->placedByDistributor->code ?? 'N/A',
        //     ],

        //     // Include the formatted list of items
        //     'items' => OrderAllocationResource::collection($this->whenLoaded('allocations')),
            
        //     // Include a list of attachment URLs
        //     'attachments' => OrderAttachmentResource::collection($this->whenLoaded('attachments')),

        //     // Calculate totals
        //     'sub_total' => (float) $itemsTotal,
        //     'charges' => (float) $this->loading_charge + (float) $this->insurance_charge,
        //     'grand_total' => (float) $itemsTotal + (float) $this->loading_charge + (float) $this->insurance_charge,
        // ];



        // Calculate the subtotal of all items (allocations)
        $itemsTotal = $this->whenLoaded('allocations', function() {
            return $this->allocations->sum(function ($item) {
                return (float)$item->qty * (float)$item->agreed_basic_price;
            });
        }, 0);

        // [NEW] Calculate total GST from all allocations
        // Yeh tabhi kaam karega jab controller se 'gst_rate' inject hua ho
        $totalGst = $this->whenLoaded('allocations', function() {
            return $this->allocations->sum(function ($item) {
                $itemTotal = (float)$item->qty * (float)$item->agreed_basic_price;
                $gstRate = (float)($item->gst_rate ?? 0); // Use injected rate
                return ($itemTotal * $gstRate) / 100;
            });
        }, 0);

        // [NEW] Individual charges
        $loadingCharge = (float) $this->loading_charge;
        $insuranceCharge = (float) $this->insurance_charge;
        $totalCharges = $loadingCharge + $insuranceCharge;

        // [NEW] Calculate final grand total
        $grandTotal = $itemsTotal + $totalGst + $totalCharges;

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'order_date' => $this->order_date->format('d M, Y'), // e.g., 17 Oct, 2025
            'status' => ucfirst($this->status),
            
            // --- [NEW] Added fields from your DB column list ---
            'type' => $this->type,
            'payment_term' => $this->payment_term,
            'token_amount' => (float) $this->token_amount,
            'remarks' => $this->remarks,
            'terms_conditions' => $this->terms_conditions,
            
            // Timestamps (Null check ke saath)
            'approval_time' => $this->approval_time ? $this->approval_time->format('d M, Y h:i A') : null,
            'created_at' => $this->created_at->format('d M, Y h:i A'),
            // --- End of new fields ---

            // Get the name and code of who placed the order
            // 'placed_by' => [
            //     'name' => $this->placedByDealer->name ?? $this->placedByDistributor->name ?? 'N/A',
            //     'code' => $this->placedByDealer->code ?? $this->placedByDistributor->code ?? 'N/A',
            // ],

            // [UPDATED] Get the name and code of who placed the order (Flattened)
            'placed_by_name' => $this->placedByDealer->name ?? $this->placedByDistributor->name ?? 'N/A',
            'placed_by_code' => $this->placedByDealer->code ?? $this->placedByDistributor->code ?? 'N/A',

            // Include the formatted list of items
            'items' => OrderAllocationResource::collection($this->whenLoaded('allocations')),
            
            // Include a list of attachment URLs
            'attachments' => OrderAttachmentResource::collection($this->whenLoaded('attachments')),

            // [UPDATED] Calculate totals
            // 'sub_total' => (float) $itemsTotal,
            'loading_charge' => $loadingCharge,
            'insurance_charge' => $insuranceCharge,
            'total_charges' => $totalCharges, // (loading + insurance)
            // 'total_gst' => (float) $totalGst,
            // 'grand_total' => (float) $grandTotal,
        ];

        
    }
}
