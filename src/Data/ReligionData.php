<?php

namespace Hanafalah\ModulePeople\Data;

use Hanafalah\LaravelSupport\Data\UnicodeData;
use Hanafalah\ModulePeople\Contracts\Data\ReligionData as DataReligionData;

class ReligionData extends PeopleStuffData implements DataReligionData
{
    public static function before(array &$attributes){
        $attributes['flag'] ??= 'Religion';
        parent::before($attributes);
    }
}