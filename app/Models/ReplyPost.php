<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplyPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'post_id',
        'post_pid',
        'subject',
        'reply_to_address',
        'reply_from_address',
        'reply_by',
        'reply_to',
        'scanned_copy',
        'status',
        'reply_type',
        'vendor',
        'reply_from',
        'delivered_by',
        'tracking_id'
    ];
}
