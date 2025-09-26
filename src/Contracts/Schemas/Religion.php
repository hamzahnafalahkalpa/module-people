<?php

namespace Hanafalah\ModulePeople\Contracts\Schemas;

use Hanafalah\ModulePeople\Contracts\Data\ReligionData;
//use Hanafalah\ModulePeople\Contracts\Data\PeopleStuffUpdateData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModulePeople\Schemas\Religion
 * @method mixed export(string $type)
 * @method self setParamLogic(string $logic, bool $search_value = false, ?array $optionals = [])
 * @method self conditionals(mixed $conditionals)
 * @method array updateReligion(?ReligionData $religion_dto = null)
 * @method Model prepareUpdateReligion(ReligionData $religion_dto)
 * @method bool deleteReligion()
 * @method bool prepareDeleteReligion(? array $attributes = null)
 * @method mixed getReligion()
 * @method ?Model prepareShowReligion(?Model $model = null, ?array $attributes = null)
 * @method array showReligion(?Model $model = null)
 * @method Collection prepareViewReligionList()
 * @method array viewReligionList()
 * @method LengthAwarePaginator prepareViewReligionPaginate(PaginateData $paginate_dto)
 * @method array viewReligionPaginate(?PaginateData $paginate_dto = null)
 * @method array storeReligion(?ReligionData $religion_dto = null);
 */

interface Religion extends PeopleStuff
{
    public function prepareStoreReligion(ReligionData $religion_dto): Model;
    public function religion(mixed $conditionals = null): Builder;
}