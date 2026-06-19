<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ForwardedDocumentsExport implements FromView
{
    protected $docs;
    protected $approvalLogs;

    public function __construct($docs, $approvalLogs)
    {
        $this->docs = $docs;
        $this->approvalLogs = $approvalLogs;
    }

    public function view(): View
    {
        return view('frontend.document.download.excel_report_forwarded_doc_template', [
            'docs' => $this->docs,
            'approvalLogs' => $this->approvalLogs,
        ]);
    }
}
