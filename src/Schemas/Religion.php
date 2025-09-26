<?php

namespace Hanafalah\ModulePeople\Schemas;

use Hanafalah\LaravelSupport\Schemas\Unicode;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModulePeople\Contracts\Schemas\Religion as ContractsReligion;
use Hanafalah\ModulePeople\Contracts\Data\ReligionData;
use Illuminate\Database\Eloquent\Builder;

class Religion extends PeopleStuff implements ContractsReligion
{
    protected string $__entity = 'Religion';
    protected $__config_name = 'module-people';
    public $religion_model;
    protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'religion',
            'tags'     => ['religion', 'religion-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStoreReligion(ReligionData $religion_dto): Model{
        $religion = $this->prepareStorePeopleStuff($religion_dto);
        return $this->religion_model = $religion;
    }

    public function religion(mixed $conditionals = null): Builder{
        return $this->peopleStuff($conditionals);
    }
}