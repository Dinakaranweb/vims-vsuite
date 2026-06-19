<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==================== HOD Users (Document Creators) ====================
        
        // Clinical Department HODs
        User::create([
            'name' => 'Dr. Rajesh Kumar',
            'username' => 'dr_rajesh',
            'emp_id' => 'EMP001',
            'designation' => 'HOD - Cardiology',
            'department' => 'Cardiology',
            'division' => 'Clinical',
            'role' => 'HOD',
            'email' => 'hod.cardiology@hospital.com',
            'phone' => '9876543210',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Dr. Priya Sharma',
            'username' => 'dr_priya',
            'emp_id' => 'EMP002',
            'designation' => 'HOD - Neurology',
            'department' => 'Neurology',
            'division' => 'Clinical',
            'role' => 'HOD',
            'email' => 'hod.neurology@hospital.com',
            'phone' => '9876543211',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Dr. Senthil Kumar',
            'username' => 'dr_senthil',
            'emp_id' => 'EMP003',
            'designation' => 'HOD - Orthopedics',
            'department' => 'Orthopedics',
            'division' => 'Clinical',
            'role' => 'HOD',
            'email' => 'hod.orthopedics@hospital.com',
            'phone' => '9876543212',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // Non-Clinical Department HODs
        User::create([
            'name' => 'Mr. Arun Prakash',
            'username' => 'arun_prakash',
            'emp_id' => 'EMP004',
            'designation' => 'HOD - Administration',
            'department' => 'Administration',
            'division' => 'Non Clinical',
            'role' => 'HOD',
            'email' => 'hod.administration@hospital.com',
            'phone' => '9876543213',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Mr. Venkatesh Rao',
            'username' => 'venkatesh_rao',
            'emp_id' => 'EMP005',
            'designation' => 'HOD - Human Resources',
            'department' => 'Human Resources',
            'division' => 'Non Clinical',
            'role' => 'HOD',
            'email' => 'hod.hr@hospital.com',
            'phone' => '9876543214',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Ms. Kavitha S',
            'username' => 'kavitha_s',
            'emp_id' => 'EMP006',
            'designation' => 'HOD - Finance',
            'department' => 'Finance',
            'division' => 'Non Clinical',
            'role' => 'HOD',
            'email' => 'hod.finance@hospital.com',
            'phone' => '9876543215',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);

        // ==================== SuperAdmin Users (Approvers) ====================
        
        // Medical Director (First approver for Clinical departments)
        User::create([
            'name' => 'Dr. Anitha Krishnan',
            'username' => 'dr_anitha',
            'emp_id' => 'SA001',
            'designation' => 'Medical Director',
            'department' => 'Medical Director',
            'division' => 'Clinical',
            'role' => 'SuperAdmin',
            'email' => 'medical.director@hospital.com',
            'phone' => '9876543216',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // General Manager (First approver for Non-Clinical departments)
        User::create([
            'name' => 'Mr. Ramesh Chandran',
            'username' => 'ramesh_c',
            'emp_id' => 'SA002',
            'designation' => 'General Manager',
            'department' => 'General Manager',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'general.manager@hospital.com',
            'phone' => '9876543217',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // General Manager - Admin
        User::create([
            'name' => 'Mr. Suresh Babu',
            'username' => 'suresh_babu',
            'emp_id' => 'SA003',
            'designation' => 'General Manager - Admin',
            'department' => 'General Manager - Admin',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'gm.admin@hospital.com',
            'phone' => '9876543218',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // Purchase Head
        User::create([
            'name' => 'Mr. Prakash Raj',
            'username' => 'prakash_raj',
            'emp_id' => 'SA004',
            'designation' => 'Purchase Head',
            'department' => 'Purchase Head',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'purchase.head@hospital.com',
            'phone' => '9876543219',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // Purchase Head Chennai (For high value purchases > 2 Lakhs)
        User::create([
            'name' => 'Mr. Murugan S',
            'username' => 'murugan_s',
            'emp_id' => 'SA005',
            'designation' => 'Purchase Head - Chennai',
            'department' => 'Purchase Head Chennai',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'purchase.chennai@hospital.com',
            'phone' => '9876543220',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // Finance Head Salem
        User::create([
            'name' => 'Mr. Sundararajan',
            'username' => 'sundar',
            'emp_id' => 'SA006',
            'designation' => 'Finance Head',
            'department' => 'Finance Head Salem',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'finance.head@hospital.com',
            'phone' => '9876543221',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);

        // Finance Head Chennai
        User::create([
            'name' => 'Mr. Rajendran P',
            'username' => 'rajendran_fh',
            'emp_id' => 'SA012',
            'designation' => 'Finance Head',
            'department' => 'Finance Head Chennai',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'finance.chennai@hospital.com',
            'phone' => '9876543232',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);

        // Finance Head Karaikal
        User::create([
            'name' => 'Mr. Murugesan K',
            'username' => 'murugesan_fh',
            'emp_id' => 'SA013',
            'designation' => 'Finance Head',
            'department' => 'Finance Head Karaikal',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'finance.karaikal@hospital.com',
            'phone' => '9876543233',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);

        // Finance Head Pondicherry
        User::create([
            'name' => 'Mr. Anbazhagan S',
            'username' => 'anbazhagan_fh',
            'emp_id' => 'SA014',
            'designation' => 'Finance Head',
            'department' => 'Finance Head Pondy',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'finance.pondy@hospital.com',
            'phone' => '9876543234',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // STB Office (Multiple users - Parallel Approvers)
        User::create([
            'name' => 'Mr. Karthikeyan',
            'username' => 'karthik_stb',
            'emp_id' => 'SA007',
            'designation' => 'STB Officer',
            'department' => 'STB Office',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'stb.officer1@hospital.com',
            'phone' => '9876543222',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Mr. Balasubramanian',
            'username' => 'bala_stb',
            'emp_id' => 'SA008',
            'designation' => 'STB Officer',
            'department' => 'STB Office',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'stb.officer2@hospital.com',
            'phone' => '9876543223',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        // Chairman Office (Multiple users - Parallel Approvers)
        User::create([
            'name' => 'Dr. Vijayakumar',
            'username' => 'vijay_chairman',
            'emp_id' => 'SA009',
            'designation' => 'Chairman',
            'department' => 'Chairman',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'chairman@hospital.com',
            'phone' => '9876543224',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Mr. Anand Raj',
            'username' => 'anand_ea',
            'emp_id' => 'SA010',
            'designation' => 'Executive Assistant',
            'department' => 'Chairman',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'ea.chairman@hospital.com',
            'phone' => '9876543225',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);

        // PA to Chairman (Final approver for high-value purchase documents)
        User::create([
            'name' => 'Mr. Selvaraj N',
            'username' => 'selvaraj_pa',
            'emp_id' => 'SA015',
            'designation' => 'PA to Chairman',
            'department' => 'PA to Chairman',
            'division' => 'Non Clinical',
            'role' => 'SuperAdmin',
            'email' => 'pa.chairman@hospital.com',
            'phone' => '9876543235',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);

        // Students Welfare (Payment Processing)
        User::create([
            'name' => 'Mr. Saravanan',
            'username' => 'saravanan_sw',
            'emp_id' => 'SA011',
            'designation' => 'Students Welfare Officer',
            'department' => 'Students Welfare',
            'division' => 'Non Clinical',
            'role' => 'HOD',
            'email' => 'students.welfare@hospital.com',
            'phone' => '9876543226',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);

        // ==================== Staff Users (Regular Employees) ====================
        
        User::create([
            'name' => 'Mr. Kamal Raj',
            'username' => 'kamal_r',
            'emp_id' => 'STAFF001',
            'designation' => 'Staff Nurse',
            'department' => 'Cardiology',
            'division' => 'Clinical',
            'role' => 'User',
            'email' => 'staff.nurse@hospital.com',
            'phone' => '9876543227',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Ms. Deepa R',
            'username' => 'deepa_r',
            'emp_id' => 'STAFF002',
            'designation' => 'Lab Technician',
            'department' => 'Neurology',
            'division' => 'Clinical',
            'role' => 'User',
            'email' => 'lab.tech@hospital.com',
            'phone' => '9876543228',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Mr. Vignesh S',
            'username' => 'vignesh_s',
            'emp_id' => 'STAFF003',
            'designation' => 'Administrative Assistant',
            'department' => 'Administration',
            'division' => 'Non Clinical',
            'role' => 'User',
            'email' => 'admin.assistant@hospital.com',
            'phone' => '9876543229',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Ms. Divya K',
            'username' => 'divya_k',
            'emp_id' => 'STAFF004',
            'designation' => 'HR Assistant',
            'department' => 'Human Resources',
            'division' => 'Non Clinical',
            'role' => 'User',
            'email' => 'hr.assistant@hospital.com',
            'phone' => '9876543230',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
        
        User::create([
            'name' => 'Mr. Manikandan',
            'username' => 'mani',
            'emp_id' => 'STAFF005',
            'designation' => 'Accounts Assistant',
            'department' => 'Finance',
            'division' => 'Non Clinical',
            'role' => 'User',
            'email' => 'accounts@hospital.com',
            'phone' => '9876543231',
            'password' => Hash::make('password'),
            'is_active' => 1,
        ]);
    }
}