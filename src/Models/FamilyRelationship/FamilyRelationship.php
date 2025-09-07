<?php

namespace Hanafalah\ModulePeople\Models\FamilyRelationship;

use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModulePeople\Resources\FamilyRelationship\{
    ViewFamilyRelationship, ShowFamilyRelationship
};
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class FamilyRelationship extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $list = [
        'id', 
        'people_id', 
        'name', 
        'sex', 
        'phone', 
        'family_role_id', 
        'reference_id', 
        'reference_type', 
        'props'
    ];

    public function people(){return $this->belongsToModel('People');}
    public function reference(){return $this->morphTo();}
    public function familyRole(){return $this->belongsToModel('FamilyRole');}
    public function getViewResource(){return ViewFamilyRelationship::class;}
    public function getShowResource(){return ShowFamilyRelationship::class;}
}
