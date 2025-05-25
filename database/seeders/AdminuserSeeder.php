<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminuserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = User::create([
            'first_name'  => 'Super',
            'last_name'  => 'Admin',
            'email' => 'superadmin@admin.com',
            'is_admin'  => 1,
            'is_verified'  => 1,
            'password' => Hash::make('abc123')
        ]);
        $user->assignRole('Administrators');

    }
}
