<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id',
        'mode',
        'payment_reference_no',
        'payment_date',
        'paid_amount',
        'expenditure_id',
        'expenditure_category',  // Added
        'payment_type',
        'remarks',
        'tds_amount',
        'cheque_issue_date',      // Added
        'cheque_cleared_date',    // Added
        'bill_amount',            // Added
        'bill_submission_date',   // Added
        'refund_amount',          // Added
        'refund_date',            // Added
    ];

    public function document()
    {
        return $this->belongsTo(DocumentApproval::class, 'doc_id', 'id');
    }
}