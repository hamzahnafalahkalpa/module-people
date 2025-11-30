<?php 

namespace Hanafalah\ModulePeople\Database\Seeders;

use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $educationModel = app(config('database.models.Education'));

        $data = [
            ['label' => 'Nil', 'name' => 'Tidak Sekolah'],
            ['label' => 'None', 'name' => 'Belum Sekolah'],
            ['label' => 'SD', 'name' => 'Sekolah Dasar'],
            ['label' => 'SMP', 'name' => 'Sekolah Menengah Pertama'],
            ['label' => 'SMA', 'name' => 'Sekolah Menengah Atas'],
            ['label' => 'D3', 'name' => 'Diploma 3'],
            ['label' => 'S1', 'name' => 'Sarjana'],
            ['label' => 'S2', 'name' => 'Magister'],
            ['label' => 'S3', 'name' => 'Doktor'],
        ];

        foreach ($data as $item) {
            $educationModel::withoutGlobalScopes()->updateOrCreate([
                'name' => $item['name'],
                'flag' => 'Education',
                'label' => $item['label']
            ]);
        }
    }
}