<?php

namespace Database\Seeders;

use App\Constants\PermissionConstant;
use App\Models\Default\Permission;
use App\Models\Default\Role;
use App\Models\Default\Setting;
use App\Models\Default\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['id' => Str::ulid(), 'key' => 'app_name', 'value' => 'Daisy UI App', 'type' => 'text'],
            ['id' => Str::ulid(), 'key' => 'app_logo', 'value' => '', 'type' => 'image'],
        ];

        Setting::insert($settings);

        foreach (PermissionConstant::LIST as $permission) {
            Permission::insert(['id' => Str::ulid(), ...$permission]);
        }

        $role = Role::create(['name' => 'admin']);

        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $role->rolePermissions()->create(['permission_id' => $permission->id]);
        }

        User::create([
            'name' => 'Super Administrator',
            'email' => 'root@admin.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Administator',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $guest = Role::create(['name' => Role::GUEST]);
        $permission = Permission::where('name', 'view-shortlink')->first();
        $guest->rolePermissions()->create(['permission_id' => $permission->id]);
    }
}
