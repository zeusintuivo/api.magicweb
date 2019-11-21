<?php

namespace App\Http\Resources\Cab7;

use Illuminate\Http\Resources\Json\JsonResource;

class LedgerBalanceResource extends JsonResource
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
            'skr04_id' => $this->skr04_id,
            'balance'  => (float) $this->balance,
            'pid'      => (int) $this->pid,
            'side'     => $this->side,
            'vat_code' => (int) $this->vat_code,
            'private'  => (int) $this->private,
            'de_DE'    => $this->de_DE,
            'en_GB'    => $this->en_GB,
        ];
    }
}
