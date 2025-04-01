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
        $arr = [
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'name'          => (isset($this->first_name) ? '' : 'FNU ') . $this->name,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            "dob"           => $this->dob ?? null,
            "pob"           => $this->pob ?? null,
            "sex"           => isset($this->sex) ? intval($this->sex) : null,
            'card_identity' => $this->prop_card_identity
        ];

        return $arr;
    }
}
