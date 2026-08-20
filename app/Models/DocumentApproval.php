<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DocumentApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'doc_id',
        'ticket_id',
        'from',
        'title',
        'by',
        'to',
        'subject',
        'description',
        'status',
        'attachment',
        'approval_status',
        'tags',
        'reference',
        'forwarded_to',
        'requested_by',
        'amount',
        'currency',
        'purchase_committee_report',
        'purchase_order',
        'justification',
        'is_purchase',
        'is_payment_involved',  // New field: Y/N for payment involvement
        'priority',
        'payment_mode',
        'cash_in_favour',
        'account_holder',
        'account_number',
        'ifsc_code',
        'account_branch',
        'upi_id',
        'recommended_amount',
        'sanctioned_amount',
        'paid_amount',
        'work_order',
        'approval_sequence',      // JSON field for approval path
        'current_sequence_index', // Current position in sequence
        'consult_department',     // Department currently in a Consult Department (Acknowledge-only) slot
        'completed_at',           // When status first became Completed
        'closed_at',              // When the creator formally closed the document
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Auto-stamp completed_at/closed_at the moment status actually transitions into
     * Completed/Closed, regardless of which handler in DocumentApprovalController triggers it
     * (there are several: handleApproval, handleChairmanApproval, handleSTBAcknowledge, the
     * handleAcknowledgeConsultation edge case, ...). Doing this here instead of in each handler
     * means "days to close" stays correct even if a future code path sets status='Completed'
     * without anyone remembering to stamp the timestamp by hand.
     */
    protected static function booted(): void
    {
        static::saving(function (self $doc) {
            if ($doc->isDirty('status')) {
                if ($doc->status === 'Completed' && !$doc->completed_at) {
                    $doc->completed_at = now();
                }
                if ($doc->status === 'Closed' && !$doc->closed_at) {
                    $doc->closed_at = now();
                }
            }
        });
    }

    /**
     * Whole days between the flow completing and the document being closed - or, if not
     * closed yet, days completed and still awaiting closure. Null until the flow completes.
     */
    public function getDaysToCloseAttribute(): ?int
    {
        if (!$this->completed_at) {
            return null;
        }

        return (int) $this->completed_at->diffInDays($this->closed_at ?? now());
    }

    /**
     * Next sequential doc_id in the shared VIMS-DOC-NNNN series — used by both
     * the web and mobile create flows so numbering never forks into separate
     * series depending on which client created the document.
     */
    public static function nextDocId(): string
    {
        $maxDocNumber = static::where('doc_id', '!=', 'Draft')
            ->selectRaw("MAX(CAST(SUBSTRING(doc_id, 10) AS UNSIGNED)) as max_number")
            ->value('max_number');
        $lastNumber = $maxDocNumber ? intval($maxDocNumber) : 0;

        return 'VIMS-DOC-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    // ==================== Relationships ====================

    /**
     * Get the payment details for this document
     */
    public function payments()
    {
        return $this->hasMany(PaymentDetails::class, 'doc_id');
    }

    /**
     * Relationship to DocumentApprovalForwardings
     */
    public function forwardings()
    {
        return $this->hasMany(DocumentApprovalForwardings::class, 'doc_id', 'id');
    }
    
    /**
     * Relationship to PaymentProcessing
     */
    public function paymentProcessings()
    {
        return $this->hasMany(PaymentProcessing::class, 'doc_id', 'id');
    }
    
    /**
     * Relationship to PaymentDetails
     */
    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetails::class, 'doc_id', 'id');
    }
    
    /**
     * Get the creator of the document
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'by');
    }
    
    /**
     * Get the current approver details
     */
    public function currentApproverUser()
    {
        return User::where('department', $this->current_approver)
            ->whereIn('role', ['HOD', 'SuperAdmin'])
            ->first();
    }
    
    // ==================== Accessors & Mutators ====================
    
    /**
     * Get the approval sequence as array
     */
    public function getApprovalSequenceArrayAttribute()
    {
        if (empty($this->approval_sequence)) {
            return [];
        }
        
        try {
            $sequence = json_decode($this->approval_sequence, true);
            return is_array($sequence) ? $sequence : [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get the current approver from sequence
     */
    public function getCurrentApproverAttribute()
    {
        $sequence = $this->approval_sequence_array;
        $index = $this->current_sequence_index ?? 0;
        
        return $sequence[$index] ?? null;
    }
    
    /**
     * Get the next approver from sequence
     */
    public function getNextApproverAttribute()
    {
        $sequence = $this->approval_sequence_array;
        $nextIndex = ($this->current_sequence_index ?? 0) + 1;
        
        return $sequence[$nextIndex] ?? null;
    }
    
    /**
     * Get the previous approver from sequence
     */
    public function getPreviousApproverAttribute()
    {
        $sequence = $this->approval_sequence_array;
        $prevIndex = ($this->current_sequence_index ?? 0) - 1;
        
        return $prevIndex >= 0 ? ($sequence[$prevIndex] ?? null) : null;
    }
    
    /**
     * Get the approval progress percentage
     */
    public function getApprovalProgressAttribute()
    {
        $sequence = $this->approval_sequence_array;
        $total = count($sequence);
        
        if ($total === 0) {
            return 0;
        }
        
        $completed = $this->current_sequence_index ?? 0;
        return round(($completed / $total) * 100);
    }
    
    /**
     * Get the approval status with next approver info
     */
    public function getCurrentStatusWithNextAttribute()
    {
        if ($this->isFullyApproved()) {
            return 'Completed';
        }
        
        $nextApprover = $this->next_approver;
        return $nextApprover ? "Pending at {$nextApprover}" : $this->status;
    }
    
    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('paid_amount');
    }
    
    /**
     * Get total TDS amount
     */
    public function getTotalTdsAttribute()
    {
        return $this->payments()->sum('tds_amount');
    }
    
    /**
     * Get grand total (paid + TDS)
     */
    public function getGrandTotalAttribute()
    {
        return $this->total_paid + $this->total_tds;
    }
    
    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        $currency = $this->currency ?? 'INR';
        $symbol = $currency === 'INR' ? '₹' : ($currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : '£'));
        return $symbol . ' ' . number_format($this->amount, 2);
    }
    
    // ==================== Boolean Checks ====================
    
    /**
     * Check if document is fully approved
     */
    public function isFullyApproved()
    {
        $sequence = $this->approval_sequence_array;
        return ($this->current_sequence_index ?? 0) >= count($sequence);
    }
    
    /**
     * Check if payment is involved
     */
    public function hasPayment()
    {
        return $this->is_payment_involved === 'Y';
    }
    
    /**
     * Check if amount exceeds threshold for special routing
     */
    public function requiresSpecialRouting()
    {
        return $this->hasPayment() && ($this->amount > 200000);
    }
    
    /**
     * Check if document is a purchase request
     */
    public function isPurchaseRequest()
    {
        return $this->is_purchase === 'Y';
    }
    
    /**
     * Check if document is a draft
     */
    public function isDraft()
    {
        return $this->status === 'Draft';
    }
    
    /**
     * Check if document is completed
     */
    public function isCompleted()
    {
        return in_array($this->status, ['Completed', 'Closed']);
    }
    
    /**
     * Check if document is rejected
     */
    public function isRejected()
    {
        return $this->status === 'Rejected';
    }
    
    /**
     * Check if document is on hold
     */
    public function isOnHold()
    {
        return $this->status === 'Hold';
    }
    
    /**
     * Check if current user can approve this document
     */
    public function canUserApprove($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        
        // SuperAdmin can approve if their department matches current approver
        if ($user->role === 'SuperAdmin') {
            return $user->department === $this->current_approver;
        }
        
        // HOD can approve if they are the current approver
        if ($user->role === 'HOD') {
            return $user->department === $this->current_approver;
        }
        
        return false;
    }
    
    /**
     * Check if current user can close this document
     */
    public function canUserClose($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        
        // Creator can close
        if ($user->id === $this->by) {
            return true;
        }
        
        // SuperAdmin can close
        if ($user->role === 'SuperAdmin') {
            return true;
        }
        
        return false;
    }
    
    // ==================== Helper Methods ====================
    
    /**
     * Move to next approver in sequence
     */
    public function moveToNextApprover()
    {
        $nextIndex = ($this->current_sequence_index ?? 0) + 1;
        $sequence = $this->approval_sequence_array;
        
        if ($nextIndex < count($sequence)) {
            $this->update([
                'current_sequence_index' => $nextIndex,
                'forwarded_to' => $sequence[$nextIndex],
                'to' => $sequence[$nextIndex],
                'updated_at' => now(),
            ]);
            return true;
        }
        
        // If no next approver, mark as completed
        $this->update([
            'status' => 'Completed',
            'updated_at' => now(),
        ]);
        
        return false;
    }
    
    /**
     * Get approval logs with user details
     */
    public function getApprovalLogsWithUsers()
    {
        return DB::table('approval_log')
            ->where('doc_id', $this->id)
            ->leftJoin('users', 'approval_log.by', '=', 'users.id')
            ->select('approval_log.*', 'users.name', 'users.department', 'users.designation')
            ->orderBy('approval_log.created_at', 'desc')
            ->get();
    }
    
    /**
     * Get document logs with user details
     */
    public function getDocumentLogsWithUsers()
    {
        return DB::table('document_logs')
            ->where('doc_id', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Get all annexures
     */
    public function getAnnexures()
    {
        return DB::table('document_annexures')
            ->where('doc_id', $this->id)
            ->get();
    }
    
    /**
     * Get the current step number in approval sequence
     */
    public function getCurrentStepNumber()
    {
        return ($this->current_sequence_index ?? 0) + 1;
    }
    
    /**
     * Get total steps in approval sequence
     */
    public function getTotalSteps()
    {
        return count($this->approval_sequence_array);
    }
    
    // ==================== Scopes ====================
    
    /**
     * Scope for documents pending at a specific department
     */
    public function scopePendingAt($query, $department)
    {
        return $query->where('forwarded_to', $department)
            ->whereNotIn('status', ['Closed', 'Completed', 'Rejected', 'Draft']);
    }
    
    /**
     * Scope for documents created by a user
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('by', $userId);
    }
    
    /**
     * Scope for documents from a department
     */
    public function scopeFromDepartment($query, $department)
    {
        return $query->where('from', $department);
    }
    
    /**
     * Scope for documents sent to a department
     */
    public function scopeSentTo($query, $department)
    {
        return $query->where('to', $department);
    }
    
    /**
     * Scope for documents with payment involved
     */
    public function scopeWithPayment($query)
    {
        return $query->where('is_payment_involved', 'Y');
    }
    
    /**
     * Scope for high value documents (>2 lakhs)
     */
    public function scopeHighValue($query)
    {
        return $query->where('amount', '>', 200000);
    }
    
    /**
     * Scope for documents pending approval (not completed/rejected/draft)
     */
    public function scopePending($query)
    {
        return $query->whereNotIn('status', ['Closed', 'Completed', 'Rejected', 'Draft']);
    }
    
    /**
     * Scope for active documents (not closed or draft)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['Closed', 'Draft']);
    }
    
    /**
     * Scope for documents that need action from a specific department
     */
    public function scopeNeedsActionFrom($query, $department)
    {
        return $query->where('forwarded_to', $department)
            ->where('status', 'like', '%Sent to%')
            ->whereNotIn('status', ['Closed', 'Completed', 'Rejected']);
    }
    
    /**
     * Scope for documents by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
    
    /**
     * Scope for documents created between dates
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}