<?php 

namespace Hanafalah\ModulePeople\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PeopleStuffSeeder::class,
            EducationSeeder::class,
            FamilyRoleSeeder::class,
            ReligionSeeder::class
        ]);
    }
}