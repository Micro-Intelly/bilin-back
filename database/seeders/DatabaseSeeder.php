<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Episode;
use App\Models\Favorite;
use App\Models\File;
use App\Models\History;
use App\Models\Language;
use App\Models\Organization;
use App\Models\Question;
use App\Models\Result;
use App\Models\Section;
use App\Models\Serie;
use App\Models\Tag;
use App\Models\Test;
use Database\Factories\FavoriteFactory;
use Database\Factories\SerieFactory;
use Database\Factories\TestFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Post;
use function Sodium\add;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Reset cached roles and permissions
        $this->call([
            PermissionSeeder::class,
            LanguageSeeder::class,
            TagSeeder::class,
            OrganizationSeeder::class,
        ]);
        $tags = Tag::all();
        $organizations = Organization::all();
        $languages = Language::all();
        $orgWithNull = clone $organizations;
        $orgWithNull->add(null);

        // create roles and assign existing permissions
        $student = Role::create(['name' => 'Student', 'need_key' => false]);
        $teacher = Role::create(['name' => 'Teacher', 'need_key' => false]);
        $organization = Role::create(['name' => 'Organization', 'need_key' => true]);
        $manager = Role::create(['name' => 'Manager', 'need_key' => true]);
        $admin = Role::create(['name' => 'Admin', 'need_key' => true]);

        $student->syncPermissions(['manage-self-user','manage-self-post', 'manage-self-comment']);
        $teacher->syncPermissions(['manage-self-user','manage-self-post', 'manage-self-comment',
            'manage-self-series', 'manage-self-test']);
        $organization->syncPermissions(['manage-self-user','manage-org-user','manage-self-post', 'manage-self-comment',
            'manage-self-series', 'manage-self-test']);
        $manager->syncPermissions(['manage-self-user','manage-self-post', 'manage-self-comment',
            'manage-self-series', 'manage-self-test','manage-user','manage-post','manage-comment','manage-test','manage-series']);
        $admin->syncPermissions(['manage-self-user','manage-self-post', 'manage-self-comment',
            'manage-self-series', 'manage-self-test','manage-user','manage-post','manage-comment','manage-test',
            'manage-series','delete-user']);

        // Users
        $studentUser = User::factory()->withKnowEmail('student@example.es')->create();
        $studentUser->assignRole($student);
        $studentUserList = collect();
        for($i = 0; $i < 10; $i++){
            $studentUserAux = User::factory()->withKnowEmail('student'.$i.'@example.es')->create();
            $studentUserAux->assignRole($student);
            $studentUserList->add($studentUserAux);
        }
        $studentUserList->each(function ($user) use ($organizations) {
            $user->organizations()->attach(
                $organizations->random(rand(1, $organizations->count()))->pluck('id')->toArray()
            );
        });
        $teacherUser = User::factory()->withKnowEmail('teacher@example.es')->create();
        $teacherUser->assignRole($teacher);
        $teacherUserList = collect();
        for($i = 0; $i < 10; $i++){
            $teacherUserAux = User::factory()->withKnowEmail('teacher'.$i.'@example.es')->create();
            $teacherUserAux->assignRole($teacher);
            $teacherUserList->add($teacherUserAux);
        }
        $teacherUserList->each(function ($user) use ($organizations) {
            $user->organizations()->attach(
                $organizations->random(rand(1, $organizations->count()))->pluck('id')->toArray()
            );
        });
        $teacherUserList->each(function ($user) use ($organizations) {
            $user->organizations()->attach(
                $organizations->random(rand(1, $organizations->count()))->pluck('id')->toArray()
            );
        });
        /** @var User $orgUser */
        $orgUser = User::factory()->withKnowEmail('org@example.es')->create();
        $orgUser->assignRole($organization);
        $orgUser->organizations()->save($organizations->random());
        $orgUserList = collect();
        for($i = 0; $i < 10; $i++){
            $orgUserAux = User::factory()->withKnowEmail('org'.$i.'@example.es')->create();
            $orgUserAux->assignRole($organization);
            $orgUserList->add($orgUserAux);
        }
        $orgUserList->each(function ($user) use ($organizations) {
            $user->organizations()->attach(
                $organizations->random()->pluck('id')->toArray()
            );
        });
        $orgUserList->each(function ($user) use ($organizations) {
            $user->organizations()->attach(
                $organizations->random(rand(1, $organizations->count()))->pluck('id')->toArray()
            );
        });
        $managerUser = User::factory()->withKnowEmail('manager@example.es')->create();
        $managerUser->assignRole($manager);
        $adminUser = User::factory()->withKnowEmail('admin@example.es')->create();
        $adminUser->assignRole($admin);

        // Series, Section, Episode, Comment, File
        $seriesList = collect();
        for($i = 0; $i < 10; $i++){
            $teacherSelected = $teacherUserList->random();
            $seriesAuxFactory = Serie::factory()
                ->withUser($teacherSelected)
                ->withLanguage($languages->random());
            $orgAux = $orgWithNull->random();
            if($orgAux != null){
                $seriesAuxFactory = $seriesAuxFactory->withOrg($orgAux);
            }
            /** @var Serie $seriesAux */
            $seriesAux = $seriesAuxFactory->create();
            $seriesAux->tags()->saveMany($tags->random(3));
            File::factory()->count(3)->withSeries($seriesAux)->create();
            Comment::factory()->count(2)->withUser($teacherSelected)->withCommentable($seriesAux)->isNote()->create();

            $sectionsList = Section::factory()->count(3)->withSeries($seriesAux)->create();
            $episodesList = collect();
            foreach($sectionsList as $section){
                $episodeAux = Episode::factory()->count(4)->withSection($section)->create();
                $episodesList = $episodesList->concat($episodeAux);
            }
            $comments = Comment::factory()->count(10)->withUser($studentUserList->random())->withCommentable($episodesList->random())->isComment()->create();
            $subComments = Comment::factory()->count(10)->withUser($studentUserList->random())->withRoot($comments->random())->isComment()->create();
            Comment::factory()->count(10)->withUser($studentUserList->random())->withInReplyTo($subComments->random())->isComment()->create();
            Comment::factory()->count(10)->withUser($studentUserList->random())->withCommentable($episodesList->random())->isNote()->create();

            $seriesList->add($seriesAux);
        }
        // Post, Comment
        /** @var Post $post */
        $post = Post::factory()->withUser($studentUser)->withLanguage($languages->random())->create();
        $post->tags()->saveMany($tags->random(3));
        $comments = Comment::factory()->count(10)->withUser($studentUser)->withCommentable($post)->isComment()->create();
        $subComments = Comment::factory()->count(10)->withUser($studentUser)->withRoot($comments->random())->isComment()->create();
        Comment::factory()->count(10)->withUser($studentUser)->withInReplyTo($subComments->random())->isComment()->create();

        $postsList = collect();
        for($i = 0; $i < 10; $i++){
            /** @var Post $postAux */
            $postAux = Post::factory()->withUser($teacherUserList->random())->withLanguage($languages->random())->create();
            $postAux->tags()->saveMany($tags->random(3));

            $comments = Comment::factory()->count(10)->withUser($studentUserList->random())->withCommentable($postAux)->isComment()->create();
            $subComments = Comment::factory()->count(10)->withUser($studentUserList->random())->withRoot($comments->random())->isComment()->create();
            Comment::factory()->count(10)->withUser($studentUserList->random())->withInReplyTo($subComments->random())->isComment()->create();

            $postsList->add($postAux);
        }

        // Test, question, result, comment
        /** @var Test $test */
        $test = Test::factory()->withUser($teacherUserList->random())->withLanguage($languages->random())->create();
        $test->tags()->saveMany($tags->random(3));
        Question::factory()->count(5)->withTest($test)->create();
        Result::factory()->count(10)->withUser($studentUser)->withTest($test)->create();

        $testsList = collect();
        for($i = 0; $i < 10; $i++){
            $testAuxFactory = Test::factory()->withUser($teacherUserList->random())->withLanguage($languages->random())->withSeries($seriesList->random());
            $orgAux = $orgWithNull->random();
            if($orgAux != null){
                $testAuxFactory = $testAuxFactory->withOrg($orgAux);
            }
            /** @var Test $testAux */
            $testAux = $testAuxFactory->create();

            $testAux->tags()->saveMany($tags->random(3));
            Question::factory()->count(5)->withTest($testAux)->create();

            Result::factory()->count(10)->withUser($studentUserList->random())->withTest($testAux)->create();

            $comments = Comment::factory()->count(10)->withUser($studentUserList->random())->withCommentable($testAux)->isComment()->create();
            $subComments = Comment::factory()->count(10)->withUser($studentUserList->random())->withRoot($comments->random())->isComment()->create();
            Comment::factory()->count(10)->withUser($studentUserList->random())->withInReplyTo($subComments->random())->isComment()->create();

            $testsList->add($testAux);
        }

        // Favorite
//        Favorite::factory()->count(10)->withUser($studentUserList->random())->withFavoriteAble($postsList->random())->create();
//        Favorite::factory()->count(10)->withUser($studentUserList->random())->withFavoriteAble($seriesList->random())->create();
//        Favorite::factory()->count(10)->withUser($studentUserList->random())->withFavoriteAble($testsList->random())->create();

        // History
        History::factory()->count(10)->withUser($studentUserList->random())->withHistoryAble($postsList->random())->create();
        History::factory()->count(10)->withUser($studentUserList->random())->withHistoryAble($testsList->random())->create();
        History::factory()->count(10)->withUser($studentUserList->random())->withHistoryAble($seriesList->random()->episodes()->get()->random())->create();
    }
}
