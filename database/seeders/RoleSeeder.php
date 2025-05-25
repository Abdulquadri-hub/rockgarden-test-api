<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $role = Role::create(['name' => 'Developer']);
        $role = Role::create(['name' => 'Administrators']);
        $role = Role::create(['name' => 'Registered']);
        $role = Role::create(['name' => 'Client']);
        $role = Role::create(['name' => 'Care Giver']);
        $role = Role::create(['name' => 'Nurse']);
        $role = Role::create(['name' => 'Nurse Assistant']);
        $role = Role::create(['name' => 'Physiotherapist']);
        $role = Role::create(['name' => 'Doctor']);
    }
}

