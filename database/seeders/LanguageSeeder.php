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
//        Language::factory()->withLanCode('English (United States)','en-us')->create();
//        Language::factory()->withLanCode('Spanish (Spain)','es')->create();
//        Language::factory()->withLanCode('French (Standard)','fr')->create();
//        Language::factory()->withLanCode('Italian (Standard)','it')->create();
//        Language::factory()->withLanCode('Japanese','ja')->create();
//        Language::factory()->withLanCode('Chinese (PRC)','zh-cn')->create();
//        Language::factory()->withLanCode('German (Standard)','de')->create();
//        Language::factory()->withLanCode('Portuguese (Portugal)','pt')->create();
//        Language::factory()->withLanCode('Russian','ru')->create();
        Language::factory()->withLanCode('English','en-us')
            ->withPhrase('Well done is better than well said.')
            ->create();
        Language::factory()->withLanCode('Spanish','es')
            ->withPhrase('Si puedes imaginarlo puedes lograrlo, si puedes soñarlo, puedes hacerlo realidad.')
            ->create();
        Language::factory()->withLanCode('French','fr')
            ->withPhrase('On ne voit bien qu’avec le coeur. L’essentiel est invisible pour les yeux')
            ->create();
        Language::factory()->withLanCode('Italian','it')
            ->withPhrase('Odi, veti et tace, se voi vivir in pace.')
            ->create();
        Language::factory()->withLanCode('Japanese','ja')
            ->withPhrase('君のいない毎日なんて、考えられないよ')
            ->create();
        Language::factory()->withLanCode('Chinese','zh-cn')
            ->withPhrase('水滴石穿，绳锯木断。')
            ->create();
        Language::factory()->withLanCode('German','de')
            ->withPhrase('Aller guten Dinge sind drei. Todas las buenas cosas vienen de tres en tres.')
            ->create();
        Language::factory()->withLanCode('Portuguese','pt')
            ->withPhrase('Para bom entendedor, meia palavra basta')
            ->create();
        Language::factory()->withLanCode('Russian','ru')
            ->withPhrase('Авось да как-нибудь до добра не доведут')
            ->create();
    }
}
