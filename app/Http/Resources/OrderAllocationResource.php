<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderAllocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return [
        //     'allocated_to_type' => $this->allocated_to_type,
        //     'allocated_to_id' => $this->allocated_to_id,
        //     'quantity' => (float) $this->qty,
        //     'agreed_price' => (float) $this->agreed_basic_price,
        //     'item_total' => (float) $this->qty * (float) $this->agreed_basic_price,
        // ];

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            
            // Allocation kiske liye hai (type aur id)
            'allocated_to_type' => $this->allocated_to_type,
            'allocated_to_id' => $this->allocated_to_id,
            
            // Party ki details (jiske liye allocation hai)
            // (Assuming the relationship on OrderAllocation model is 'dealer')
            // 'party' => [
            //     'id'   => $this->whenLoaded('dealer', $this->dealer->id ?? null),
            //     'name' => $this->whenLoaded('dealer', $this->dealer->name ?? 'N/A'),
            //     'code' => $this->whenLoaded('dealer', $this->dealer->code ?? 'N/A'),
            // ],

            // [UPDATED] Party ki details ab flat hain (object hata diya)
            // 'party_id'   => $this->whenLoaded('dealer', $this->dealer->id ?? null),
            // 'party_name' => $this->whenLoaded('dealer', $this->dealer->name ?? 'N/A'),
            // 'party_code' => $this->whenLoaded('dealer', $this->dealer->code ?? 'N/A'),
            'party_name' => $this->whenLoaded('allocatedTo', $this->allocatedTo->name ?? 'N/A'),
            'party_code' => $this->whenLoaded('allocatedTo', $this->allocatedTo->code ?? 'N/A'),
            
            // Item details
            // 'product_name' => $this->product_name ?? 'Product Name N/A', 
            'qty' => (float) $this->qty,
            'basic_price' => (float) $this->basic_price,
            'agreed_basic_price' => (float) $this->agreed_basic_price,
            'token_amount' => (float) $this->token_amount,
            'dispatch_qty' => (float) $this->dispatch_qty,
            'remaining_qty' => (float) $this->remaining_qty,
            'payment_terms' => $this->payment_terms,
            'status' => $this->status,
            'remarks' => $this->remarks,
            
            // GST rate jo controller se inject hua hai
            // Hum yahan calculate nahi kar rahe, sirf rate bhej rahe hain.
            'gst_rate' => (float) ($this->gst_rate ?? 0),
        ];
    }
}
