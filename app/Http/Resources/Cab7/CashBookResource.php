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
            'id'              => $this->id,
            'journal'         => $this->journal,
            'skr04RefAccount' => $this->skr04RefAccount,
            'date'            => $this->date,
            'debit'           => (float) $this->debit,
            'credit'          => (float) $this->credit,
            'amount'          => $this->debit - $this->credit,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
