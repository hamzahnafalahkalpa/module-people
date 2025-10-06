<?php

namespace Hanafalah\ModulePeople\Models\People;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\LaravelSupport\Concerns\Support\HasPhone;
use Hanafalah\ModuleCardIdentity\Concerns\HasCardIdentity;
use Hanafalah\ModuleUser\Concerns\UserReference\HasUserReference;
use Hanafalah\ModulePeople\Enums\People\BloodType;
use Hanafalah\ModulePeople\Resources\People\ShowPeople;
use Hanafalah\ModulePeople\Resources\People\ViewPeople;
use Hanafalah\ModuleRegional\Concerns\HasAddress;
use Hanafalah\ModuleRegional\Concerns\HasLocation;

class People extends BaseModel
{
    use HasUlids, HasCardIdentity, HasAddress, 
        HasUserReference, HasProps, HasLocation, HasPhone;

    protected $table          = 'peoples';
    public $incrementing      = false;
    protected $keyType        = "string";
    protected $primaryKey     = "id";
    protected $list           = ['id', 'uuid', 'name', 'sex', 'dob', 'pob'];
    protected $show           = [
        'last_education_id', 'father_name', 'mother_name', 
        'blood_type', 'first_name', 'last_name', 
        'country_id', 'total_children', 'marital_status_id'
    ];

    protected $casts = [
        'name'              => 'string',
        'first_name'        => 'string',
        'last_name'         => 'string',
        'sex'               => 'string'
    ];

    protected $prop_attributes = [];

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return [
            'familyRelationship'
        ];
    }

    public function getViewResource(){
        return ViewPeople::class;
    }

    public function getShowResource(){
        return ShowPeople::class;
    }

    public function getBloodTypes(): array{
        return array_column(BloodType::cases(), 'value');
    }

    public function country(){
        return $this->belongsToModel('Country');
    }

    public function addresses(){
        return $this->morphManyModel('Address', 'model');
    }

    public function familyRelationship(){
        return $this->hasOneModel('FamilyRelationship','people_id');
    }
}
