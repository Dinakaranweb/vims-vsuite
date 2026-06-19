<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DdeDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'c_code',
        'reg_no',
        'student_name',
        'is_fee_paid',
        'fee_item',
        'mode',
        'payment_reference_no',
        'payment_date',
        'micr_code',
        'bank_name',
        'branch',
        'amount',
        'remarks',
        'receipt_no',
        'received_date',
    ];
}
