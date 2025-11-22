<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderAttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return [
        //     'url' => Storage::url($this->attachment),
        //     'remarks' => $this->remarks,
        // ];

        // Yahan 'attachment' aapke database column ka naam hai
        $storedPath = $this->attachment; 

        // [NEW] Step 1: Sabse pehle backslashes (\) ko forward slashes (/) mein badlein
        $cleanedPath = str_replace('\\', '/', $storedPath);

        // [NEW] Step 2: Shuru ka 'public/storage/' ya '/storage/' ya 'storage/' hissa hata dein
        // Taa ki humein sirf 'order_attachments/...' mile
        $relativePath = preg_replace('/^(public\/storage\/|\/storage\/|storage\/)/', '', $cleanedPath);

        return [
            // [UPDATED] Ab Storage::url() ko saaf relative path milega
            // 'url' => Storage::url($this->attachment),
            'url' => url('storage/' . $this->attachment), 
            'remarks' => $this->remarks,
        ];
    }
}
