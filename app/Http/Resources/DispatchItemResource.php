<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DispatchItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item_name' => $this->item_name,
            'size' => $this->whenLoaded('size', $this->size->size . ' mm'),
            'length' => (float) $this->length,
            'quantity' => (float) $this->dispatch_qty,
            'basic_price' => (float) $this->basic_price,
            'gauge_diff' => (float) $this->gauge_diff,
            'final_price' => (float) $this->final_price,
            'gst_percentage' => (float) $this->gst,
            'total_amount' => (float) $this->total_amount,
            'order_number' => $this->whenLoaded('order', $this->order->order_number),
        ];
    }
}
