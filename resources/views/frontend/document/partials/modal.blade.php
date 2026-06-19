<!-- Acknowledge & Forward - Bootstrap native modal (triggered via data-toggle on the button) -->
<div class="modal fade" id="acknowledgeStbModal" tabindex="-1" role="dialog" aria-labelledby="acknowledgeStbModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acknowledgeStbModalLabel">Acknowledge &amp; Forward</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="acknowledgeStbForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Acknowledgment Remarks</label>
                        <div class="input-group">
                            <div class="col-sm-12 col-md-12">
                                <textarea name="message" class="summernote form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Acknowledge">
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-shadow btn-stb-ack">Acknowledge &amp; Forward</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form class="modal-part" id="modal-approve-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        @if($doc->amount)
            <div class="form-group row align-items-center">
                <label class="col-md-6 text-md-right text-left">Requested amount<span style="color:Red"> *</span></label>
                <div class="col-lg-5 col-md-6">
                    <input type="text" name="amount" value="{{ $doc->amount }}" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group row align-items-center">
                <label class="col-md-6 text-md-right text-left">Recommended amount<span style="color:Red"> *</span></label>
                <div class="col-lg-5 col-md-6">
                    <input type="text" name="recommended_amount" value="{{ $doc->recommended_amount ? $doc->recommended_amount : $doc->amount }}" class="form-control" {{ $doc->recommended_amount ? 'readonly' : '' }}>
                </div>
            </div>
            @if($doc->recommended_amount)
                <div class="form-group row align-items-center">
                    <label class="col-md-6 text-md-right text-left">Sanctioned amount<span style="color:Red"> *</span></label>
                    <div class="col-lg-5 col-md-6">
                        <input type="text" name="sanctioned_amount" value="{{ $doc->sanctioned_amount ? $doc->sanctioned_amount : $doc->recommended_amount }}" class="form-control">
                    </div>
                </div>
            @endif
        @endif
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Approved">
    </div>
</form>

<form class="modal-part" id="modal-approve-in-principle-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Approved in Principle">
    </div>
</form>

<form class="modal-part" id="modal-retract-part">
    @csrf
    <div class="form-group">
        <label>Reason</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <p style = "color:red">Retract option is available only once!</p>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Retract">
    </div>
</form>

<form class="modal-part" id="modal-hold-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Hold">
    </div>
</form>

<form class="modal-part" id="modal-close-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Close">
    </div>
</form>

<form class="modal-part" id="modal-completed-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Completed">
    </div>
</form>

<form class="modal-part" id="modal-pay-part">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Payment Mode <span style="color:Red">*</span></label>
                <select name="mode" class="form-control" id="payment-mode" required>
                    <option value="">Select Payment Mode</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque">Cheque</option>
                    <option value="NEFT/RTGS">NEFT/RTGS</option>
                    <option value="UPI">UPI</option>
                </select>
            </div>
            <div class="form-group">
                <label>Payment Reference No <span style="color:Red">*</span></label>
                <input type="text" name="payment_reference_no" class="form-control" placeholder="Reference Number" required>
            </div>
            <div class="form-group">
                <label>Payment Date <span style="color:Red">*</span></label>
                <input type="date" name="payment_date" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Payment Type <span style="color:Red">*</span></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payment_type" id="full_payment" value="Full Payment" required>
                    <label class="form-check-label" for="full_payment">Full Payment</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payment_type" id="partial_payment" value="Partial Payment">
                    <label class="form-check-label" for="partial_payment">Partial Payment</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Expenditure ID <span style="color:Red">*</span></label>
                <input type="text" name="expenditure_id" id="expenditure_id" class="form-control" value="{{ isset($expenditure_id) ? $expenditure_id : '' }}" required>
            </div>
            <div class="form-group">
                <label>Paid Amount <span style="color:Red">*</span></label>
                <input type="number" step="0.01" name="paid_amount" class="form-control" placeholder="Amount Paid" required>
            </div>
        </div>
    </div>
    
    <!-- Cheque Date Fields (Initially Hidden) -->
    <div class="row cheque-date-fields" style="display: none;">
        <div class="col-md-6">
            <div class="form-group">
                <label>Cheque Issue Date</label>
                <input type="date" name="cheque_issue_date" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Cheque Cleared Date</label>
                <input type="date" name="cheque_cleared_date" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Expenditure Category Dropdown -->
            <div class="form-group">
                <label>Expenditure Category <span style="color:Red">*</span></label>
                <select name="expenditure_category" class="form-control" id="expenditure-category" required>
                    <option value="">Select Category</option>
                    <option value="Accredation and Ranking">Accredation and Ranking</option>
                    <option value="Admission/ Advertisement">Admission/ Advertisement</option>
                    <option value="AIU Tournament">AIU Tournament</option>
                    <option value="Alumni Meet - Chennai campus">Alumni Meet - Chennai campus</option>
                    <option value="Alumni Meet - Chennai; PB related">Alumni Meet - Chennai; PB related</option>
                    <option value="AMC/ Maintenance of Equipments">AMC/ Maintenance of Equipments</option>
                    <option value="Building Maintenance">Building Maintenance</option>
                    <option value="Building Rennovation">Building Rennovation</option>
                    <option value="Chennai salary Contribution">Chennai salary Contribution</option>
                    <option value="CoE/PhD Contribution">CoE/PhD Contribution</option>
                    <option value="Conference Registration Fee">Conference Registration Fee</option>
                    <option value="Deepawali Exgratia">Deepawali Exgratia</option>
                    <option value="EPALM related">EPALM related</option>
                    <option value="EPSI program">EPSI program</option>
                    <option value="Flip book Subscription">Flip book Subscription</option>
                    <option value="Fuel-Vehicle">Fuel-Vehicle</option>
                    <option value="Fuel-Vehicle; Generator">Fuel-Vehicle; Generator</option>
                    <option value="Hackathan Expenses">Hackathan Expenses</option>
                    <option value="HPE Conference Registration Fee">HPE Conference Registration Fee</option>
                    <option value="ICTIH'26 conference">ICTIH'26 conference</option>
                    <option value="ICTIH'26 conference Expenses">ICTIH'26 conference Expenses</option>
                    <option value="Ideathon Expenses">Ideathon Expenses</option>
                    <option value="ISR">ISR</option>
                    <option value="Land purchase">Land purchase</option>
                    <option value="Legal Fee">Legal Fee</option>
                    <option value="Marathon Expenses; VINSPORTS-25">Marathon Expenses; VINSPORTS-25</option>
                    <option value="Meeting Expenses">Meeting Expenses</option>
                    <option value="Miscellaneous Expenses">Miscellaneous Expenses</option>
                    <option value="Mobile & Telephone">Mobile & Telephone</option>
                    <option value="NASCENT MR 2025 Conference Registration Fee">NASCENT MR 2025 Conference Registration Fee</option>
                    <option value="Other Income">Other Income</option>
                    <option value="Patent related">Patent related</option>
                    <option value="PB related">PB related</option>
                    <option value="Printing and Stationaries">Printing and Stationaries</option>
                    <option value="Purchase of Computer and its accessories">Purchase of Computer and its accessories</option>
                    <option value="Purchase of Container">Purchase of Container</option>
                    <option value="Purchase of Equipments">Purchase of Equipments</option>
                    <option value="Purchase of Furniture">Purchase of Furniture</option>
                    <option value="Purchase of Licenses including softwares">Purchase of Licenses including softwares</option>
                    <option value="Purchase of Xerox">Purchase of Xerox</option>
                    <option value="Refunds">Refunds</option>
                    <option value="Research Day Expenses">Research Day Expenses</option>
                    <option value="Research Incentives -15th Phase">Research Incentives -15th Phase</option>
                    <option value="Research Incentives">Research Incentives</option>
                    <option value="SALEM Electricity">SALEM Electricity</option>
                    <option value="Salem Salary & Other Benefits">Salem Salary & Other Benefits</option>
                    <option value="Seed Money">Seed Money</option>
                    <option value="Sponser to Institute for conducting National conferences">Sponser to Institute for conducting National conferences</option>
                    <option value="Sponsorship">Sponsorship</option>
                    <option value="Students Club festival - Salem">Students Club festival - Salem</option>
                    <option value="TA/DA Expenses">TA/DA Expenses</option>
                    <option value="TDS Payment">TDS Payment</option>
                    <option value="Tea Expenses">Tea Expenses</option>
                    <option value="University Fencing work">University Fencing work</option>
                    <option value="Vehicle EMI">Vehicle EMI</option>
                    <option value="Vehicle Insurance">Vehicle Insurance</option>
                    <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                    <option value="Vehicle Rent">Vehicle Rent</option>
                    <option value="VINSPORTS-25">VINSPORTS-25</option>
                    <option value="V-Lead Conference Expenses - HPE">V-Lead Conference Expenses - HPE</option>
                    <option value="VMKVASC Expenses">VMKVASC Expenses</option>
                    <option value="other">Other (Please specify)</option>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <!-- Hidden field for custom category input -->
            <div class="form-group custom-category-group" style="display: none;">
                <label>Specify Other Category <span style="color:Red">*</span></label>
                <input type="text" name="custom_expenditure_category" class="form-control" placeholder="Enter custom expenditure category">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_tds_applicable" id="is_tds_applicable">
                    <label class="form-check-label" for="is_tds_applicable">TDS Applicable</label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group tds-amount-group" style="display: none;">
                <label>TDS Amount</label>
                <input type="number" step="0.01" name="tds_amount" class="form-control" placeholder="Enter TDS amount">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control" placeholder="Any additional remarks"></textarea>
            </div>
        </div> 
    </div>
    <div class="form-group">
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Paid">
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tdsCheckbox = document.getElementById('is_tds_applicable');
        const tdsAmountGroup = document.querySelector('.tds-amount-group');
        const fullPaymentRadio = document.getElementById('full_payment');
        const partialPaymentRadio = document.getElementById('partial_payment');
        const expenditureIdInput = document.getElementById('expenditure_id');
        const paymentModeSelect = document.getElementById('payment-mode');
        const chequeDateFields = document.querySelector('.cheque-date-fields');
        const expenditureCategorySelect = document.getElementById('expenditure-category');
        const customCategoryGroup = document.querySelector('.custom-category-group');
        const customCategoryInput = document.querySelector('input[name="custom_expenditure_category"]');
        
        // Safe check for user role
        let isHOD = '0';
        @if(isset($user))
            isHOD = "{{ $user->role == 'HOD' ? '1' : '0' }}";
        @endif

        // TDS functionality
        if (tdsCheckbox) {
            tdsCheckbox.addEventListener('change', function() {
                if(this.checked) {
                    if(tdsAmountGroup) tdsAmountGroup.style.display = 'block';
                } else {
                    if(tdsAmountGroup) tdsAmountGroup.style.display = 'none';
                    const tdsInput = document.querySelector('input[name="tds_amount"]');
                    if(tdsInput) tdsInput.value = '';
                }
            });
        }

        // Expenditure ID editability based on payment type, except for HOD
        if (isHOD !== '1') {
            if (fullPaymentRadio) {
                fullPaymentRadio.addEventListener('change', function() {
                    if(this.checked && expenditureIdInput) {
                        expenditureIdInput.readOnly = true;
                    }
                });
            }

            if (partialPaymentRadio) {
                partialPaymentRadio.addEventListener('change', function() {
                    if(this.checked && expenditureIdInput) {
                        expenditureIdInput.readOnly = false;
                    }
                });
            }

            // Also handle the case when page loads with a pre-selected value
            if (fullPaymentRadio && fullPaymentRadio.checked && expenditureIdInput) {
                expenditureIdInput.readOnly = true;
            } else if (partialPaymentRadio && partialPaymentRadio.checked && expenditureIdInput) {
                expenditureIdInput.readOnly = false;
            }
        } else {
            if(expenditureIdInput) expenditureIdInput.readOnly = false;
        }

        // Show/Hide cheque date fields based on payment mode
        if (paymentModeSelect) {
            paymentModeSelect.addEventListener('change', function() {
                if (this.value === 'Cheque') {
                    if(chequeDateFields) chequeDateFields.style.display = 'flex';
                } else {
                    if(chequeDateFields) chequeDateFields.style.display = 'none';
                    const issueDate = document.querySelector('input[name="cheque_issue_date"]');
                    const clearedDate = document.querySelector('input[name="cheque_cleared_date"]');
                    if(issueDate) issueDate.value = '';
                    if(clearedDate) clearedDate.value = '';
                }
            });

            // Also handle the initial state on page load
            if (paymentModeSelect.value === 'Cheque' && chequeDateFields) {
                chequeDateFields.style.display = 'flex';
            }
        }
        
        // Show/Hide custom category input based on selection
        if (expenditureCategorySelect) {
            expenditureCategorySelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    if(customCategoryGroup) customCategoryGroup.style.display = 'block';
                    if(customCategoryInput) customCategoryInput.required = true;
                } else {
                    if(customCategoryGroup) customCategoryGroup.style.display = 'none';
                    if(customCategoryInput) {
                        customCategoryInput.required = false;
                        customCategoryInput.value = '';
                    }
                }
            });
            
            // Handle initial state for custom category
            if (expenditureCategorySelect.value === 'other') {
                if(customCategoryGroup) customCategoryGroup.style.display = 'block';
                if(customCategoryInput) customCategoryInput.required = true;
            }
        }
    });
</script>

<form class="modal-part" id="modal-process-payment-part">
    @csrf
    
    @php
        $staff = App\Models\User::where('department', 'Students Welfare')->get();
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Expenditure ID</label>
                <input type="text" name="expenditure_id" class="form-control" value="{{ isset($process_payment) && $process_payment ? $process_payment->expenditure_id : (isset($expenditure_id) ? $expenditure_id : '') }}" readonly>
            </div>
            <div class="form-group">
                <label>Assign to</label>
                <select name="assigned_to" class="form-control" required>
                    <option value="">Select the staff to assign</option>
                    @foreach($staff as $staff_member)
                        <option value="{{ $staff_member->id }}">{{ $staff_member->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" style="min-width:350px; max-width:100%; min-height:150px; height:100%; width:100%; border-color: #6fb4df" required></textarea>
            </div>
        </div> 
    </div>
    <div class="form-group">
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Payment In Progress">
    </div>
</form>

<form class="modal-part" id="modal-reject-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Rejected">
    </div>
</form>

<form class="modal-part" id="modal-delete-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Deleted">
    </div>
</form>

<form class="modal-part" id="modal-revoke-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Revoked">
    </div>
</form>

<form class="modal-part" id="modal-pending-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Pending">
    </div>
</form>

<form class="modal-part" id="modal-discuss-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Discuss">
    </div>
</form>

<form class="modal-part" id="modal-noted-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Noted">
    </div>
</form>

<form class="modal-part" id="modal-forward-doc-part">
    @csrf
    <div class="form-group">
        
        <div class="input-group mb-4">
            <label>Forward To</label>
            <div class="col-sm-12 col-md-12">
            
            @php
                $forwardedTo = isset($doc->forwarded_to) ? explode(', ', $doc->forwarded_to) : [];
                $forwardedTo = array_map('trim', $forwardedTo);
            @endphp

            @if(isset($doc->forwarded_to) && $doc->forwarded_to)
                <!-- Selected departments tags will display here -->
                <div id="selected-departments" class="mb-2">
                @foreach($forwardedTo as $section)
                    <span class="badge badge-danger mr-1">{{ $section }} <span class="remove-tag" data-dept="{{ $section }}" style="cursor: pointer;">&times;</span></span>
                @endforeach
                </div>
            @else
                <!-- Selected departments tags will display here -->
                <div id="selected-departments" class="mb-2"></div>
            @endif
                
                <!-- Searchable input for the dropdown -->
                <div class="dropdown">
                    <input type="text" id="forward_to_search" name="forward_to" value="{{ $doc->forwarded_to ?? '' }}" class="form-control" placeholder="Search and select departments" autocomplete="off" data-toggle="dropdown">
                    <div id="forward-suggestions" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                </div>
            </div>
        </div><br>
        <div class="form-group mt-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="send_original" name="send_original" value="1" checked>
                <label class="custom-control-label" for="send_original">Send Original</label>
            </div>
        </div><br>
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Forward">
    </div>
</form>

<form class="modal-part" id="modal-re-submit-part">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Re-Submit">
    </div>
</form>

<form class="modal-part" id="modal-comment-part" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12 col-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Commented">
    </div>
    <div class="form-group row mb-4">
        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">File</label>
        <div class="col-sm-12 col-md-7">
        <div class="custom-file">
            <input type="file" name="file" class="custom-file-input">
            <label class="custom-file-label">Choose file</label>
        </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.custom-file-input').on('change', function (event) {
                var inputFile = event.currentTarget;
                $(inputFile).parent()
                    .find('.custom-file-label')
                    .html(inputFile.files[0].name);
            });
        });
    </script>
    @if(isset($user) && Auth::user()->department == 'Purchase')
        <div class="form-group row mb-4">
            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">File Type</label>
            <div class="col-sm-12 col-md-7">
            <select class="form-control selectric" name="file_type" required>  
                <option>Select</option>
                <option value="Purchase Committee Report">Purchase Committee Report</option>
                <option value="Purchase Order">Purchase Order</option>
                <option value="Work Order">Work Order</option>
                <option value="Annexure">Annexure</option>
            </select>
            </div>
        </div>
    @else
        <input type="hidden" name="file_type" value="Annexure">
    @endif
</form>

<form class="modal-part" id="modal-request-doc-part" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12 col-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Requested">
    </div>
</form>

<form class="modal-part" id="modal-send-doc-part" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12 col-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Send">
    </div>
</form>

<form class="modal-part" id="modal-cancel-request-part" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Message</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12 col-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
        <input type="hidden" name="doc_id" value="{{ $doc->id }}">
        <input type="hidden" name="status" value="Cancel RFA">
    </div>
</form>

<!-- Chairman Approval Modal -->
<div class="modal fade" id="chairmanApprovalModal" tabindex="-1" role="dialog" aria-labelledby="chairmanApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #461a7a);">
                <h5 class="modal-title text-white" id="chairmanApprovalModalLabel">
                    <i class="fas fa-check-double"></i> Chairman Approval
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="chairmanApprovalForm">
                @csrf
                <div class="modal-body">

                    @if($doc->amount)
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Requested Amount (₹)</label>
                                <input type="text" name="amount" value="{{ $doc->amount }}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Recommended Amount (₹)</label>
                                <input type="text" name="recommended_amount" value="{{ $doc->recommended_amount ?: $doc->amount }}" class="form-control" {{ $doc->recommended_amount ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sanctioned Amount (₹)</label>
                                <input type="text" name="sanctioned_amount" value="{{ $doc->sanctioned_amount ?: $doc->recommended_amount }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="message" class="summernote form-control"></textarea>
                    </div>

                    @if($doc->is_payment_involved == 'Y')
                    <div class="card mt-3" style="border: 1px dashed #6f42c1; border-radius: 8px;">
                        <div class="card-body py-3">
                            <label class="font-weight-bold text-purple mb-1">
                                <i class="fas fa-university"></i> Route directly to Finance Head
                                <small class="text-muted font-weight-normal">(optional — leave blank to follow normal sequence)</small>
                            </label>
                            <select name="finance_head" class="form-control">
                                <option value="">-- Skip PA step &amp; assign Finance Head directly --</option>
                                <option value="Finance Head Salem">Finance Head Salem</option>
                                <option value="Finance Head Chennai">Finance Head Chennai</option>
                                <option value="Finance Head Karaikal">Finance Head Karaikal</option>
                                <option value="Finance Head Pondy">Finance Head Pondy</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Chairman Approve">
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-chairman btn-shadow btn-chairman-submit">
                        <i class="fas fa-check-double"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Finance Head Forward Modal (Finance Head forwarding to another Finance location) -->
<div class="modal fade" id="financeForwardModal" tabindex="-1" role="dialog" aria-labelledby="financeForwardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2a6d98, #1a4f6e);">
                <h5 class="modal-title text-white" id="financeForwardModalLabel">
                    <i class="fas fa-exchange-alt"></i> Forward to Another Finance Location
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="financeForwardForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Forward To <span class="text-danger">*</span></label>
                        <select name="forward_to" class="form-control" required>
                            <option value="">-- Select Finance Location --</option>
                            @foreach(['Finance Head Salem', 'Finance Head Chennai', 'Finance Head Karaikal', 'Finance Head Pondy'] as $fh)
                                @if($fh !== ($user->department ?? ''))
                                    <option value="{{ $fh }}">{{ $fh }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reason <span class="text-muted">(optional)</span></label>
                        <textarea name="message" class="form-control" rows="2" placeholder="Reason for forwarding..."></textarea>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Finance Head Forward">
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-shadow btn-fhf-submit">
                        <i class="fas fa-exchange-alt"></i> Forward
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Select Finance Head Modal (Bootstrap native, for PA to Chairman / PA to GM) -->
<div class="modal fade" id="selectFinanceHeadModal" tabindex="-1" role="dialog" aria-labelledby="selectFinanceHeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectFinanceHeadModalLabel"><i class="fas fa-user-tie text-info"></i> Forward to Finance Head</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="selectFinanceHeadForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Finance Head Section <span class="text-danger">*</span></label>
                        <select name="finance_head" class="form-control" required>
                            <option value="">-- Select Finance Head --</option>
                            <option value="Finance Head Salem">Finance Head Salem</option>
                            <option value="Finance Head Chennai">Finance Head Chennai</option>
                            <option value="Finance Head Karaikal">Finance Head Karaikal</option>
                            <option value="Finance Head Pondy">Finance Head Pondy</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chairman's Instruction / Remarks <span class="text-muted">(optional)</span></label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Note the Chairman's instruction for selecting this Finance Head..."></textarea>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Select Finance Head">
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info btn-shadow btn-fh-submit">Forward to Finance Head</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Purchase Order Modal (Bootstrap native, triggered via data-toggle) -->
<div class="modal fade" id="purchaseOrderModal" tabindex="-1" role="dialog" aria-labelledby="purchaseOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseOrderModalLabel"><i class="fas fa-file-pdf text-primary"></i> Create Purchase Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="purchaseOrderForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Purchase Order File <span class="text-danger">*</span></label>
                        <input type="file" name="purchase_order" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Accepted: PDF, JPG, PNG — Max 5 MB</small>
                    </div>
                    <div class="form-group">
                        <label>Remarks <span class="text-muted">(optional)</span></label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Add any remarks..."></textarea>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Create Purchase Order">
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-shadow btn-po-submit">Upload Purchase Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Work Order Modal (Bootstrap native, triggered via data-toggle) -->
<div class="modal fade" id="workOrderModal" tabindex="-1" role="dialog" aria-labelledby="workOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderModalLabel"><i class="fas fa-file-alt text-secondary"></i> Create Work Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="workOrderForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Work Order File <span class="text-danger">*</span></label>
                        <input type="file" name="work_order" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-muted">Accepted: PDF, JPG, PNG — Max 5 MB</small>
                    </div>
                    <div class="form-group">
                        <label>Remarks <span class="text-muted">(optional)</span></label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Add any remarks..."></textarea>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Create Work Order">
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-shadow btn-wo-submit">Upload Work Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sanction Amount (for Finance Head) -->
<form class="modal-part" id="modal-sanction-part">
    @csrf
    <div class="form-group">
        <label>Sanctioned Amount <span style="color:Red">*</span></label>
        <div class="input-group">
            <input type="number" step="0.01" name="sanctioned_amount" class="form-control" placeholder="Enter sanctioned amount" required>
            <div class="input-group-append">
                <span class="input-group-text">{{ $doc->currency ?? 'INR' }}</span>
            </div>
        </div>
        <small class="text-muted">Requested Amount: {{ $doc->currency ?? '₹' }} {{ number_format($doc->amount ?? 0, 2) }}</small>
    </div>
    <div class="form-group">
        <label>Recommended Amount</label>
        <div class="input-group">
            <input type="number" step="0.01" name="recommended_amount" class="form-control" placeholder="Enter recommended amount">
            <div class="input-group-append">
                <span class="input-group-text">{{ $doc->currency ?? 'INR' }}</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Remarks</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
    </div>
    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
    <input type="hidden" name="status" value="Sanction">
</form>

<!-- Finance Details Modal (Bootstrap native, for Finance Head) -->
<div class="modal fade" id="financeDetailsModal" tabindex="-1" role="dialog" aria-labelledby="financeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="financeDetailsModalLabel"><i class="fas fa-rupee-sign text-success"></i> Create Finance Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="financeDetailsForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select name="payment_type" class="form-control" required>
                                    <option value="">-- Select Payment Type --</option>
                                    <option value="Full Payment">Full Payment</option>
                                    <option value="Partial Payment">Partial Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount Credited (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="paid_amount" class="form-control" placeholder="Enter amount credited" required>
                                @if($doc->amount)
                                    <small class="text-muted">Requested: ₹{{ number_format($doc->amount, 2) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Mode <span class="text-danger">*</span></label>
                                <select name="mode" class="form-control" required>
                                    <option value="">-- Select Mode --</option>
                                    <option value="NEFT">NEFT</option>
                                    <option value="RTGS">RTGS</option>
                                    <option value="IMPS">IMPS</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Online Transfer">Online Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reference / UTR Number <span class="text-danger">*</span></label>
                                <input type="text" name="payment_reference_no" class="form-control" placeholder="Transaction / UTR / Cheque No." required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks <span class="text-muted">(optional)</span></label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Any additional remarks..."></textarea>
                    </div>
                    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
                    <input type="hidden" name="status" value="Create Finance Details">
                </div>
                <div class="modal-footer bg-whitesmoke">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-shadow btn-fd-submit">Submit Finance Details</button>
                </div>
            </form>
        </div>
    </div>
</div>