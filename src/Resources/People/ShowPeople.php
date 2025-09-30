<?php

namespace Hanafalah\ModulePeople\Resources\People;

use Hanafalah\ModuleRegional\Resources\Address\ShowAddress;
use Illuminate\Support\Str;

class ShowPeople extends ViewPeople
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $arr = [
            'profile'             => asset($this->profile),
            'blood_type'          => $this->blood_type,
            'last_education_id'   => $this->last_education_id,
            'last_education'      => $this->prop_last_education,
            'religion_id'         => $this->religion_id,
            'religion'            => $this->prop_religion,
            'marital_status_id'   => $this->marital_status_id,
            'marital_status'      => $this->prop_marital_status,
            'total_children'      => $this->total_children,
            'email'               => $this->email,
            'father_name'         => $this->father_name,
            'mother_name'         => $this->mother_name,
            'is_nationality'      => $this->boolValidate('is_nationality'),
            'country'             => $this->prop_country,
            'family_relationship' => $this->relationValidation("familyRelationship", function () {
                return $this->familyRelationShip->toShowApi()->resolve();
            }),
            'phones' => $this->relationValidation('hasPhones', function () {
                return $this->hasPhones->transform(function ($phone) {
                    return $phone->toShowApi()->resolve();
                });
            }),
            'address' => $this->relationValidation('addresses', function () {
                return (object) $this->addresses->mapWithKeys(function ($address) {
                    return [Str::lower($address->flag) => new ShowAddress($address)];
                });
            })
        ];

        $arr = array_merge(parent::toArray($request), $arr);

        return $arr;
    }
}
