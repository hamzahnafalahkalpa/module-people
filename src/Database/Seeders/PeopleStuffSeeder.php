<?php 

namespace Hanafalah\ModulePeople\Database\Seeders;

use Illuminate\Database\Seeder;

class PeopleStuffSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $peopleStuffModel = app(config('database.models.PeopleStuff'));

        $data = [
            ['label' => 'Kawin', 'flag' => 'MaritalStatus', 'name' => 'Kawin'],
            ['label' => 'Belum Kawin', 'flag' => 'MaritalStatus', 'name' => 'Belum Kawin'],
            ['label' => 'Cerai Mati', 'flag' => 'MaritalStatus', 'name' => 'Cerai Mati'],
            ['label' => 'Cerai Hidup', 'flag' => 'MaritalStatus', 'name' => 'Cerai Hidup'],
            ['label' => 'Duda', 'flag' => 'MaritalStatus', 'name' => 'Duda'],
            ['label' => 'Janda', 'flag' => 'MaritalStatus', 'name' => 'Janda'],
        ];

        foreach ($data as $item) {
            $peopleStuffModel::updateOrCreate([
                'name' => $item['name'],
                'flag' => $item['flag'] ?? 'PeopleStuff',
                'label' => $item['label']
            ]);
        }
    }
}