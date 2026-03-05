<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create Permissions for Users
        $userPermissions = [
            'create users',
            'read users',
            'update users',
            'delete users',
            'list users',
        ];

        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Permissions for Posts
        $postPermissions = [
            'create posts',
            'read posts',
            'update posts',
            'delete posts',
            'list posts',
            'publish posts',
        ];

        foreach ($postPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Permissions for Comments
        $commentPermissions = [
            'create comments',
            'read comments',
            'update comments',
            'delete comments',
            'list comments',
        ];

        foreach ($commentPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Permissions for Analytics/Admin
        $adminPermissions = [
            'view analytics',
            'view reports',
            'manage settings',
            'manage roles',
            'manage permissions',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Super Admin Role - has all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'create users',
            'read users',
            'update users',
            'delete users',
            'list users',
            'manage settings',
            'manage roles',
            'view analytics',
            'view reports',
        ]);

        // Editor Role - can manage posts
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->syncPermissions([
            'create posts',
            'read posts',
            'update posts',
            'delete posts',
            'list posts',
            'publish posts',
            'create comments',
            'read comments',
            'update comments',
            'delete comments',
            'list comments',
        ]);

        // Author Role - can create and read posts
        $authorRole = Role::firstOrCreate(['name' => 'author']);
        $authorRole->syncPermissions([
            'create posts',
            'read posts',
            'update posts',
            'list posts',
            'create comments',
            'read comments',
            'list comments',
        ]);

        // Contributor Role - can read and comment
        $contributorRole = Role::firstOrCreate(['name' => 'contributor']);
        $contributorRole->syncPermissions([
            'read posts',
            'list posts',
            'create comments',
            'read comments',
            'list comments',
        ]);

        // User Role - basic read-only access
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'read posts',
            'list posts',
            'read comments',
            'list comments',
        ]);
    }
}
