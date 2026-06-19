<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dept_id',
        'dept_name',
        'dept_label',
        'is_active',
    ];

    // In Department.php model
    public function head()
    {
        return $this->hasOne(User::class, 'department', 'dept_label')
                    ->where(function($query) {
                        $query->where('role', 'HOD')
                              ->orWhere('role', 'SuperAdmin');
                    });
    }
    
}