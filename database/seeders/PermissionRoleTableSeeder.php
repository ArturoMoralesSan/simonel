<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use DB;
class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate(['permission_role']);

        $roles = Role::all(['id', 'key_name'])->pluck('id', 'key_name');
        $permissions = Permission::all(['id', 'key_name'])->pluck('id', 'key_name');

        foreach ($this->getData() as $role => $permissionKeys) {
            foreach ($permissionKeys as $permission) {
                DB::table('permission_role')->insert([
                    'role_id'       => $roles[$role],
                    'permission_id' => $permissions[$permission]
                ]);
            }
        };
    }


    /**
     * Return the data to populate table.
     *
     * @return array
     */
    private function getData()
    {
        return [
            'customer' => [
                'update.profile',
                'update.password',
            ]
        ];
    }
}
