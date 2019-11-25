<?php

namespace App\Http\Resources\Cab7;

use Illuminate\Http\Resources\Json\JsonResource;

class CashBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'internal_bill_number' => $this->internal_bill_number,
            'date'                 => $this->date,
            'amount'               => $this->amount,
            'vat_code'             => $this->vat_code,
            'ref_account'          => $this->ref_account,
            'client_details'       => $this->client_details,
            'system_details'       => $this->system_details,
            'created_at'           => $this->created_at,
        ];
    }
}
