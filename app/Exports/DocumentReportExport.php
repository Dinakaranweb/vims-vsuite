<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DocumentReportExport implements FromView
{
    protected $docs;

    public function __construct($docs)
    {
        $this->docs = $docs;
    }

    public function view(): View
    {
        return view('frontend.document.download.excel_report_template', [
            'docs' => $this->docs,
        ]);
    }
}
