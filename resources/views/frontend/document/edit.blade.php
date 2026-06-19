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
</style>

@section('content')

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            
            @include('frontend.admin.body.header')
            @include('frontend.admin.body.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Edit Document</h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                            <div class="breadcrumb-item">Edit Document</div>
                        </div>
                    </div>

                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Edit Document</h4>
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

                                        <form class="wizard-content mt-2" action="{{ route('document.update', $doc->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            
                                            <div class="wizard-pane" id="step1" style="display: block;">
                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Purchase Request</label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="request_type" class="form-check-input" id="purchaseRequestCheckbox" @if($doc->is_purchase == 'Y') checked @endif>
                                                            <label class="form-check-label" for="purchaseRequestCheckbox">Yes</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Approval Path Selection - Hospital Workflow -->
                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Select First Approver<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <select class="form-control" id="initial_approver" name="to" required>
                                                            <option value="">Select First Approver</option>
                                                            <option value="Medical Director" {{ $doc->to == 'Medical Director' ? 'selected' : '' }}>Medical Director</option>
                                                            <option value="General Manager" {{ $doc->to == 'General Manager' ? 'selected' : '' }}>General Manager</option>
                                                        </select>
                                                        <small class="form-text text-muted">
                                                            <i class="fas fa-info-circle"></i> 
                                                            Based on your selection, the document will route automatically.
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
                                                        <input type="text" name="title" value="{{ $doc->title }}" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Subject<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <textarea name="subject" class="summernote-simple" required>{{ $doc->subject }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Description<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <textarea name="description" class="summernote" required>{{ $doc->description }}</textarea>
                                                    </div>
                                                </div>

                                                <!-- Payment Section -->
                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Is payment involved?</label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <input type="checkbox" id="isPaymentInvolved" name="is_payment_involved" {{ $doc->amount ? 'checked' : '' }}>
                                                        <label for="isPaymentInvolved">Yes</label>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center" id="amountFieldGroup" style="{{ $doc->amount ? '' : 'display:none;' }}">
                                                    <label class="col-md-4 text-md-right text-left">Amount<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div class="input-group">
                                                            <input type="number" step="0.01" name="amount" id="amountInput" value="{{ $doc->amount }}" class="form-control">
                                                            <div class="input-group-append">
                                                                <select class="form-control" name="currency" id="currencySelect" style="width: 100px;">
                                                                    <option value="INR" {{ $doc->currency == 'INR' ? 'selected' : '' }}>INR</option>
                                                                    <option value="USD" {{ $doc->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                                                    <option value="GBP" {{ $doc->currency == 'GBP' ? 'selected' : '' }}>GBP</option>
                                                                    <option value="EUR" {{ $doc->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted" id="amountNote"></small>
                                                    </div>
                                                </div>

                                                <!-- Payment Mode Options -->
                                                <div id="paymentModeSection" style="{{ $doc->amount ? '' : 'display:none;' }}">
                                                    <div class="form-group row align-items-center">
                                                        <label class="col-md-4 text-md-right text-left">Payment Mode</label>
                                                        <div class="col-lg-5 col-md-6">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentModeCash" value="cash" {{ $doc->payment_mode == 'cash' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="paymentModeCash">Cash</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentModeAnnexure" value="annexure" {{ $doc->payment_mode == 'annexure' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="paymentModeAnnexure">Annexure</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentModeCheque" value="cheque" {{ $doc->payment_mode == 'cheque' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="paymentModeCheque">Cheque</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="payment_mode" id="paymentModeBank" value="bank" {{ $doc->payment_mode == 'bank' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="paymentModeBank">NEFT/RTGS</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="chequeFields" style="display: {{ $doc->payment_mode == 'cheque' ? 'block' : 'none' }};">
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Cheque in Favour of<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="cash_in_favour" id="cashInFavour" class="form-control" value="{{ $doc->cash_in_favour }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="bankFields" style="display: {{ $doc->payment_mode == 'bank' ? 'block' : 'none' }};">
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Account Holder<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="account_holder" id="accountHolder" class="form-control" value="{{ $doc->account_holder }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Account Number<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="account_number" id="accountNumber" class="form-control" value="{{ $doc->account_number }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">IFSC Code<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="ifsc_code" id="ifscCode" class="form-control" value="{{ $doc->ifsc_code }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">Branch<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="account_branch" id="accountBranch" class="form-control" value="{{ $doc->account_branch }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="upiFields" style="display: {{ $doc->payment_mode == 'upi' ? 'block' : 'none' }};">
                                                        <div class="form-group row align-items-center">
                                                            <label class="col-md-4 text-md-right text-left">UPI ID<span style="color:Red"> *</span></label>
                                                            <div class="col-lg-5 col-md-6">
                                                                <input type="text" name="upi_id" id="upiId" class="form-control" value="{{ $doc->upi_id }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Priority<span style="color:Red"> *</span></label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div class="selectgroup selectgroup-pills">
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="priority" value="Low" class="selectgroup-input" @if($doc->priority == 'Low') checked @endif required>
                                                                <span class="selectgroup-button">Low</span>
                                                            </label>
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="priority" value="Medium" class="selectgroup-input" @if($doc->priority == 'Medium') checked @endif>
                                                                <span class="selectgroup-button">Medium</span>
                                                            </label>
                                                            <label class="selectgroup-item">
                                                                <input type="radio" name="priority" value="High" class="selectgroup-input" @if($doc->priority == 'High') checked @endif>
                                                                <span class="selectgroup-button">High</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Justification</label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <textarea name="justification" class="summernote-simple">{{ $doc->justification }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

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

                                                <!-- Display Existing Annexure Files -->
                                                <div class="form-group row align-items-center">
                                                    <label class="col-md-4 text-md-right text-left">Existing Annexures:</label>
                                                    <div class="col-lg-5 col-md-6">
                                                        <div id="existing-file-list" class="file-list">
                                                            @foreach($annexures as $attachment)
                                                                <div class="file-item">
                                                                    <span>{{ basename($attachment->annexure) }}</span>
                                                                    <button type="button" class="remove-btn" onclick="removeExistingFile('{{ $attachment->id }}')">×</button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

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
                    // Function to determine approval path based on selections
                    function getApprovalPath() {
                        const initialApprover = $('#initial_approver').val();
                        const isPaymentInvolved = $('#isPaymentInvolved').is(':checked');
                        const amount = parseFloat($('#amountInput').val()) || 0;
                        
                        let path = [];
                        
                        if (!initialApprover) {
                            return [];
                        }
                        
                        if (initialApprover === 'Medical Director') {
                            path = ['Medical Director', 'General Manager'];
                        } else if (initialApprover === 'General Manager') {
                            path = ['General Manager', 'Medical Director'];
                        } else {
                            path = [initialApprover];
                        }
                        
                        if (isPaymentInvolved) {
                            path.push('Purchase Head');
                            
                            if (amount > 200000) {
                                path.push('Purchase Head Chennai');
                                path.push('STB Office');
                                path.push('Chairman');
                                path.push('Finance Head Salem');
                            } else if (amount > 0) {
                                path.push('Finance Head Salem');
                            }
                        }
                        
                        return path;
                    }

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
                        
                        let description = '';
                        if (initialApprover === 'Medical Director') {
                            description = '📋 Selected: Medical Director first → Then automatically forwards to General Manager';
                        } else if (initialApprover === 'General Manager') {
                            description = '📋 Selected: General Manager first → Then automatically forwards to Medical Director';
                        }
                        
                        if (isPaymentInvolved) {
                            description += ' → Purchase Head';
                            if (amount > 200000) {
                                description += ' → Purchase Head Chennai → STB Office → Chairman → Finance Head Salem';
                            } else if (amount > 0) {
                                description += ' → Finance Head Salem';
                            }
                        }
                        
                        $('#pathDescription').html(description);
                        previewDiv.show();
                        $('#summary-approval-path').text(path.join(' → '));
                    }

                    function updateAmountNote() {
                        const amount = parseFloat($('#amountInput').val()) || 0;
                        const noteSpan = $('#amountNote');
                        const initialApprover = $('#initial_approver').val();
                        const isPaymentInvolved = $('#isPaymentInvolved').is(':checked');
                        
                        if (!isPaymentInvolved) {
                            noteSpan.html('');
                            return;
                        }
                        
                        let pathPrefix = '';
                        if (initialApprover === 'Medical Director') {
                            pathPrefix = 'Medical Director → General Manager';
                        } else if (initialApprover === 'General Manager') {
                            pathPrefix = 'General Manager → Medical Director';
                        }
                        
                        if (amount > 200000) {
                            noteSpan.html(`<strong class="text-warning"><i class="fas fa-exclamation-triangle"></i> Note: Amount exceeds ₹2,00,000. Document will be routed to Purchase Head → Purchase Head Chennai → STB Office → Chairman → Finance Head Salem.<br><br>📌 Complete Path: ${pathPrefix} → Purchase Head → Purchase Head Chennai → STB Office → Chairman → Finance Head Salem</strong>`);
                        } else if (amount > 0) {
                            noteSpan.html(`<strong class="text-info"><i class="fas fa-info-circle"></i> Note: Amount is less than ₹2,00,000. Document will be routed to Purchase Head → Finance Head Salem.<br><br>📌 Complete Path: ${pathPrefix} → Purchase Head → Finance Head Salem</strong>`);
                        } else {
                            noteSpan.html('');
                        }
                        
                        updateApprovalPathPreview();
                    }

                    $('#initial_approver').on('change', function() {
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

                    updateApprovalPathPreview();
                    updateAmountNote();

                    // Rest of your existing wizard navigation code...
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
                        if (currentStep === 1) {
                            // Validate required fields
                            if (!$('#initial_approver').val()) {
                                showError('Please select first approver');
                                return;
                            }
                        }
                        if (currentStep < totalSteps) switchStep(currentStep + 1);
                        if (currentStep === 2) populateSummary();
                    });

                    $('#prevBtn').click(function (e) {
                        e.preventDefault();
                        if (currentStep > 1) switchStep(currentStep - 1);
                    });

                    function populateSummary() {
                        $('#summary-title').text($('input[name="title"]').val() || 'N/A');
                        $('#summary-subject').html($('textarea[name="subject"]').summernote('code') || 'N/A');
                        $('#summary-description').html($('textarea[name="description"]').summernote('code') || 'N/A');
                        $('#summary-amount').text($('#amountInput').val() || 'N/A');
                        $('#summary-justification').html($('textarea[name="justification"]').summernote('code') || 'N/A');

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

                        // Files summary
                        $('#summary-files').empty();
                        $('#existing-file-list .file-item span').each(function() {
                            $('#summary-files').append(`<li>${$(this).text()}</li>`);
                        });
                        const files = $('#fileInput')[0].files;
                        Array.from(files).forEach(file => {
                            $('#summary-files').append(`<li>${file.name}</li>`);
                        });
                    }

                    $('#step1').show();
                    updateButtonVisibility();
                    updateWizardSteps();
                });

                function showError(message) {
                    $('#errorMessage').text(message);
                    $('#errorNotification').fadeIn(300).delay(3000).fadeOut(300);
                }

                function removeExistingFile(fileId) {
                    $.ajax({
                        url: '/remove-annexure/' + fileId,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                const fileItem = $(`button[onclick="removeExistingFile('${fileId}')"]`).closest('.file-item');
                                fileItem.remove();
                                $('#summary-files li').filter(function() {
                                    return $(this).text().trim() === fileItem.find('span').text().trim();
                                }).remove();
                            } else {
                                alert('Failed to remove the file. Please try again.');
                            }
                        },
                        error: function() {
                            alert('An error occurred while removing the file.');
                        }
                    });
                }
            </script>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const dropArea = document.getElementById("drop-area");
                    const fileInput = document.getElementById("fileInput");
                    const fileList = document.getElementById("file-list");
                    let selectedFiles = [];

                    function updateFileInput() {
                        const dt = new DataTransfer();
                        selectedFiles.forEach(file => dt.items.add(file));
                        fileInput.files = dt.files;
                    }

                    function updateFileList() {
                        fileList.innerHTML = '';
                        selectedFiles.forEach((file, index) => {
                            const fileItem = document.createElement("div");
                            fileItem.classList.add("file-item");
                            fileItem.innerHTML = `
                                <span>${file.name}</span>
                                <button class="remove-btn" onclick="removeFile(${index})">×</button>
                            `;
                            fileList.appendChild(fileItem);
                        });
                    }

                    window.removeFile = function(index) {
                        selectedFiles.splice(index, 1);
                        updateFileInput();
                        updateFileList();
                    };

                    fileInput.addEventListener("change", function() {
                        const newFiles = Array.from(fileInput.files);
                        newFiles.forEach(file => {
                            if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                                selectedFiles.push(file);
                            }
                        });
                        updateFileInput();
                        updateFileList();
                    });

                    dropArea.addEventListener("dragover", (e) => {
                        e.preventDefault();
                        dropArea.classList.add("dragover");
                    });

                    dropArea.addEventListener("dragleave", () => {
                        dropArea.classList.remove("dragover");
                    });

                    dropArea.addEventListener("drop", (e) => {
                        e.preventDefault();
                        dropArea.classList.remove("dragover");
                        const droppedFiles = Array.from(e.dataTransfer.files);
                        droppedFiles.forEach(file => {
                            if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                                selectedFiles.push(file);
                            }
                        });
                        updateFileInput();
                        updateFileList();
                    });
                });
            </script>

            @include('frontend.body.footer')
        </div>
    </div>
@endsection

@include('frontend.postal.script')