@extends('frontend.frontend_master')

<style>
  .wizard-pane {
    display: none;
  }

  .wizard-pane.active {
    display: block;
  }
  .wizard-step {
    cursor: pointer;
  }
  .wizard-step.disabled {
    pointer-events: none;
    opacity: 0.5;
  }

  .custom-file-drop-area {
    border: 1px solid #2a6d98;
    margin: auto;
    border-radius: 4px;
    padding: 40px;
    text-align: center;
    color: #000000;
    font-family: Arial, sans-serif;
    background-color: #fff;
    position: relative;
    transition: background-color 0.3s, border-color 0.3s;
  }

  .custom-file-drop-area p {
    margin: 0;
    font-size: 1.1em;
    color: #000000;
  }

  .upload-btn {
    margin-top: 10px;
    padding: 10px 20px;
    font-size: 1em;
    color: #ffffff;
    background-color: #2a6d98;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s, color 0.2s;
  }

  .upload-btn:hover {
    background-color: #1f506f;
    border: 1px solid #1f506f;
  }

  .custom-file-drop-area.dragover {
    background-color: #e2e6ea;
    border-color: #adb5bd;
  }

  .custom-file-input {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
  }

  .file-list {
    margin-top: 15px;
    margin-left: 25px;
    font-family: Arial, sans-serif;
    color: #495057;
  }

  .file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background-color: #fff;
    border: 1px solid #2a6d98;
    border-radius: 4px;
    margin-top: 5px;
  }

  .file-item span {
    font-size: 0.95em;
  }

  .remove-btn {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 1.2em;
    cursor: pointer;
  }

  .remove-btn:hover {
    color: #bd2130;
  }

  .review-heading {
    font-size: 1.5em;
    text-align: center;
    margin-bottom: 20px;
    color: #333;
  }

  .review-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
  }

  .review-label {
    font-weight: bold;
    color: #555;
    width: 150px;
    text-align: right;
    margin-right: 20px;
    font-size: 1em;
  }

  .review-value {
    flex: 1;
    font-size: 1em;
    color: #333;
    padding: 5px 10px;
    background-color: #f9f9f9;
    border-radius: 5px;
  }

  .review-files {
    list-style-type: none;
    padding: 0;
  }

  .review-files li {
    font-size: 0.95em;
    color: #333;
    background-color: #f0f8ff;
    padding: 5px;
    margin-bottom: 5px;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .review-files li::before {
    content: '📄 ';
    margin-right: 8px;
  }

  .review-submit-btn {
    display: block;
    width: 100%;
    max-width: 300px;
    margin: 20px auto;
    padding: 12px;
    font-size: 1.1em;
    background-color: #007bff;
    border: none;
    color: #fff;
    border-radius: 5px;
    transition: background-color 0.3s;
  }

  .review-submit-btn:hover {
    background-color: #0056b3;
  }

  .summernote-simple .is-invalid {
    border-color: #dc3545 !important;
  }

  /* Approval Path Preview Styles */
  .approval-path-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
    border-left: 4px solid #2a6d98;
  }

  .approval-path-preview h6 {
    margin-bottom: 10px;
    color: #2a6d98;
  }

  .path-steps {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
  }

  .path-step {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 5px 15px;
    font-size: 13px;
    font-weight: 500;
    color: #495057;
  }

  .path-arrow {
    color: #6c757d;
    font-size: 16px;
  }

  .path-step.highlight {
    background: #2a6d98;
    color: #fff;
    border-color: #2a6d98;
  }

  .info-box {
    background: #e7f3ff;
    border-left: 4px solid #2196F3;
    padding: 12px;
    border-radius: 6px;
    margin-top: 10px;
  }

  .info-box.warning {
    background: #fff3e0;
    border-left-color: #ff9800;
  }

  .info-box.success {
    background: #e8f5e9;
    border-left-color: #4caf50;
  }
</style>

@section('content')
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            
            @if(Auth::user()->role == 'HOD')
                @include('frontend.admin.body.header')
                @include('frontend.admin.body.sidebar')
            @elseif(Auth::user()->role == 'SuperAdmin')
                @include('frontend.superadmin.body.header')
                @include('frontend.superadmin.body.sidebar')
            @else
                @include('frontend.staff.body.header')
                @include('frontend.staff.body.sidebar')
            @endif

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Create Document</h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                            <div class="breadcrumb-item">Create Document</div>
                        </div>
                    </div>

                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Document Creation</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mt-4">
                                            <div class="col-12 col-lg-8 offset-lg-2">
                                                <div id="errorNotification" class="alert alert-danger" style="display:none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;">
                                                    <strong>Error!</strong> <span id="errorMessage"></span>
                                                </div>
                                                <div class="wizard-steps">
                                                    <div class="wizard-step wizard-step-active" data-step="1">
                                                        <div class="wizard-step-icon">
                                                            <i class="far fa-file-alt" style="font-size: 20px"></i>
                                                        </div>
                                                        <div class="wizard-step-label">Document Details</div>
                                                    </div>
                                                    <div class="wizard-step" data-step="2">
                                                        <div class="wizard-step-icon">
                                                            <i class="fas fa-paperclip" style="font-size: 20px"></i>
                                                        </div>
                                                        <div class="wizard-step-label">Annexure</div>
                                                    </div>
                                                    <div class="wizard-step" data-step="3">
                                                        <div class="wizard-step-icon">
                                                            <i class="fas fa-check" style="font-size: 20px"></i>
                                                        </div>
                                                        <div class="wizard-step-label">Submit</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <form class="wizard-content mt-2" action="{{ route('add_document_for_approval') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @if(isset($ticket_id))
                                                <input type="hidden" name="ticket_id" value="{{ $ticket_id }}">
                                            @endif
                                            
                                            <!-- Hidden inputs for auto-assigned approver -->
                                            <input type="hidden" name="user_division" value="{{ Auth::user()->division ?? 'Non Clinical' }}">
                                            <input type="hidden" name="to" id="initial_approver" value="">

                                            <!-- Step 1: Document Details -->
                                            <div class="wizard-pane" id="step1" style="display: block;">
                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Purchase Request</label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="request_type" class="form-check-input" id="purchaseRequestCheckbox">
                                                            <label class="form-check-label" for="purchaseRequestCheckbox">Yes</label>
                                                        </div>
                                                        <small class="form-text text-muted">If checked, document will route through Purchase Head</small>
                                                    </div>
                                                </div>

                                                <!-- Auto-assigned First Approver Display (No Dropdown) -->
                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">First Approver<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div class="info-box" style="background: #e7f3ff; border-left: 4px solid #2196F3; padding: 12px; border-radius: 6px;">
                                                            <i class="fas fa-info-circle"></i> 
                                                            <span id="autoApproverDisplay">Loading...</span>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            <i class="fas fa-info-circle"></i> 
                                                            <span id="pathHint">First approver is automatically determined based on your department division.</span>
                                                        </small>
                                                    </div>
                                                </div>

                                                <!-- Approval Path Preview -->
                                                <div class="form-group row align-items-center">
                                                    <div class="col-md-4"></div>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div id="approvalPathPreview" class="approval-path-preview" style="display: none;">
                                                            <h6><i class="fas fa-code-branch"></i> Approval Path Preview</h6>
                                                            <div id="pathStepsContainer" class="path-steps"></div>
                                                            <div id="pathDescription" class="mt-2" style="font-size: 13px; color: #666;"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Document Title<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <input type="text" name="title" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Subject<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <textarea name="subject" class="summernote-simple" required></textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Description<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <textarea name="description" class="summernote" required></textarea>
                                                    </div>
                                                </div>

                                                <!-- Payment Section -->
                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Is payment involved?</label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <input type="checkbox" id="isPaymentInvolved" name="is_payment_involved">
                                                        <label for="isPaymentInvolved">Yes</label>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center" id="amountFieldGroup" style="display:none;">
                                                    <label class="col-md-4 text-md-right text-left">Amount<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" name="amount" id="amountInput" class="form-control">
                                                            <div class="input-group-append">
                                                                <select class="form-control" name="currency" id="currencySelect" style="width: 100px;">
                                                                    <option value="INR">INR</option>
                                                                    <option value="USD">USD</option>
                                                                    <option value="GBP">GBP</option>
                                                                    <option value="EUR">EUR</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted" id="amountNote"></small>
                                                    </div>
                                                </div>

                                                <!-- Payment Mode Options -->
                                                <div id="paymentModeSection" style="display:none;">
                                                    <div class="form-group row align-items-center">
                                                        <label class="col-md-4 text-md-right text-left">Payment Mode<span style="color:Red"> *</span></label>
                                                        <div class="col-lg-5 col-md-6">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentCash" value="cash">
                                                                <label class="form-check-label" for="paymentCash">Cash</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentannexure" value="annexure">
                                                                <label class="form-check-label" for="paymentannexure">Annexure</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentCheque" value="cheque">
                                                                <label class="form-check-label" for="paymentCheque">Cheque</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentBank" value="bank">
                                                                <label class="form-check-label" for="paymentBank">NEFT/RTGS</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="chequeFields" style="display:none;">
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Cheque in favour of<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="cash_in_favour" id="cashInFavour" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="bankFields" style="display:none;">
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Account Holder Name<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="account_holder" id="accountHolder" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Account Number<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="account_number" id="accountNumber" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">IFSC Code<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="ifsc_code" id="ifscCode" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Account Branch<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="account_branch" id="accountBranch" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="upiFields" style="display:none;">
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">UPI ID<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="upi_id" id="upiId" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Priority<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div class="selectgroup selectgroup-pills">
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="priority" value="Low" class="selectgroup-input" checked required>
                                                                <span class="selectgroup-button">Low</span>
                                                            </label>
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="priority" value="Medium" class="selectgroup-input">
                                                                <span class="selectgroup-button">Medium</span>
                                                            </label>
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="priority" value="High" class="selectgroup-input">
                                                                <span class="selectgroup-button">High</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Justification</label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <textarea name="justification" class="summernote-simple"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 2: Annexure -->
                                            <div class="wizard-pane" id="step2" style="display: none;">
                                                <div class="form-group row align-items-center">
                                                    <div class="input-group">
                                                        <div class="custom-file-drop-area" id="drop-area">
                                                            <p>Drag and drop your files anywhere or</p>
                                                            <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click()">Upload a file</button>
                                                            <input type="file" name="files[]" id="fileInput" class="custom-file-input" multiple accept=".pdf, application/pdf">
                                                            <p style="color:red">(Only PDF files less than 2MB are supported)</p>
                                                        </div>
                                                        <div id="file-list" class="file-list"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 3: Review & Submit -->
                                            <div class="wizard-pane" id="step3" style="display: none;">
                                                <h4 class="review-heading">Review and Confirm</h4>
                                                <div class="review-item">
                                                    <label class="review-label">Approval Path:</label>
                                                    <p class="review-value" id="summary-approval-path"></p>
                                                </div>
                                                <div class="review-item">
                                                    <label class="review-label">Title:</label>
                                                    <p class="review-value" id="summary-title"></p>
                                                </div>
                                                <div class="review-item">
                                                    <label class="review-label">Subject:</label>
                                                    <p class="review-value" id="summary-subject"></p>
                                                </div>
                                                <div class="review-item">
                                                    <label class="review-label">Description:</label>
                                                    <p class="review-value" id="summary-description"></p>
                                                </div>
                                                <div class="review-item">
                                                    <label class="review-label">Amount:</label>
                                                    <p class="review-value" id="summary-amount"></p>
                                                </div>
                                                <div class="review-item" id="summary-payment-mode-row" style="display:none;">
                                                    <label class="review-label">Payment Mode:</label>
                                                    <p class="review-value" id="summary-payment-mode"></p>
                                                </div>
                                                <div class="review-item" id="summary-payment-details-row" style="display:none;">
                                                    <label class="review-label">Payment Details:</label>
                                                    <p class="review-value" id="summary-payment-details"></p>
                                                </div>
                                                <div class="review-item">
                                                    <label class="review-label">Justification:</label>
                                                    <p class="review-value" id="summary-justification"></p>
                                                </div>
                                                <div class="review-item" style="margin-bottom: 25px;">
                                                    <label class="review-label">Annexure:</label>
                                                    <ul id="summary-files" class="review-files"></ul>
                                                </div>
                                                <div class="form-group row align-items-center">
                                                    <button type="submit" class="btn btn-lg btn-primary review-submit-btn" name="action" value="submit">Submit & Send</button>
                                                    <button type="submit" class="btn btn-lg btn-warning review-submit-btn" name="action" value="draft">Save as Draft</button>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-md-4"></div>
                                                <div class="col-lg-4 col-md-6 text-right">
                                                    <a href="#" class="btn btn-icon icon-right btn-primary" id="prevBtn" style="display: none;"><i class="fas fa-arrow-left"></i> Previous</a>
                                                    <a href="#" class="btn btn-icon icon-right btn-primary" id="nextBtn" disabled>Next <i class="fas fa-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        
            <script>
                $(document).ready(function () {
                    // Get logged-in user's division from PHP
                    const userDivision = '{{ Auth::user()->division ?? "Non Clinical" }}';
                    const userDepartment = '{{ Auth::user()->department ?? "" }}';
                    
                    // Determine first approver based on division
                    function getFirstApprover() {
                        if (userDivision === 'Clinical') {
                            return 'Medical Director';
                        } else {
                            return 'General Manager';
                        }
                    }
                    
                    // Set the first approver value
                    const firstApprover = getFirstApprover();
                    $('#initial_approver').val(firstApprover);
                    $('#autoApproverDisplay').html(`<strong>${firstApprover}</strong> <i class="fas fa-arrow-right"></i> (Auto-assigned based on your ${userDivision === 'Clinical' ? 'Clinical' : 'Non-Clinical'} department)`);
                    
                    // Update path hint based on division
                    if (userDivision === 'Clinical') {
                        $('#pathHint').html('🏥 Clinical department: Document will automatically route to Medical Director first, then to General Manager.');
                    } else {
                        $('#pathHint').html('📋 Non-Clinical department: Document will automatically route to General Manager first, then to Medical Director.');
                    }
                    
                    // Function to determine approval path based on user's division and selections
                    function getApprovalPath() {
                        const initialApprover = $('#initial_approver').val();
                        const isPaymentInvolved = $('#isPaymentInvolved').is(':checked');
                        const isPurchaseRequest = $('#purchaseRequestCheckbox').is(':checked');
                        const amount = parseFloat($('#amountInput').val()) || 0;
                        
                        let path = [];
                        
                        if (!initialApprover) {
                            return [];
                        }
                        
                        // Step 1: Add the first approver (auto-determined)
                        path.push(initialApprover);
                        
                        // Step 2: Add the second approver based on division
                        if (userDivision === 'Clinical') {
                            // Clinical department: Medical Director first, then General Manager
                            if (initialApprover !== 'General Manager') {
                                path.push('General Manager');
                            }
                        } else {
                            // Non-Clinical department: General Manager first, then Medical Director
                            if (initialApprover !== 'Medical Director') {
                                path.push('Medical Director');
                            }
                        }
                        
                        // Remove duplicates
                        path = [...new Map(path.map(item => [item, item])).values()];
                        
                        // Step 3: If payment involved
                        if (isPaymentInvolved) {
                            // Check amount threshold
                            if (amount > 200000) {
                                // High value amount > 2 Lakhs
                                path.push('STB Office');
                                path.push('Chairman');
                                
                                // ONLY add Purchase Head Chennai if this IS a purchase request
                                if (isPurchaseRequest) {
                                    path.push('Purchase Head Chennai');
                                }
                                
                                // Finally Finance Head Salem
                                path.push('Finance Head Salem');
                                
                            } else {
                                // Low value amount <= 2 Lakhs
                                // ONLY add Purchase Head Salem if this IS a purchase request
                                if (isPurchaseRequest) {
                                    path.push('Purchase Head');
                                }
                                
                                // Finally Finance Head Salem
                                path.push('Finance Head Salem');
                            }
                        }
                        
                        return path;
                    }
                    
                    // Get the complete path description
                    function getPathDescription() {
                        const initialApprover = $('#initial_approver').val();
                        const isPaymentInvolved = $('#isPaymentInvolved').is(':checked');
                        const isPurchaseRequest = $('#purchaseRequestCheckbox').is(':checked');
                        const amount = parseFloat($('#amountInput').val()) || 0;
                        
                        let description = '';
                        
                        // Determine base routing based on division
                        if (userDivision === 'Clinical') {
                            description = '🏥 Clinical Department: ' + initialApprover + ' → General Manager';
                        } else {
                            description = '📋 Non-Clinical Department: ' + initialApprover + ' → Medical Director';
                        }
                        
                        if (isPaymentInvolved) {
                            if (amount > 200000) {
                                description += ' → STB Office → Chairman';
                                if (isPurchaseRequest) {
                                    description += ' → Purchase Head Chennai';
                                }
                                description += ' → Finance Head Salem';
                            } else {
                                if (isPurchaseRequest) {
                                    description += ' → Purchase Head';
                                }
                                description += ' → Finance Head Salem';
                            }
                        }
                        
                        return description;
                    }
                    
                    // Update approval path preview
                    function updateApprovalPathPreview() {
                        const path = getApprovalPath();
                        const container = $('#pathStepsContainer');
                        const previewDiv = $('#approvalPathPreview');
                        const initialApprover = $('#initial_approver').val();
                        const isPaymentInvolved = $('#isPaymentInvolved').is(':checked');
                        const amount = parseFloat($('#amountInput').val()) || 0;
                        
                        if (path.length === 0 || !initialApprover) {
                            previewDiv.hide();
                            return;
                        }
                        
                        let html = '';
                        path.forEach((step, index) => {
                            let additionalClass = '';
                            if (index === 0) {
                                additionalClass = 'highlight';
                            }
                            html += `<span class="path-step ${additionalClass}">${step}</span>`;
                            if (index < path.length - 1) {
                                html += `<span class="path-arrow"><i class="fas fa-arrow-right"></i></span>`;
                            }
                        });
                        
                        container.html(html);
                        $('#pathDescription').html(getPathDescription());
                        previewDiv.show();
                        
                        // Add payment routing info to hint
                        if (isPaymentInvolved) {
                            if (amount > 200000) {
                                $('#pathHint').html($('#pathHint').html() + '<br><span class="text-warning">⚠️ High Value Amount: Will route through STB Office → Chairman → Finance Head Salem</span>');
                            } else if (amount > 0) {
                                $('#pathHint').html($('#pathHint').html() + '<br><span class="text-info">💰 Payment Involved: Will route through Finance Head Salem</span>');
                            }
                        }
                        
                        // Update summary
                        $('#summary-approval-path').text(path.join(' → '));
                    }
                    
                    // Update amount note based on value
                    function updateAmountNote() {
                        const amount = parseFloat($('#amountInput').val()) || 0;
                        const noteSpan = $('#amountNote');
                        const isPaymentInvolved = $('#isPaymentInvolved').is(':checked');
                        const isPurchaseRequest = $('#purchaseRequestCheckbox').is(':checked');
                        
                        if (!isPaymentInvolved) {
                            noteSpan.html('');
                            return;
                        }
                        
                        let purchasePath = isPurchaseRequest ? ' → Purchase Head' : '';
                        let purchasePathHigh = isPurchaseRequest ? ' → Purchase Head Chennai' : '';
                        let basePath = userDivision === 'Clinical' ? 'Medical Director → General Manager' : 'General Manager → Medical Director';
                        
                        if (amount > 200000) {
                            noteSpan.html(`<strong class="text-warning"><i class="fas fa-exclamation-triangle"></i> ⚠️ Amount exceeds ₹2,00,000 - High Value Approval Required!<br>Complete Path: ${basePath} → STB Office → Chairman${purchasePathHigh} → Finance Head Salem</strong>`);
                        } else if (amount > 0) {
                            noteSpan.html(`<strong class="text-info"><i class="fas fa-info-circle"></i> Amount is less than ₹2,00,000 - Standard Approval<br>Complete Path: ${basePath}${purchasePath} → Finance Head Salem</strong>`);
                        } else {
                            noteSpan.html('');
                        }
                        
                        updateApprovalPathPreview();
                    }
                    
                    // Event listeners for dynamic path calculation
                    $('#purchaseRequestCheckbox').on('change', function() {
                        updateApprovalPathPreview();
                        updateAmountNote();
                    });

                    $('#isPaymentInvolved').on('change', function() {
                        updateApprovalPathPreview();
                        if ($(this).is(':checked') && $('#amountInput').val()) {
                            updateAmountNote();
                        } else {
                            $('#amountNote').html('');
                            updateApprovalPathPreview();
                        }
                    });

                    $('#amountInput').on('input', function() {
                        updateAmountNote();
                        updateApprovalPathPreview();
                    });

                    // Initial preview update
                    updateApprovalPathPreview();

                    const selectors = {
                        amount: '#amountInput',
                        modes: 'input[name="payment_mode"]',
                        modeFields: {
                            cash: '#cashFields',
                            cheque: '#chequeFields',
                            bank: '#bankFields',
                            upi: '#upiFields'
                        },
                        requiredFields: {
                            cheque: ['#cashInFavour'],
                            bank: ['#accountHolder', '#accountNumber', '#ifscCode', '#accountBranch'],
                            upi: ['#upiId']
                        },
                        step1Inputs: '#step1 input[name], #step1 textarea[name]',
                        fileInput: '#fileInput'
                    };

                    function toggleRequired(fields, required) {
                        $(fields.join(',')).attr('required', required);
                    }

                    function clearModeFields() {
                        Object.values(selectors.modeFields).forEach(sel => $(sel).hide());
                        toggleRequired(Object.values(selectors.requiredFields).flat(), false);
                    }

                    function handleModeChange(mode) {
                        clearModeFields();
                        if (mode && selectors.modeFields[mode]) {
                            $(selectors.modeFields[mode]).show();
                            toggleRequired(selectors.requiredFields[mode], true);
                        }
                    }

                    function showError(message) {
                        $('#errorMessage').text(message);
                        $('#errorNotification').fadeIn(300).delay(3000).fadeOut(300);
                    }

                    $(selectors.modes).on('change', function () {
                        if ($(selectors.amount).val().trim() !== '') {
                            handleModeChange(this.value);
                        }
                    });

                    $('#isPaymentInvolved').on('change', function () {
                        if ($(this).is(':checked')) {
                            $('#amountFieldGroup').show();
                            $('#amountInput').attr('required', true);
                            updateApprovalPathPreview();
                        } else {
                            $('#amountFieldGroup').hide();
                            $('#amountInput').val('').attr('required', false);
                            $('#paymentModeSection').hide();
                            $('input[name="payment_mode"]').prop('checked', false);
                            clearModeFields();
                            updateApprovalPathPreview();
                        }
                    });

                    $(selectors.amount).on('input', function () {
                        const hasAmount = $(this).val().trim() !== '';
                        const isPayment = $('#isPaymentInvolved').is(':checked');
                        const amountValue = parseFloat($(this).val().trim());

                        if (!isNaN(amountValue) && amountValue >= 1000) {
                            $('#paymentCash').prop('disabled', true).prop('checked', false);
                            if ($('input[name="payment_mode"]:checked').val() === 'cash') {
                                $('input[name="payment_mode"]').prop('checked', false);
                                $('#cashFields').hide();
                            }
                        } else {
                            $('#paymentCash').prop('disabled', false);
                        }

                        if (hasAmount && isPayment) {
                            $('#paymentModeSection').slideDown();
                            $(selectors.modes).attr('required', true);
                        } else {
                            $('#paymentModeSection').slideUp();
                            $(selectors.modes).attr('required', false).prop('checked', false);
                            clearModeFields();
                        }
                        
                        updateAmountNote();
                    });

                    function validateStep1() {
                        let allValid = true;
                        const isPayment = $('#isPaymentInvolved').is(':checked');
                        const amount = $(selectors.amount).val().trim();
                        
                        if (isPayment && !amount) {
                            $('#amountInput').addClass('is-invalid');
                            allValid = false;
                        } else {
                            $('#amountInput').removeClass('is-invalid');
                        }

                        const selectedMode = $(selectors.modes + ':checked').val();

                        $(selectors.step1Inputs).each(function () {
                            const name = $(this).attr('name');
                            let value = $(this).is('textarea') && $(this).hasClass('summernote-simple')
                                ? $(this).summernote('code').trim()
                                : $(this).val().trim();

                            if (['amount', 'justification', 'to'].includes(name)) return;

                            const paymentFields = ['payment_mode', 'cash_in_favour', 'account_holder', 'account_number', 'ifsc_code', 'account_branch', 'upi_id'];
                            if (!amount && paymentFields.includes(name)) {
                                $(this).removeClass('is-invalid');
                                return;
                            }

                            const modeCheck = {
                                cash_in_favour: selectedMode === 'cheque',
                                account_holder: selectedMode === 'bank',
                                account_number: selectedMode === 'bank',
                                ifsc_code: selectedMode === 'bank',
                                account_branch: selectedMode === 'bank',
                                upi_id: selectedMode === 'upi'
                            };

                            if (!modeCheck[name] && paymentFields.includes(name)) {
                                $(this).removeClass('is-invalid');
                                return;
                            }

                            if (!value || value === '<p><br></p>') {
                                $(this).addClass('is-invalid');
                                allValid = false;
                            } else {
                                $(this).removeClass('is-invalid');
                            }
                        });

                        return allValid;
                    }

                    function validateStep1Extended() {
                        let valid = validateStep1();
                        const amount = $(selectors.amount).val().trim();
                        const selectedMode = $(selectors.modes + ':checked').val();

                        if (amount) {
                            if (!selectedMode) {
                                $(selectors.modes).addClass('is-invalid');
                                return false;
                            } else {
                                $(selectors.modes).removeClass('is-invalid');
                                selectors.requiredFields[selectedMode]?.forEach(id => {
                                    const val = $(id).val().trim();
                                    $(id).toggleClass('is-invalid', !val);
                                    valid = valid && !!val;
                                });
                            }
                        }
                        return valid;
                    }

                    window.validateStep1 = validateStep1Extended;

                    let currentStep = 1;
                    const totalSteps = 3;

                    function updateButtonVisibility() {
                        $('#prevBtn').toggle(currentStep > 1);
                        $('#nextBtn').toggle(currentStep < totalSteps);
                    }

                    function updateWizardSteps() {
                        $('.wizard-step').each((i, el) => {
                            $(el).toggleClass('disabled', i > currentStep - 1);
                        });
                    }

                    function validateStep2() {
                        const files = $('#fileInput')[0].files;
                        if (files.length === 0) {
                            showError('Please upload at least one file.');
                            return false;
                        }
                        return true;
                    }

                    function switchStep(step) {
                        $('#step' + currentStep).fadeOut(300, function () {
                            currentStep = step;
                            $('#step' + currentStep).fadeIn(300);
                            $('.wizard-step').removeClass('wizard-step-active').eq(currentStep - 1).addClass('wizard-step-active');
                            updateButtonVisibility();
                            updateWizardSteps();
                        });
                    }

                    $('#nextBtn').click(function (e) {
                        e.preventDefault();
                        if (currentStep === 1 && !validateStep1()) return showError('Please fill all required fields.');
                        if (currentStep === 2 && !validateStep2()) return;
                        if (currentStep < totalSteps) switchStep(currentStep + 1);
                    });

                    $('#prevBtn').click(function (e) {
                        e.preventDefault();
                        if (currentStep > 1) switchStep(currentStep - 1);
                    });

                    $('.wizard-step').click(function () {
                        const step = $(this).data('step');
                        if ((step === 2 && !validateStep1()) || (step === 3 && (!validateStep1() || !validateStep2()))) {
                            return showError('Please complete previous steps.');
                        }
                        if (step !== currentStep) switchStep(step);
                    });

                    $('#nextBtn').click(function () {
                        if (currentStep === 2) populateSummary();
                    });

                    function populateSummary() {
                        const getCode = name => $(`[name="${name}"]`).hasClass('summernote-simple') ? $(`[name="${name}"]`).summernote('code') : $(`[name="${name}"]`).val();

                        $('#summary-title').text(getCode('title') || 'N/A');
                        $('#summary-subject').html(getCode('subject') || 'N/A');
                        $('#summary-description').html(getCode('description') || 'N/A');
                        $('#summary-amount').text(getCode('amount') || 'N/A');
                        $('#summary-justification').html(getCode('justification') || 'N/A');
                        
                        const isPayment = $('#isPaymentInvolved').is(':checked');
                        if (isPayment) {
                            const mode = $('input[name="payment_mode"]:checked').val();
                            let modeText = 'N/A';
                            let details = '';
                            if (mode === 'cash') {
                                modeText = 'Cash';
                                details = 'N/A';
                            } else if (mode === 'cheque') {
                                modeText = 'Cheque';
                                details = 'In favour of: ' + ($('#cashInFavour').val() || 'N/A');
                            } else if (mode === 'bank') {
                                modeText = 'Bank Transfer';
                                details = 'Account Holder: ' + ($('#accountHolder').val() || 'N/A') + '<br>' +
                                    'Account Number: ' + ($('#accountNumber').val() || 'N/A') + '<br>' +
                                    'IFSC: ' + ($('#ifscCode').val() || 'N/A') + '<br>' +
                                    'Branch: ' + ($('#accountBranch').val() || 'N/A');
                            } else if (mode === 'upi') {
                                modeText = 'UPI';
                                details = 'UPI ID: ' + ($('#upiId').val() || 'N/A');
                            }
                            $('#summary-payment-mode').text(modeText);
                            $('#summary-payment-details').html(details);
                            $('#summary-payment-mode-row').show();
                            $('#summary-payment-details-row').show();
                        } else {
                            $('#summary-payment-mode-row').hide();
                            $('#summary-payment-details-row').hide();
                        }

                        const files = $('#fileInput')[0].files;
                        $('#summary-files').empty().append([...files].map(f => `<li>${f.name}</li>`));
                    }

                    $('#step1').show();
                    updateButtonVisibility();
                });
            </script>

            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const dropArea = document.getElementById("drop-area");
                    const fileInput = document.getElementById("fileInput");
                    const fileList = document.getElementById("file-list");
                    let selectedFiles = [];

                    function updateFilesDisplay() {
                        const dt = new DataTransfer();
                        selectedFiles.forEach(file => dt.items.add(file));
                        fileInput.files = dt.files;

                        fileList.innerHTML = '';
                        selectedFiles.forEach((file, index) => {
                            fileList.innerHTML += `
                                <div class="file-item">
                                    <span>${file.name}</span>
                                    <button class="remove-btn" onclick="removeFile(${index})">×</button>
                                </div>`;
                        });
                    }

                    window.removeFile = function (index) {
                        selectedFiles.splice(index, 1);
                        updateFilesDisplay();
                    };

                    fileInput.addEventListener("change", () => {
                        Array.from(fileInput.files).forEach(file => {
                            if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                                selectedFiles.push(file);
                            }
                        });
                        updateFilesDisplay();
                    });

                    ['dragover', 'drop', 'dragleave'].forEach(evt => {
                        dropArea.addEventListener(evt, e => e.preventDefault());
                    });

                    dropArea.addEventListener("dragover", () => dropArea.classList.add("dragover"));
                    dropArea.addEventListener("dragleave", () => dropArea.classList.remove("dragover"));
                    dropArea.addEventListener("drop", (e) => {
                        dropArea.classList.remove("dragover");
                        Array.from(e.dataTransfer.files).forEach(file => {
                            if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                                selectedFiles.push(file);
                            }
                        });
                        updateFilesDisplay();
                    });
                });
            </script>
                    
            @include('frontend.body.footer')
        </div>
    </div>
@endsection

@include('frontend.postal.script')