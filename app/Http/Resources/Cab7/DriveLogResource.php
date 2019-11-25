<?php

namespace App\Http\Resources\Cab7;

use Illuminate\Http\Resources\Json\JsonResource;

class DriveLogResource extends JsonResource
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
            "id"         => $this->id,
            // "user"         => $this->user,
            "began_at"   => $this->began_at,
            "ended_at"   => $this->ended_at,
            "duration"   => $this->duration,
            "driver"     => $this->driver,
            "vehicle"    => $this->vehicle,
            // "charge_total" => (float) $this->charge_total,
            // "charge_tarif" => (float) $this->charge_tarif,
            // "charge_extra" => (float) $this->charge_extra,
            "km_total"   => (float) $this->km_total,
            // "km_taken"     => (float) $this->km_taken,
            // "km_empty"     => (float) $this->km_empty,
            "mileage"    => (float) $this->mileage,
            "trip_count" => (int) $this->trip_count,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
