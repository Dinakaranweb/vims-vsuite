<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentDetailsExport implements FromView
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function view(): View
    {
        return view('frontend.document.download.report_payment_details_excel', [
            'payments' => $this->payments
        ]);
    }
}

