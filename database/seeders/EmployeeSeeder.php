<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::create([
            'first_name'  => 'Employee 1',
            'last_name'  => 'Care Giver',
            'email' => 'caregiver@admin.com',
            'is_admin'  => 1,
            'is_verified'  => 1,
            'password' => Hash::make('abc123')
        ]);

        $user->assignRole('Care Giver');

        $employee = new Employee();
        $latestEmp = Employee::orderBy('created_at','DESC')->first();
        $employee->employee_no = 'EMP0000'.($latestEmp === null ? 1 : ($latestEmp->id + 1));
        $employee->user_id = $user->id;

        $employee->save();


        $user = User::create([
            'first_name'  => 'Employee 1',
            'last_name'  => 'Nurse Assistant',
            'email' => 'nurseassistant@admin.com',
            'is_admin'  => 1,
            'is_verified'  => 1,
            'password' => Hash::make('abc123')
        ]);

        $user->assignRole('Nurse Assistant');

        $employee = new Employee();
        $latestEmp = Employee::orderBy('created_at','DESC')->first();
        $employee->employee_no = 'EMP0000'.($latestEmp === null ? 1 : ($latestEmp->id + 1));
        $employee->user_id = $user->id;

        $employee->save();

        $user = User::create([
            'first_name'  => 'Employee 1',
            'last_name'  => 'Physiotherapist',
            'email' => 'physiotherapist@admin.com',
            'is_admin'  => 1,
            'is_verified'  => 1,
            'password' => Hash::make('abc123')
        ]);

        $user->assignRole('Physiotherapist');

        $employee = new Employee();
        $latestEmp = Employee::orderBy('created_at','DESC')->first();
        $employee->employee_no = 'EMP0000'.($latestEmp === null ? 1 : ($latestEmp->id + 1));
        $employee->user_id = $user->id;

        $employee->save();

        $user = User::create([
            'first_name'  => 'Employee 1',
            'last_name'  => 'Doctor',
            'email' => 'doctor@admin.com',
            'is_admin'  => 1,
            'is_verified'  => 1,
            'password' => Hash::make('abc123')
        ]);

        $user->assignRole('Doctor');

        $employee = new Employee();
        $latestEmp = Employee::orderBy('created_at','DESC')->first();
        $employee->employee_no = 'EMP0000'.($latestEmp === null ? 1 : ($latestEmp->id + 1));
        $employee->user_id = $user->id;

        $employee->save();

    }
}
