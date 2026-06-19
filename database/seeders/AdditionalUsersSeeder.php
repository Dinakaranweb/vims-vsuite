<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdditionalUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Finance Head Chennai
        User::updateOrCreate(
            ['email' => 'finance.chennai@hospital.com'],
            [
                'name'       => 'Mr. Rajendran P',
                'username'   => 'rajendran_fh',
                'emp_id'     => 'SA012',
                'designation'=> 'Finance Head',
                'department' => 'Finance Head Chennai',
                'division'   => 'Non Clinical',
                'role'       => 'SuperAdmin',
                'phone'      => '9876543232',
                'password'   => Hash::make('password'),
                'is_active'  => 1,
            ]
        );

        // Finance Head Karaikal
        User::updateOrCreate(
            ['email' => 'finance.karaikal@hospital.com'],
            [
                'name'       => 'Mr. Murugesan K',
                'username'   => 'murugesan_fh',
                'emp_id'     => 'SA013',
                'designation'=> 'Finance Head',
                'department' => 'Finance Head Karaikal',
                'division'   => 'Non Clinical',
                'role'       => 'SuperAdmin',
                'phone'      => '9876543233',
                'password'   => Hash::make('password'),
                'is_active'  => 1,
            ]
        );

        // Finance Head Pondicherry
        User::updateOrCreate(
            ['email' => 'finance.pondy@hospital.com'],
            [
                'name'       => 'Mr. Anbazhagan S',
                'username'   => 'anbazhagan_fh',
                'emp_id'     => 'SA014',
                'designation'=> 'Finance Head',
                'department' => 'Finance Head Pondy',
                'division'   => 'Non Clinical',
                'role'       => 'SuperAdmin',
                'phone'      => '9876543234',
                'password'   => Hash::make('password'),
                'is_active'  => 1,
            ]
        );

        // PA to Chairman
        User::updateOrCreate(
            ['email' => 'pa.chairman@hospital.com'],
            [
                'name'       => 'Mr. Selvaraj N',
                'username'   => 'selvaraj_pa',
                'emp_id'     => 'SA015',
                'designation'=> 'PA to Chairman',
                'department' => 'PA to Chairman',
                'division'   => 'Non Clinical',
                'role'       => 'SuperAdmin',
                'phone'      => '9876543235',
                'password'   => Hash::make('password'),
                'is_active'  => 1,
            ]
        );
    }
}
