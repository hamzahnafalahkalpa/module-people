<?php

namespace Hanafalah\ModulePeople\Resources\FamilyRelationship;

use Hanafalah\LaravelSupport\Resources\ApiResource;
use Illuminate\Support\Str;

class ViewFamilyRelationship extends ApiResource
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
            'id'             => $this->id,
            'name'           => $this->name, 
            'phone'          => $this->phone, 
            'family_role_id' => $this->family_role_id,
            'family_role'    => $this->relationValidation('familyRole',function(){
                return $this->familyRole->toViewApi()->resolve();
            },$this->prop_family_role)
        ];
        return $arr;
    }
}
