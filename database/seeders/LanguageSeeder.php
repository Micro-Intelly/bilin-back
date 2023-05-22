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
            ->withImage('storage/image/application/en-us/uk-england-london-metropolitan-area-urban-area-city-1588229-pxhere.com.jpg')
            ->withImageMini('storage/image/application/en-us/architecture-skyline-town-building-city-skyscraper-75745-pxhere.com.jpg')
            ->create();
        Language::factory()->withLanCode('Spanish','es')
            ->withPhrase('Si puedes imaginarlo puedes lograrlo, si puedes soñarlo, puedes hacerlo realidad.')
            ->withImage('storage/image/application/es/skyline-street-town-city-cityscape-panorama-941600-pxhere.com.jpg')
            ->withImageMini('storage/image/application/es/architecture-town-building-city-urban-cityscape-1332950-pxhere.com.jpg')
            ->create();
        Language::factory()->withLanCode('French','fr')
            ->withPhrase('On ne voit bien qu’avec le coeur. L’essentiel est invisible pour les yeux')
            ->withImage('storage/image/application/fr/landscape-horizon-cloud-sky-sunrise-sunset-814430-pxhere.com.jpg')
            ->withImageMini('storage/image/application/fr/sea-coast-water-ocean-architecture-skyline-948365-pxhere.com.jpg')
            ->create();
        Language::factory()->withLanCode('Italian','it')
            ->withPhrase('Odi, veti et tace, se voi vivir in pace.')
            ->withImage('storage/image/application/it/landscape-architecture-skyline-photography-town-city-1223282-pxhere.com.jpg')
            ->withImageMini('storage/image/application/it/old-city-cityscape-europe-evening-landmark-980413-pxhere.com.jpg')
            ->create();
        Language::factory()->withLanCode('Japanese','ja')
            ->withPhrase('君のいない毎日なんて、考えられないよ')
            ->withImage('storage/image/application/ja/pedestrian-architecture-people-road-street-night-981164-pxhere.com.jpg')
            ->withImageMini('storage/image/application/ja/Osaka-Castle-Japan-www.istockphoto.com_gb_photo_osaka-castle-osaka-japan-gm474272268-64549069-Ikuni-1.webp')
            ->create();
        Language::factory()->withLanCode('Chinese','zh-cn')
            ->withPhrase('水滴石穿，绳锯木断。')
            ->withImage('storage/image/application/zh-cn/roof-gcac1e6804_1920.jpg')
            ->withImageMini('storage/image/application/zh-cn/city-g1800c0504_1920.jpg')
            ->create();
        Language::factory()->withLanCode('German','de')
            ->withPhrase('Aller guten Dinge sind drei. Todas las buenas cosas vienen de tres en tres.')
            ->withImage('storage/image/application/de/6903531-germany-landscape.jpg')
            ->withImageMini('storage/image/application/de/6903591-germany-city-landscape.jpg')
            ->create();
        Language::factory()->withLanCode('Portuguese','pt')
            ->withPhrase('Para bom entendedor, meia palavra basta')
            ->withImage('storage/image/application/pt/landscape-horizon-sunset-town-city-urban-606521-pxhere.com.jpg')
            ->withImageMini('storage/image/application/pt/skyline-hill-town-building-city-cityscape-967832-pxhere.com.jpg')
            ->create();
        Language::factory()->withLanCode('Russian','ru')
            ->withPhrase('Авось да как-нибудь до добра не доведут')
            ->withImage('storage/image/application/ru/river-gcc4835fde_1920.jpg')
            ->withImageMini('storage/image/application/ru/city-g4afc97703_1920.jpg')
            ->create();
    }
}
