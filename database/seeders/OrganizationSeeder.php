<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        Organization::factory()->count(10)->create();
        Organization::factory()->withName('EduPlus')->create();
        Organization::factory()->withName('Language Academy')->create();
        Organization::factory()->withName('Learning Camp')->create();
    }
}
