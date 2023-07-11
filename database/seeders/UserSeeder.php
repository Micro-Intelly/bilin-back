<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\File;
use App\Models\Language;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Section;
use App\Models\Serie;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
//        $studentUserList->each(function ($user) use ($organizations) {
//            $user->organizations()->attach(
//                $organizations->random(rand(1, $organizations->count()))->pluck('id')->toArray()
//            );
//        });
        $teacherUser = User::factory()->withKnowEmail('teacher@example.es')->create();
        $teacherUser->assignRole($teacher);
        $teacherUserList = collect();
        for($i = 0; $i < 10; $i++){
            $teacherUserAux = User::factory()->withKnowEmail('teacher'.$i.'@example.es')->create();
            $teacherUserAux->assignRole($teacher);
            $teacherUserList->add($teacherUserAux);
        }
//        $teacherUserList->each(function ($user) use ($organizations) {
//            $user->organizations()->attach(
//                $organizations->random(rand(1, $organizations->count()))->pluck('id')->toArray()
//            );
//        });
        /** @var User $orgUser */
        $orgUser = User::factory()->withKnowEmail('org@example.es')->withKnowOrg($organizations[0])->create();
        $orgUser->assignRole($organization);
        $orgUserList = collect();
        for($i = 1; $i < $organizations->count(); $i++){
            $mainOrg = $organizations->random();
            $orgUserAux = User::factory()->withKnowEmail('org'.$i.'@example.es')->withKnowOrg($organizations[$i])->create();
            $orgUserAux->assignRole($organization);
            $orgUserList->add($orgUserAux);
        }
        $managerUser = User::factory()->withKnowEmail('manager@example.es')->create();
        $managerUser->assignRole($manager);
        $adminUser = User::factory()->withKnowEmail('admin@example.es')->create();
        $adminUser->assignRole($admin);
    }
}
