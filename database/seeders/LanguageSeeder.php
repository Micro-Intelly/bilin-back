<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::factory()->withLanCode('English (United States)','en-us')->create();
        Language::factory()->withLanCode('Spanish (Spain)','es')->create();
        Language::factory()->withLanCode('French (Standard)','fr')->create();
        Language::factory()->withLanCode('Italian (Standard)','it')->create();
        Language::factory()->withLanCode('Japanese','ja')->create();
        Language::factory()->withLanCode('Chinese (PRC)','zh-cn')->create();
        Language::factory()->withLanCode('German (Standard)','de')->create();
        Language::factory()->withLanCode('Portuguese (Portugal)','pt')->create();
        Language::factory()->withLanCode('Russian','ru')->create();
    }
}
