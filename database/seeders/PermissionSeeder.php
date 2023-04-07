<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
        Permission::create(['name' => 'delete-user']);
        Permission::create(['name' => 'manage-self-user']);
        Permission::create(['name' => 'manage-org-user']);
        Permission::create(['name' => 'manage-user']);
        Permission::create(['name' => 'manage-self-post']);
        Permission::create(['name' => 'manage-post']);
        Permission::create(['name' => 'manage-self-comment']);
        Permission::create(['name' => 'manage-comment']);
        Permission::create(['name' => 'manage-self-test']);
        Permission::create(['name' => 'manage-test']);
        Permission::create(['name' => 'manage-self-series']);
        Permission::create(['name' => 'manage-series']);
    }
}
