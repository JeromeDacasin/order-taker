<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'customer'      => $this->customer->full_name,
            'delivery_date' => $this->date_of_delivery,
            'amount_due'    => $this->amount_due,
            'is_active'     => $this->is_active,
            'status'        => $this->status
        ];
    }
}