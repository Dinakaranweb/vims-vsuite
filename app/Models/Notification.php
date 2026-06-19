<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'to',
        'task_type',
        'is_read',
        'task_id',
        'message',
    ];
}
