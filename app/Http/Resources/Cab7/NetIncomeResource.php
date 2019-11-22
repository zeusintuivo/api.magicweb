<?php

namespace App\Http\Resources\Cab7;

use Illuminate\Http\Resources\Json\JsonResource;

class NetIncomeResource extends JsonResource
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
            'date'                 => $this->date,
            'amount'               => $this->amount,
            'vat_code'             => $this->vat_code,
            'direct_account'       => $this->direct_account,
            'offset_account'       => $this->offset_account,
            'client_details'       => $this->client_details,
            'system_details'       => $this->system_details,
            'internal_bill_number' => $this->internal_bill_number,
            'original_bill_number' => $this->original_bill_number,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
        ];
    }
}
