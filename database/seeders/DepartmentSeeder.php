<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Two sources combined: the real VMRF-DU department list (source: production `vsuite`
     * database - covers the approval-chain departments like Medical Director/General Manager/
     * Chairman/STB Office), plus the VIMS Hospitals departments from StaffDirectorySeeder that
     * aren't already in that list (e.g. Biomedical, Blood Bank) - both are needed for the
     * document Forward-to department search/autocomplete to find every real department.
     */
    public function run(): void
    {
        $departments = [
            ['dept_id' => 'G012', 'dept_name' => 'Information and Communication Technology', 'dept_label' => 'ICT'],
            ['dept_id' => 'DPT-9531', 'dept_name' => 'Human Resources', 'dept_label' => 'HR'],
            ['dept_id' => 'DPT-8426', 'dept_name' => 'General Manager - Admin', 'dept_label' => 'General Manager - Admin'],
            ['dept_id' => 'DPT-4625', 'dept_name' => 'Admission', 'dept_label' => 'Admission'],
            ['dept_id' => 'DPT-6352', 'dept_name' => 'Finance', 'dept_label' => 'Finance'],
            ['dept_id' => 'DPT-1536', 'dept_name' => 'General Manager', 'dept_label' => 'General Manager'],
            ['dept_id' => 'DPT-2156', 'dept_name' => 'Medical Director', 'dept_label' => 'Medical Director'],
            ['dept_id' => 'DPT-4932', 'dept_name' => 'Academic', 'dept_label' => 'Academic'],
            ['dept_id' => 'DPT-9513', 'dept_name' => 'Maintenance', 'dept_label' => 'Maintenance'],
            ['dept_id' => 'DPT-9546', 'dept_name' => 'Controller of Examination', 'dept_label' => 'COE'],
            ['dept_id' => 'DPT-1678', 'dept_name' => 'IQAC', 'dept_label' => 'IQAC'],
            ['dept_id' => 'DPT-9456', 'dept_name' => 'Students Welfare', 'dept_label' => 'Students Welfare'],
            ['dept_id' => 'DPT-1698', 'dept_name' => 'Research', 'dept_label' => 'Research'],
            ['dept_id' => 'DPT-4692', 'dept_name' => 'Innovation', 'dept_label' => 'Innovation'],
            ['dept_id' => 'DPT-9631', 'dept_name' => 'International Relation', 'dept_label' => 'International Relation'],
            ['dept_id' => 'DPT-4695', 'dept_name' => 'Clinical Trials', 'dept_label' => 'Clinical Trials'],
            ['dept_id' => 'DPT-1469', 'dept_name' => 'Accreditation', 'dept_label' => 'Accreditation'],
            ['dept_id' => 'DPT-4963', 'dept_name' => 'Alumni Relationship and Placement', 'dept_label' => 'Alumni Relationship and Placement'],
            ['dept_id' => 'DPT-1469', 'dept_name' => 'Property Management', 'dept_label' => 'Property Management'],
            ['dept_id' => 'DPT-3595', 'dept_name' => 'Vehicle', 'dept_label' => 'Vehicle'],
            ['dept_id' => 'DPT-4268', 'dept_name' => 'Public Relations Officer', 'dept_label' => 'PRO'],
            ['dept_id' => 'DPT-0156', 'dept_name' => 'KV Star Property Management', 'dept_label' => 'KV Star Property Management'],
            ['dept_id' => 'DPT-0587', 'dept_name' => 'KV Star Finance', 'dept_label' => 'KV Star Finance'],
            ['dept_id' => 'DPT-1595', 'dept_name' => 'DDE Examination', 'dept_label' => 'DDE Examination'],
            ['dept_id' => 'DPT-5963', 'dept_name' => 'Legal', 'dept_label' => 'Legal'],
            ['dept_id' => 'DPT-1596', 'dept_name' => 'PIO', 'dept_label' => 'PIO'],
            ['dept_id' => 'DPT-58741', 'dept_name' => 'Bank', 'dept_label' => 'Bank'],
            ['dept_id' => 'DPT-0365', 'dept_name' => 'Construction', 'dept_label' => 'Construction'],
            ['dept_id' => 'DPT-0258', 'dept_name' => 'Postal', 'dept_label' => 'Postal'],
            ['dept_id' => 'DPT-7264', 'dept_name' => 'Electrical', 'dept_label' => 'Electrical'],
            ['dept_id' => 'DPT-6244', 'dept_name' => 'Driver', 'dept_label' => 'Driver'],
            ['dept_id' => 'DPT-0365', 'dept_name' => 'Planning', 'dept_label' => 'Planning'],
            ['dept_id' => 'DPT-3281', 'dept_name' => 'VC Office', 'dept_label' => 'VC Office'],
            ['dept_id' => 'DPT-7992', 'dept_name' => 'Pro VC Office', 'dept_label' => 'Pro VC Office'],
            ['dept_id' => 'DPT-3665', 'dept_name' => 'PHD', 'dept_label' => 'PHD'],
            ['dept_id' => 'DPT-1125', 'dept_name' => 'Purchase', 'dept_label' => 'Purchase'],
            ['dept_id' => 'DPT-8845', 'dept_name' => 'VP Office', 'dept_label' => 'VP Office (Shri NVC)'],
            ['dept_id' => 'DPT-1111', 'dept_name' => 'Registrar Office', 'dept_label' => 'Registrar Office'],
            ['dept_id' => 'DPT-1112', 'dept_name' => 'Administration', 'dept_label' => 'Administration'],
            ['dept_id' => 'DPT-9800', 'dept_name' => 'Digital Library', 'dept_label' => 'Digital Library'],
            ['dept_id' => 'DPT-2015', 'dept_name' => 'Accreditation & Ranking (Medical)', 'dept_label' => 'Accreditation & Ranking (Medical)'],
            ['dept_id' => 'DPT-9634', 'dept_name' => 'DDE Admission & Finance', 'dept_label' => 'DDE Admission & Finance'],
            ['dept_id' => 'DPT-8505', 'dept_name' => 'Cottage', 'dept_label' => 'Cottage'],
            ['dept_id' => 'DPT-6479', 'dept_name' => 'Store', 'dept_label' => 'Store'],
            ['dept_id' => 'DPT - 1943', 'dept_name' => 'Research - Director (Chennai)', 'dept_label' => 'Research - Director (Chennai)'],
            ['dept_id' => 'DPT-0058', 'dept_name' => 'Research - Deputy Director (Chennai)', 'dept_label' => 'Research - Deputy Director (Chennai)'],
            ['dept_id' => 'DPT-2258', 'dept_name' => 'Deputy Registrar Admin (Chennai)', 'dept_label' => 'Deputy Registrar Admin (Chennai)'],
            ['dept_id' => 'DPT-3358', 'dept_name' => 'Joint Regsitrar (HR) Chennai', 'dept_label' => 'Joint Regsitrar (HR) Chennai'],
            ['dept_id' => 'DPT-1117', 'dept_name' => 'Salary', 'dept_label' => 'Salary'],
            ['dept_id' => 'DPT-2227', 'dept_name' => 'Accounts', 'dept_label' => 'Accounts'],
            ['dept_id' => 'DPT-0447', 'dept_name' => 'Manager (Planning & Coordination) - Library', 'dept_label' => 'Manager (Planning & Coordination) - Library'],
            ['dept_id' => 'DPT-1076', 'dept_name' => 'Bunglow-4', 'dept_label' => 'Bunglow-4'],
            ['dept_id' => 'DPT-6944', 'dept_name' => 'Manager - PRO', 'dept_label' => 'Manager - PRO'],
            ['dept_id' => 'DPT-0001', 'dept_name' => 'Bunglow-3', 'dept_label' => 'Bunglow-3'],
            ['dept_id' => 'DPT-0002', 'dept_name' => 'Bunglow-2', 'dept_label' => 'Bunglow-2'],
            ['dept_id' => 'DPT-0003', 'dept_name' => 'Bunglow-1', 'dept_label' => 'Bunglow-1'],
            ['dept_id' => 'DPT-11388', 'dept_name' => 'Professional Program', 'dept_label' => 'Professional Program'],
            ['dept_id' => 'DPT-9590', 'dept_name' => 'Communication', 'dept_label' => 'Communication'],
            ['dept_id' => 'DPT-CH-02', 'dept_name' => 'Chairman Office', 'dept_label' => 'Chairman Office'],
            ['dept_id' => 'DPT-1123', 'dept_name' => 'Research - Deputy Director (Salem)', 'dept_label' => 'Research - Deputy Director (Salem)'],
            ['dept_id' => 'DPT-CH-01', 'dept_name' => 'Chairman', 'dept_label' => 'Chairman'],
            ['dept_id' => 'DPT-02546', 'dept_name' => 'STB Office', 'dept_label' => 'STB Office'],
            ['dept_id' => 'VIMS-DEPT-001', 'dept_name' => 'Audit', 'dept_label' => 'Audit'],
            ['dept_id' => 'VIMS-DEPT-002', 'dept_name' => 'Billing', 'dept_label' => 'Billing'],
            ['dept_id' => 'VIMS-DEPT-003', 'dept_name' => 'Biomedical', 'dept_label' => 'Biomedical'],
            ['dept_id' => 'VIMS-DEPT-004', 'dept_name' => 'Blood Bank', 'dept_label' => 'Blood Bank'],
            ['dept_id' => 'VIMS-DEPT-005', 'dept_name' => 'Branding', 'dept_label' => 'Branding'],
            ['dept_id' => 'VIMS-DEPT-006', 'dept_name' => 'Clinical Pharmacology', 'dept_label' => 'Clinical Pharmacology'],
            ['dept_id' => 'VIMS-DEPT-007', 'dept_name' => 'Dialysis', 'dept_label' => 'Dialysis'],
            ['dept_id' => 'VIMS-DEPT-008', 'dept_name' => 'Dietary', 'dept_label' => 'Dietary'],
            ['dept_id' => 'VIMS-DEPT-009', 'dept_name' => 'Emergency', 'dept_label' => 'Emergency'],
            ['dept_id' => 'VIMS-DEPT-010', 'dept_name' => 'Facility', 'dept_label' => 'Facility'],
            ['dept_id' => 'VIMS-DEPT-011', 'dept_name' => 'Front Office', 'dept_label' => 'Front Office'],
            ['dept_id' => 'VIMS-DEPT-012', 'dept_name' => 'Guest Relation', 'dept_label' => 'Guest Relation'],
            ['dept_id' => 'VIMS-DEPT-013', 'dept_name' => 'HIMS', 'dept_label' => 'HIMS'],
            ['dept_id' => 'VIMS-DEPT-014', 'dept_name' => 'HRD', 'dept_label' => 'HRD'],
            ['dept_id' => 'VIMS-DEPT-015', 'dept_name' => 'Hospital Infection Control', 'dept_label' => 'Hospital Infection Control'],
            ['dept_id' => 'VIMS-DEPT-016', 'dept_name' => 'House Keeping', 'dept_label' => 'House Keeping'],
            ['dept_id' => 'VIMS-DEPT-017', 'dept_name' => 'IP Manager', 'dept_label' => 'IP Manager'],
            ['dept_id' => 'VIMS-DEPT-018', 'dept_name' => 'IT', 'dept_label' => 'IT'],
            ['dept_id' => 'VIMS-DEPT-019', 'dept_name' => 'Lab', 'dept_label' => 'Lab'],
            ['dept_id' => 'VIMS-DEPT-020', 'dept_name' => 'Marketing', 'dept_label' => 'Marketing'],
            ['dept_id' => 'VIMS-DEPT-021', 'dept_name' => 'Medical Record', 'dept_label' => 'Medical Record'],
            ['dept_id' => 'VIMS-DEPT-022', 'dept_name' => 'Nursing', 'dept_label' => 'Nursing'],
            ['dept_id' => 'VIMS-DEPT-023', 'dept_name' => 'OT', 'dept_label' => 'OT'],
            ['dept_id' => 'VIMS-DEPT-024', 'dept_name' => 'Operations', 'dept_label' => 'Operations'],
            ['dept_id' => 'VIMS-DEPT-025', 'dept_name' => 'Pharmacy', 'dept_label' => 'Pharmacy'],
            ['dept_id' => 'VIMS-DEPT-026', 'dept_name' => 'Physiotherapy', 'dept_label' => 'Physiotherapy'],
            ['dept_id' => 'VIMS-DEPT-027', 'dept_name' => 'Quality', 'dept_label' => 'Quality'],
            ['dept_id' => 'VIMS-DEPT-028', 'dept_name' => 'Radiology', 'dept_label' => 'Radiology'],
            ['dept_id' => 'VIMS-DEPT-029', 'dept_name' => 'Security', 'dept_label' => 'Security'],
            ['dept_id' => 'VIMS-DEPT-030', 'dept_name' => 'Stores', 'dept_label' => 'Stores'],
            ['dept_id' => 'VIMS-DEPT-031', 'dept_name' => 'Transplant', 'dept_label' => 'Transplant'],
            ['dept_id' => 'VIMS-DEPT-032', 'dept_name' => 'Transport', 'dept_label' => 'Transport'],
            ['dept_id' => 'VIMS-DEPT-033', 'dept_name' => 'Ward - Emergency', 'dept_label' => 'Ward - Emergency'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['dept_label' => $dept['dept_label']],
                array_merge($dept, ['is_active' => 1, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
