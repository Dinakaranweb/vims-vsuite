<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'emp_id',
        'designation',
        'department',
        'division', // Added division (Clinical/Non Clinical)
        'role',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    /**
     * Check if user can approve documents for a specific department
     */
    public function canApproveForDepartment($department)
    {
        return $this->role === 'SuperAdmin' && $this->department === $department;
    }
    
    /**
     * Check if user is HOD
     */
    public function isHOD()
    {
        return $this->role === 'HOD';
    }
    
    /**
     * Check if user is SuperAdmin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'SuperAdmin';
    }
    
    /**
     * Get all documents created by this user
     */
    public function documents()
    {
        return $this->hasMany(DocumentApproval::class, 'by');
    }
    
    /**
     * Get all documents forwarded to this user's department
     */
    public function forwardedDocuments()
    {
        return $this->hasManyThrough(
            DocumentApproval::class,
            DocumentApprovalForwardings::class,
            'forwarded_to',
            'id',
            'department',
            'doc_id'
        )->where('document_approval_forwardings.forwarded_to', $this->department);
    }
}