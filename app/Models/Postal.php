<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postal extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'post_id',
        'registrar_id',
        'subject',
        'post_to_address',
        'post_from_address',
        'sent_by',
        'sent_to',
        'scanned_copy',
        'status',
        'dispatched_to',
        'dispatched_by',
        'delivered_by',
        'collected_by',
        'closed_by',
        'remarks',
        'is_responded',
        'is_read',
        'received_date',
        'type',
        'staff_name',
        'tracking_id',
        'dde_payment_mode',
        'dde_paid_amount',
        'dde_dd_number',
        'is_forwarded',
        'forward_to',
        'forwarded_by',
        'original_at',
        'category'
    ];
}
