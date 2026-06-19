<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentApprovalForwardings extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'forwarded_to',
        'forwarded_by',
    ];
    
    public function document() {
        return $this->belongsTo(DocumentApproval::class, 'doc_id');
    }

    public function documentApproval()
    {
        // This table's doc_id references document_approvals.id
        return $this->belongsTo(DocumentApproval::class, 'doc_id', 'id');
    }
}
