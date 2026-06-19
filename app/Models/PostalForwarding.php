<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalForwarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'forwarded_to',
        'forwarded_by',
        'is_read',
        'status',
        'dispatched_by',
        'collected_by'
    ];
}
