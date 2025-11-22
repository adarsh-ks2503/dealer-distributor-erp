<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DispatchItemResource;
use App\Http\Resources\DispatchAttachmentResource;

class DispatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // --- Primary Dispatch Details ---
            'id' => $this->id,
            'dispatchNumber' => $this->dispatch_number,
            'dispatchDate' => $this->dispatch_date,
            'type' => $this->type,
            'status' => $this->status,
            'loadingPointId' => $this->loading_point_id,
            'remarks' => $this->dispatch_remarks,
            'termsAndConditions' => $this->terms_conditions,

            // --- Recipient Information ---
            'recipient' => [
                'name' => $this->recipient_name,
                'address' => $this->recipient_address,
                'stateId' => $this->recipient_state_id,
                'cityId' => $this->recipient_city_id,
                'pincode' => $this->recipient_pincode,
            ],

            // --- Consignee Information ---
            'consignee' => [
                'name' => $this->consignee_name,
                'address' => $this->consignee_address,
                'mobile' => $this->consignee_mobile_no,
                'stateId' => $this->consignee_state_id,
                'cityId' => $this->consignee_city_id,
                'pincode' => $this->consignee_pincode,
            ],

            // --- Transport & Driver Details ---
            'transportDetails' => [
                'transporterName' => $this->transporter_name,
                'vehicleNumber' => $this->vehicle_no,
                'driverName' => $this->driver_name,
                'driverMobile' => $this->driver_mobile_no,
                'eWayBillNumber' => $this->e_way_bill_no,
                'biltyNumber' => $this->bilty_no,
                'dispatchOutTime' => $this->dispatch_out_time,
                'remarks' => $this->transport_remarks,
            ],

            // --- Billing & Financials ---
            'billing' => [
                'billTo' => $this->bill_to,
                'billNumber' => $this->bill_number,
                'additionalCharges' => (float) $this->additional_charges,
                'totalAmount' => (float) $this->total_amount,
                'paymentSlip' => $this->payment_slip,
            ],

            'items' => DispatchItemResource::collection($this->whenLoaded('dispatchItems')),

            // --- Timestamps ---
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),


            // --- Relationships (Eager-Loaded Data) ---
            'dealer' => $this->whenLoaded('dealer'),
            // 'dispatchItems' => $this->whenLoaded('dispatchItems'),
            'distributor' => $this->whenLoaded('distributor'),
            // 'attachments' => $this->whenLoaded('attachments'),
            'attachments' => DispatchAttachmentResource::collection($this->whenLoaded('attachments')),
        ];
    }
}
