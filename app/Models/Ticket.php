<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ticket_id',
        'doc_id',
        'title',
        'ticket_to',
        'ticket_from',
        'ticket_by',
        'description',
        'file',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'assigned_by',
        'closed_by',
        'is_approved',
        'forwarded_to'
    ];
}
