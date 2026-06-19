<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApprovalReportExport implements FromView
{
    public function __construct(
        protected $docs,
        protected string $role,
        protected array $selectedFields,
        protected array $allFields
    ) {}

    public function view(): View
    {
        return view('frontend.document.download.approval_report_excel', [
            'docs'           => $this->docs,
            'role'           => $this->role,
            'selectedFields' => $this->selectedFields,
            'allFields'      => $this->allFields,
        ]);
    }
}
