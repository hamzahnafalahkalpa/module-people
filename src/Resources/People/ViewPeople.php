<?php

namespace Hanafalah\ModulePeople\Resources\People;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Illuminate\Support\Str;

class ViewPeople extends ApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $dob = $this->dob ?? null;
        $age = isset($dob) ? \Carbon\Carbon::parse($dob)->age : null;
        $arr = [
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'name'          => (!isset($this->first_name) && isset($this->last_name) ? 'FNU ' : '') . $this->name,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            "dob"           => $dob,
            "pob"           => $this->pob ?? null,
            "age"           => $age,
            "sex"           => $this->sex ?? null,
            'card_identity' => $this->prop_card_identity,
            'phone_1' => $this->phone_1,
            'phone_2' => $this->phone_2,
        ];

        return $arr;
    }
}
