<!-- Replace the existing Approval Status card with this code -->
<div class="card">
    <div class="card-header">
        <h4>Approval Status</h4>
    </div>
    <div class="card-body">
        <div class="approval-timeline">

            @foreach($approval_logs as $log)
                @php
                    // Get user details dynamically
                    $approverUser = \App\Models\User::find($log->by);
                    
                    // Determine role based on user's actual role and department
                    if($approverUser) {
                        if($approverUser->role == 'SuperAdmin') {
                            // For SuperAdmin, use their department as the role identifier
                            $role = $approverUser->department;
                        } else {
                            $role = $approverUser->role . ' - ' . $approverUser->department;
                        }
                    } else {
                        $role = 'Unknown User (Deleted)';
                    }
                    
                    // Determine style class based on department/role
                    if(stripos($role, 'Pro-VC') !== false || stripos($role, 'Pro VC') !== false) {
                        $style = 'provc';
                    } elseif(stripos($role, 'VC') !== false || stripos($role, 'Vice Chancellor') !== false) {
                        $style = 'vc';
                    } elseif(stripos($role, 'Registrar') !== false) {
                        $style = 'registrar';
                    } elseif(stripos($role, 'Medical Director') !== false) {
                        $style = 'medical-director';
                    } elseif(stripos($role, 'General Manager') !== false) {
                        $style = 'general-manager';
                    } elseif(stripos($role, 'Purchase Head') !== false) {
                        $style = 'purchase-head';
                    } elseif(stripos($role, 'STB Office') !== false) {
                        $style = 'stb-office';
                    } elseif(stripos($role, 'Chairman') !== false) {
                        $style = 'chairman';
                    } elseif(stripos($role, 'Finance Head') !== false) {
                        $style = 'finance-head';
                    } else {
                        $style = '';
                    }
                    
                    // Determine status type for styling
                    if(stripos($log->status, 'approve') !== false) {
                        $statusType = 'approved';
                        $statusIcon = '✓';
                    } elseif(stripos($log->status, 'reject') !== false) {
                        $statusType = 'rejected';
                        $statusIcon = '✕';
                    } elseif(stripos($log->status, 'hold') !== false) {
                        $statusType = 'hold';
                        $statusIcon = '⏸';
                    } elseif(stripos($log->status, 'pending') !== false) {
                        $statusType = 'pending';
                        $statusIcon = '⏳';
                    } elseif(stripos($log->status, 'discuss') !== false) {
                        $statusType = 'discussion';
                        $statusIcon = '💬';
                    } elseif(stripos($log->status, 'forward') !== false) {
                        $statusType = 'forwarded';
                        $statusIcon = '📨';
                    } elseif(stripos($log->status, 'complete') !== false) {
                        $statusType = 'completed';
                        $statusIcon = '✅';
                    } elseif(stripos($log->status, 'close') !== false) {
                        $statusType = 'closed';
                        $statusIcon = '🔒';
                    } else {
                        $statusType = 'default';
                        $statusIcon = '📝';
                    }
                    
                    // Get annexures for this log entry
                    $logAnnexures = [];
                    foreach($annexures as $attachment) {
                        if($attachment->created_at == $log->created_at && $attachment->doc_id == $log->doc_id) {
                            $logAnnexures[] = $attachment;
                        }
                    }
                    
                    // Format display role name
                    $displayRole = $role;
                    if($approverUser && $approverUser->name) {
                        $displayRole = $approverUser->name . ' (' . $role . ')';
                    }
                @endphp
                <div class="timeline-item {{ $style }} {{ $statusType }}" 
                     data-message="{!! htmlspecialchars($log->message) !!}"
                     data-status="{{ $log->status }}"
                     data-role="{{ $displayRole }}"
                     data-date="{{ date('d/m/Y h:i A', strtotime($log->created_at)) }}">
                    <div class="timeline-marker">
                        <div class="timeline-icon">
                            {{ $statusIcon }}
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h5 class="timeline-title">{{ $log->status }}</h5>
                            <span class="timeline-date">{{ date('d/m/Y h:i A', strtotime($log->created_at)) }}</span>
                        </div>
                        <div class="timeline-body">
                            <div class="timeline-message">{!! $log->message !!}</div>
                            
                            <!-- Annexures in card -->
                            @if(count($logAnnexures) > 0)
                                <div class="annexures-preview">
                                    <div class="annexure-badge">
                                        <span class="annexure-icon">📎</span>
                                        <span class="annexure-count">{{ count($logAnnexures) }} file(s)</span>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="timeline-footer">
                                <span class="timeline-role">{{ $displayRole }}</span>
                                <span class="view-full">View full message</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden annexures data for popup -->
                    @if(count($logAnnexures) > 0)
                        <div class="annexures-data" style="display: none;">
                            @foreach($logAnnexures as $attachment)
                                @php
                                    $link = Storage::url($attachment->annexure);
                                    $fileName = basename($attachment->annexure);
                                @endphp
                                <div class="annexure-item-data" 
                                     data-link="{{ $link }}"
                                     data-filename="{{ $fileName }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Document Creator / Owner entry — always at the bottom (origin of the flow) --}}
            @php
                $docCreator = \App\Models\User::find($doc->by);
                $creatorDisplayName = $docCreator ? $docCreator->name : 'Unknown User';
                $creatorDesignation = $docCreator && $docCreator->designation ? $docCreator->designation : null;
                $creatorDeptDisplay = $docCreator ? $docCreator->department : 'Unknown Department';
                $creatorRoleLabel = $creatorDesignation
                    ? $creatorDisplayName . ' — ' . $creatorDesignation . ', ' . $creatorDeptDisplay
                    : $creatorDisplayName . ' — ' . $creatorDeptDisplay;
            @endphp
            <div class="timeline-item creator-entry">
                <div class="timeline-marker">
                    <div class="timeline-icon" style="border-color: #056b0d; background: #f0faf1;">
                        👤
                    </div>
                </div>
                <div class="timeline-content" style="border-left: 3px solid #056b0d;">
                    <div class="timeline-header">
                        <h5 class="timeline-title" style="color: #056b0d;">Document Created</h5>
                        <span class="timeline-date">{{ date('d/m/Y h:i A', strtotime($doc->created_at)) }}</span>
                    </div>
                    <div class="timeline-body">
                        <div class="timeline-message">Document submitted for approval.</div>
                        <div class="timeline-footer mt-1">
                            <span class="timeline-role" style="color: #056b0d; font-weight: 600;">
                                <i class="fas fa-user-circle"></i>
                                {{ $creatorRoleLabel }}
                                <span class="badge badge-success ml-1">Creator</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .approval-timeline {
        position: relative;
        padding: 20px 0;
    }

    .approval-timeline::before {
        content: '';
        position: absolute;
        left: 36px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, #e0e0e0, #e0e0e0);
        border-radius: 2px;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .timeline-item.creator-entry .timeline-content {
        background: #f0faf1;
        border-radius: 8px;
        padding: 10px 14px;
    }

    .timeline-item:hover {
        transform: translateX(5px);
    }

    .timeline-marker {
        position: relative;
        z-index: 2;
        margin-right: 24px;
    }

    .timeline-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #fff;
        border: 4px solid;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }

    .timeline-item:hover .timeline-icon {
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .timeline-content {
        flex: 1;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 5px solid;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }

    .timeline-item:hover .timeline-content {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        border-left-width: 6px;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f8f9fa;
    }

    .timeline-title {
        margin: 0;
        color: #2c3e50;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.3;
    }

    .timeline-date {
        color: #6c757d;
        font-size: 13px;
        white-space: nowrap;
        font-weight: 500;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .timeline-body {
        margin-top: 8px;
    }

    .timeline-message {
        margin: 0 0 12px 0;
        color: #495057;
        line-height: 1.5;
        word-break: break-word;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-size: 14px;
        max-height: 63px;
    }

    .timeline-message b,
    .timeline-message strong {
        font-weight: 700 !important;
        color: inherit;
    }

    .timeline-message i,
    .timeline-message em {
        font-style: italic !important;
    }

    .timeline-message u {
        text-decoration: underline !important;
    }

    .annexures-preview {
        margin: 8px 0 12px 0;
    }

    .annexure-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #e7f3ff;
        color: #0066cc;
        padding: 4px 10px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #b3d9ff;
    }

    .annexure-icon {
        font-size: 11px;
    }

    .annexure-count {
        font-weight: 600;
    }

    .timeline-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f8f9fa;
    }

    .timeline-role {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        background: #f8f9fa;
        color: #495057;
    }

    .view-full {
        font-size: 12px;
        color: #007bff;
        font-weight: 500;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .timeline-item:hover .view-full {
        opacity: 1;
    }

    /* Status-specific styling */
    .timeline-item.approved .timeline-icon {
        border-color: #28a745;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .timeline-item.approved .timeline-content {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
    }

    .timeline-item.approved .timeline-title {
        color: #155724;
    }

    .timeline-item.rejected .timeline-icon {
        border-color: #dc3545;
        background: linear-gradient(135deg, #dc3545, #e83e8c);
        color: white;
    }

    .timeline-item.rejected .timeline-content {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #fff8f8 0%, #ffffff 100%);
    }

    .timeline-item.rejected .timeline-title {
        color: #721c24;
    }

    .timeline-item.hold .timeline-icon {
        border-color: #ffc107;
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: #000;
    }

    .timeline-item.hold .timeline-content {
        border-left-color: #ffc107;
        background: linear-gradient(135deg, #fffdf0 0%, #ffffff 100%);
    }

    .timeline-item.hold .timeline-title {
        color: #856404;
    }

    .timeline-item.pending .timeline-icon {
        border-color: #17a2b8;
        background: linear-gradient(135deg, #17a2b8, #6f42c1);
        color: white;
    }

    .timeline-item.pending .timeline-content {
        border-left-color: #17a2b8;
        background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);
    }

    .timeline-item.pending .timeline-title {
        color: #004085;
    }

    .timeline-item.discussion .timeline-icon {
        border-color: #6f42c1;
        background: linear-gradient(135deg, #6f42c1, #e83e8c);
        color: white;
    }

    .timeline-item.discussion .timeline-content {
        border-left-color: #6f42c1;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    }

    .timeline-item.discussion .timeline-title {
        color: #4a1e8c;
    }

    .timeline-item.forwarded .timeline-icon {
        border-color: #fd7e14;
        background: linear-gradient(135deg, #fd7e14, #e74a3b);
        color: white;
    }

    .timeline-item.forwarded .timeline-content {
        border-left-color: #fd7e14;
        background: linear-gradient(135deg, #fff4ec 0%, #ffffff 100%);
    }

    .timeline-item.forwarded .timeline-title {
        color: #8c2e00;
    }

    .timeline-item.completed .timeline-icon {
        border-color: #20c997;
        background: linear-gradient(135deg, #20c997, #28a745);
        color: white;
    }

    .timeline-item.completed .timeline-content {
        border-left-color: #20c997;
    }

    .timeline-item.closed .timeline-icon {
        border-color: #6c757d;
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
    }

    .timeline-item.closed .timeline-content {
        border-left-color: #6c757d;
    }

    .timeline-item.default .timeline-icon {
        border-color: #6c757d;
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
    }

    .timeline-item.default .timeline-content {
        border-left-color: #6c757d;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    /* Department-specific styling */
    .timeline-item.vc .timeline-role {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .timeline-item.provc .timeline-role {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .timeline-item.registrar .timeline-role {
        background: linear-gradient(135deg, #d1ecf1, #bee5eb);
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .timeline-item.medical-director .timeline-role {
        background: linear-gradient(135deg, #fff3cd, #ffeeba);
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .timeline-item.general-manager .timeline-role {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .timeline-item.purchase-head .timeline-role {
        background: linear-gradient(135deg, #e2e3e5, #d6d8db);
        color: #383d41;
        border: 1px solid #d6d8db;
    }

    .timeline-item.stb-office .timeline-role {
        background: linear-gradient(135deg, #d1ecf1, #bee5eb);
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .timeline-item.chairman .timeline-role {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .timeline-item.finance-head .timeline-role {
        background: linear-gradient(135deg, #cce5ff, #b8daff);
        color: #004085;
        border: 1px solid #b8daff;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .approval-timeline::before {
            left: 28px;
        }
        
        .timeline-icon {
            width: 56px;
            height: 56px;
            font-size: 22px;
            border-width: 3px;
        }
        
        .timeline-marker {
            margin-right: 20px;
        }
        
        .timeline-header {
            flex-direction: column;
            gap: 8px;
        }
        
        .timeline-date {
            align-self: flex-start;
        }
        
        .timeline-content {
            padding: 16px;
        }
        
        .timeline-title {
            font-size: 16px;
        }
        
        .timeline-footer {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }
        
        .view-full {
            opacity: 1;
        }
        
        .timeline-message {
            max-height: 63px;
        }
    }

    /* Message Modal */
    .message-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .message-modal.active {
        display: flex;
    }

    .modal-content {
        background: #fff;
        border-radius: 16px;
        padding: 0;
        max-width: 750px;
        width: 100%;
        max-height: 100vh;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 24px;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        border-radius: 16px 16px 0 0;
    }

    .modal-title {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .modal-title .status {
        font-weight: 700;
        font-size: 20px;
        color: #2c3e50;
    }

    .modal-title .meta {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 28px;
        cursor: pointer;
        color: #6c757d;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .close-modal:hover {
        background: #e9ecef;
        color: #495057;
    }

    .modal-body {
        padding: 24px;
        color: #495057;
        line-height: 1.6;
        max-height: 400px;
        overflow-y: auto;
        font-size: 15px;
    }

    .modal-message {
        white-space: pre-wrap;
        word-wrap: break-word;
        margin-bottom: 20px;
    }

    .modal-annexures {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .annexures-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 12px;
        font-size: 16px;
    }

    .annexures-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .annexure-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: background-color 0.2s ease;
    }

    .annexure-item:hover {
        background: #e9ecef;
    }

    .annexure-icon-modal {
        font-size: 16px;
        color: #6c757d;
    }

    .annexure-link {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        flex: 1;
    }

    .annexure-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    .modal-message b,
    .modal-message strong {
        font-weight: 700 !important;
        color: inherit;
    }

    .modal-message i,
    .modal-message em {
        font-style: italic !important;
    }

    .modal-message u {
        text-decoration: underline !important;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timelineItems = document.querySelectorAll('.timeline-item');
        
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'message-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">
                        <div class="status" id="modal-status">Status</div>
                        <div class="meta" id="modal-meta">Role • Date</div>
                    </div>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="modal-message" id="modal-message">Message content will appear here</div>
                    <div class="modal-annexures" id="modal-annexures" style="display: none;">
                        <div class="annexures-title">Attached Files</div>
                        <div class="annexures-list" id="annexures-list"></div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Timeline item click handler
        timelineItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const message = this.getAttribute('data-message');
                const status = this.getAttribute('data-status');
                const role = this.getAttribute('data-role');
                const date = this.getAttribute('data-date');
                
                document.getElementById('modal-status').textContent = status;
                document.getElementById('modal-meta').textContent = `${role} • ${date}`;
                document.getElementById('modal-message').innerHTML = message;
                
                // Handle annexures
                const annexuresData = this.querySelector('.annexures-data');
                const annexuresList = document.getElementById('annexures-list');
                const modalAnnexures = document.getElementById('modal-annexures');
                
                if (annexuresData) {
                    const annexureItems = annexuresData.querySelectorAll('.annexure-item-data');
                    annexuresList.innerHTML = '';
                    
                    annexureItems.forEach(item => {
                        const link = item.getAttribute('data-link');
                        const filename = item.getAttribute('data-filename');
                        
                        const annexureItem = document.createElement('div');
                        annexureItem.className = 'annexure-item';
                        annexureItem.innerHTML = `
                            <span class="annexure-icon-modal">📎</span>
                            <a href="${link}" target="_blank" class="annexure-link">${filename}</a>
                        `;
                        annexuresList.appendChild(annexureItem);
                    });
                    
                    modalAnnexures.style.display = 'block';
                } else {
                    modalAnnexures.style.display = 'none';
                }
                
                modal.classList.add('active');
            });
        });

        // Close modal
        modal.querySelector('.close-modal').addEventListener('click', function() {
            modal.classList.remove('active');
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                modal.classList.remove('active');
            }
        });
    });
</script>