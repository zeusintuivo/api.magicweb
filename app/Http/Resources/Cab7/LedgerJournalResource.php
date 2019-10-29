<?php

namespace App\Http\Resources\Cab7;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LedgerJournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'date' => $this->date,
            'bill_number' => $this->bill_number,
            'sequence_number' => $this->sequence_number,
            'amount' => (string) $this->amount,
            'vat_code' => $this->vat_code,
            'client_details' => $this->client_details,
            'system_details' => $this->system_details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
