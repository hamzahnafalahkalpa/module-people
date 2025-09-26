<?php

namespace Hanafalah\ModulePeople\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class ReligionSeeder extends Seeder{
    use HasRequestData;

    protected $datas = [
        ['name' => 'Islam'],
        ['name' => 'Kristen Protestan'],
        ['name' => 'Kristen Katolik'],
        ['name' => 'Hindu'],
        ['name' => 'Budha'],
        ['name' => 'Konghucu'],
        ['name' => 'Atheis'],
        ['name' => 'Aliran Kepercayaan'],
        ['name' => 'Lainnya']
    ];

    public function run()
    {
        foreach ($this->datas as $data) {
            app(config('app.contracts.Religion'))->prepareStoreReligion(
                $this->requestDTO(config('app.contracts.ReligionData'), $data)
            );
        }
    }
}
