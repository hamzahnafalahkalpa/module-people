<?php

namespace Hanafalah\ModulePeople\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePeople\Contracts\Data\MaritalStatusData;
use Hanafalah\ModulePeople\Contracts\Data\PeopleData as DataPeopleData;
use Hanafalah\ModulePeople\Enums\People\BloodType;
use Hanafalah\ModulePeople\Enums\People\MaritalStatus;
use Hanafalah\ModulePeople\Enums\People\Sex;
use Spatie\LaravelData\Attributes\{
    MapInputName, MapName,
    Validation\DateFormat,
    Validation\Enum,
    Validation\RequiredWithout,
    Validation\RequiredWithoutAll
};

class PeopleData extends Data implements DataPeopleData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('uuid')]
    #[MapName('uuid')]
    public ?string $uuid = null;

    #[MapInputName('name')]
    #[MapName('name')]
    #[RequiredWithoutAll('first_name','last_name')]
    public ?string $name = null;

    #[MapInputName('first_name')]
    #[MapName('first_name')]
    public ?string $first_name = null;
        
    #[MapInputName('last_name')]
    #[MapName('last_name')]
    #[RequiredWithout('name')]
    public ?string $last_name = null;

    #[MapInputName('sex')]
    #[MapName('sex')]
    #[Enum(Sex::class)]
    public ?string $sex = null;

    #[MapInputName('dob')]
    #[MapName('dob')]
    #[DateFormat(['Y-m-d', 'd-m-Y'])]
    public ?string $dob = null;

    #[MapInputName('pob')]
    #[MapName('pob')]
    public ?string $pob = null;

    #[MapInputName('last_education_id')]
    #[MapName('last_education_id')]
    public mixed $last_education_id = null;

    #[MapInputName('father_name')]
    #[MapName('father_name')]
    public ?string $father_name = null;

    #[MapInputName('mother_name')]
    #[MapName('mother_name')]
    public ?string $mother_name = null;

    #[MapInputName('blood_type')]
    #[MapName('blood_type')]
    #[Enum(BloodType::class)]
    public ?string $blood_type = null;

    #[MapInputName('marital_status_id')]
    #[MapName('marital_status_id')]
    public mixed $marital_status_id = null;

    #[MapInputName('marital_status')]
    #[MapName('marital_status')]
    public ?MaritalStatusData $marital_status = null;
        
    #[MapInputName('total_children')]
    #[MapName('total_children')]
    public ?int $total_children = null;

    #[MapInputName('is_nationality')]
    #[MapName('is_nationality')]
    public mixed $is_nationality = null;

    #[MapInputName('religion_id')]
    #[MapName('religion_id')]
    public mixed $religion_id = null;

    #[MapInputName('country_id')]
    #[MapName('country_id')]
    public mixed $country_id = null;

    #[MapInputName('address')]
    #[MapName('address')]
    public ?PeopleAddressData $address = null;

    #[MapInputName('card_identity')]
    #[MapName('card_identity')]
    public ?CardIdentityData $card_identity = null;

    #[MapInputName('family_relationship')]
    #[MapName('family_relationship')]
    public ?FamilyRelationshipData $family_relationship = null;

    #[MapInputName('phones')]
    #[MapName('phones')]
    public ?array $phones = [];

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = [];

    public static function after(self $data): self{
        if (!isset($data->name) && isset($data->last_name)) {
            $data->name = trim(implode(' ', [$data->first_name ?? '', $data->last_name]));
        }

        $props = &$data->props;

        $new = static::new();
        $props['prop_country'] = [
            'id'   => $data->country_id ?? null,
            'name' => null
        ];

        $phones = &$data->phones;
        if (isset($props['phone_1'])) $phones[] = $props['phone_1'];
        if (isset($props['phone_2'])) $phones[] = $props['phone_2'];
        $props['phone_1'] ??= $phones[0] ?? null;
        $props['phone_2'] ??= $phones[1] ?? null;
        $props['prop_phones'] ??= $phones ?? [];

        if (isset($props['prop_country']['id']) && !isset($props['prop_country']['name'])){
            $country = $new->CountryModel()->findOrFail($props['prop_country']['id']);
            $props['prop_country']['name'] = $country->name;
        }

        $last_education = $new->EducationModel();
        $last_education = (isset($data->last_education_id)) ? $last_education->findOrFail($data->last_education_id) : $last_education;
        $props['prop_last_education'] = $last_education->toViewApiOnlies('id','name','flag','label');
        
        $marital_status = $new->MaritalStatusModel();
        $marital_status = (isset($data->marital_status_id)) ? $marital_status->findOrFail($data->marital_status_id) : $marital_status;
        $props['prop_marital_status'] = $marital_status->toViewApiOnlies('id','name','flag','label');

        $religion = $new->ReligionModel();
        $religion = (isset($data->religion_id)) ? $religion->findOrFail($data->religion_id) : $religion;
        $props['prop_religion'] = $religion->toViewApiOnlies('id','name','flag','label');
        return $data;
    }
}