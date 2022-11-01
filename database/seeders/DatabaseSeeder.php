<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Post;

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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'view-user']);
        Permission::create(['name' => 'delete-user']);
        Permission::create(['name' => 'create-user']);
        Permission::create(['name' => 'publish-post']);
        Permission::create(['name' => 'delete-post']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Student', 'need_key' => false]);
        $role2 = Role::create(['name' => 'Teacher', 'need_key' => false]);
        $role3 = Role::create(['name' => 'Organization', 'need_key' => true]);
        $role4 = Role::create(['name' => 'Manager', 'need_key' => true]);
        $role5 = Role::create(['name' => 'Admin', 'need_key' => true]);

        $role1->givePermissionTo('view-user');

        Post::factory()->create();
        $user1 = User::factory()->withKnowEmail('test@example.es')->create();
        $user1->givePermissionTo('publish-post');
        $user1->assignRole($role1);
    }
}
