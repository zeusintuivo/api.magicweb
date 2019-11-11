<?php

namespace App\Http\Resources\Cab7;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use function date_format;
use function strtotime;

class LedgerJournalResource extends JsonResource
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
            'debit'                => $this->ledgerAccounts()->with(['skr04Account'])->whereCredit('0.00')->first()->skr04Account,
            'credit'               => $this->ledgerAccounts()->with(['skr04Account'])->whereDebit('0.00')->first()->skr04Account,
            'user'                 => new UserResource($this->user),
            'date'                 => date('d.m.Y', strtotime($this->date)),
            'original_bill_number' => $this->original_bill_number,
            'internal_bill_number' => $this->internal_bill_number,
            'amount'               => (string) $this->amount,
            'vat_code'             => $this->vat_code,
            'client_details'       => $this->client_details,
            'system_details'       => $this->system_details,
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
        ];
    }
}
