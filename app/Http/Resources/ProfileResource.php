<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'      => $this->name,
            'type'      => $this->type,
            'code'      => $this->code,
            'email'     => $this->email,
            'mobile_no' => $this->mobile_no,
            'status'    => $this->status,

            // Get just the name from the loaded relationships
            'state'     => $this->whenLoaded('state', $this->state->state),
            'city'      => $this->whenLoaded('city', $this->city->name),
            // $this->mergeWhen(
            //     // Condition same hai: type 'dealer' ho, relation loaded ho, aur null na ho
            //     $this->type === 'dealer' && $this->relationLoaded('distributor') && $this->distributor,
                
            //     // Yeh function ab sirf tabhi chalega jab upar ki condition TRUE hogi
            //     function () {
            //         return [
            //             'assigned_distributor' => $this->distributor->name
            //         ];
            //     }
            // )
            // --- YAHAN BADLAAV KIYA GAYA HAI ---
            // Humne mergeWhen ko hata diya hai aur ek normal key add ki hai
            // Ab yeh key hamesha response mein rahegi.
            'assigned_distributor' => (
                // Condition wahi hai:
                // 1. Type 'dealer' ho
                // 2. 'distributor' relation load kiya gaya ho
                // 3. Aur distributor null na ho
                $this->type === 'dealer' && $this->relationLoaded('distributor') && $this->distributor
            ) 
                ? $this->distributor->name  // Agar upar sab true hai, toh naam dikhaye
                : 'NA',                    // Warna 'N/A' dikhaye
            // --- BADLAAV KHATAM ---
        ];
    }
}
