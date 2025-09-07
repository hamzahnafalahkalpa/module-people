<?php

namespace Hanafalah\ModulePeople\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePeople\Contracts\Data\FamilyRelationshipData as DataFamilyRelationshipData;
use Hanafalah\ModulePeople\Contracts\Data\FamilyRoleData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class FamilyRelationshipData extends Data implements DataFamilyRelationshipData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('people_id')]
    #[MapName('people_id')]
    public mixed $people_id = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public ?string $name = null;

    #[MapInputName('family_role_id')]
    #[MapName('family_role_id')]
    public mixed $family_role_id = null;

    #[MapInputName('family_role')]
    #[MapName('family_role')]
    public ?FamilyRoleData $family_role = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;
    
    #[MapInputName('phone')]
    #[MapName('phone')]
    public ?string $phone = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function after(self $data): self{
        $new = self::new();

        $props = &$data->props;

        if (isset($data->family_role)){
            $data->family_role->flag = 'FamilyRole';
            $family_role = app(config('app.contracts.FamilyRole'))->prepareStoreFamilyRole($data->family_role);
            $data->family_role_id = $family_role->getKey();
        }else{
            $family_role = $new->FamilyRoleModel();
            $family_role = (isset($data->family_role_id)) ? $family_role->findOrFail($data->family_role_id) : $family_role;
        }

        $props['prop_family_role'] = $family_role->toViewApiOnlies('id','name','flag','label');
        return $data;
    }
}