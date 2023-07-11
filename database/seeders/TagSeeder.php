<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::factory()->withName('Vocabulary')->create();
        Tag::factory()->withName('Grammar')->create();
        Tag::factory()->withName('Listening')->create();
        Tag::factory()->withName('Writing')->create();
        Tag::factory()->withName('Speaking')->create();
        Tag::factory()->withName('Reading')->create();
        Tag::factory()->withName('Business')->create();
        Tag::factory()->withName('General')->create();
        Tag::factory()->withName('Schools')->create();
        Tag::factory()->withName('Tactic')->create();
    }
}
