<?php

namespace App\Http\Controllers\Admin;


use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;


class MakeRoleController extends Controller
{
    public function addRole()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'user']);
    }

    public function addPermissionUser()
    {
        Permission::create(['name' => 'add_user']);
        Permission::create(['name' => 'edit_user']);
        Permission::create(['name' => 'delete_user']);
    }

    public function addPermissionLink()
    {
        Permission::create(['name' => 'add_link']);
        Permission::create(['name' => 'edit_link']);
        Permission::create(['name' => 'delete_link']);
    }

    public function roleAsPermissionUser()
    {
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo('add user');
        $admin->givePermissionTo('edit user');
        $admin->givePermissionTo('delete user');

        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo('edit user');
    }

    public function roleAsPermissionLink()
    {
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo('add link');
        $admin->givePermissionTo('edit link');
        $admin->givePermissionTo('delete link');


        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo('edit link');
    }
}
