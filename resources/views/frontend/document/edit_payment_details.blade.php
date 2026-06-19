@extends('frontend.frontend_master')

@section('content')

<style>
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
</style>

<div id="app">
    <div class="main-wrapper main-wrapper-1">

        @if(Auth::user()->role == 'SuperAdmin')
            @include('frontend.superadmin.body.header')
            @include('frontend.superadmin.body.sidebar')
        @else
            @include('frontend.admin.body.header')
            @include('frontend.admin.body.sidebar')
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>Payment Details Report</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="{{ route('admin_dashboard') }}">Dashboard</a></div>
                        <div class="breadcrumb-item">Payment Details</div>
                    </div>
                </div>

                <div class="section-body">
                    <h2 class="section-title">Edit Payment Details</h2>
                    <p class="section-lead">
                        Edit the payment records entered in the system.
                    </p>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Payment Details</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('update-payment-details', ['id' => $paymentDetails->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Payment Mode</label>
                                                    <select name="mode" class="form-control" required>
                                                        <option value="">Select Payment Mode</option>
                                                        <option value="Cash" {{ $paymentDetails->mode == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                        <option value="Cheque" {{ $paymentDetails->mode == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                                        <option value="NEFT/RTGS" {{ $paymentDetails->mode == 'NEFT/RTGS' ? 'selected' : '' }}>NEFT/RTGS</option>
                                                        <option value="UPI" {{ $paymentDetails->mode == 'UPI' ? 'selected' : '' }}>UPI</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Payment Reference No</label>
                                                    <input type="text" name="payment_reference_no" value="{{ $paymentDetails->payment_reference_no }}" class="form-control" placeholder="Reference Number">
                                                </div>
                                                <div class="form-group">
                                                    <label>Payment Date</label>
                                                    <input type="date" name="payment_date" value="{{ $paymentDetails->payment_date }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Payment Type</label><br>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_type" id="full_payment" value="Full Payment" {{ $paymentDetails->payment_type == 'Full Payment' ? 'checked' : '' }} required>
                                                        <label class="form-check-label" for="full_payment">Full Payment</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="payment_type" id="partial_payment" value="Partial Payment" {{ $paymentDetails->payment_type == 'Partial Payment' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="partial_payment">Partial Payment</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Expenditure ID</label>
                                                    <input type="text" name="expenditure_id" value="{{ $paymentDetails->expenditure_id }}" class="form-control" placeholder="Expenditure ID">
                                                </div>
                                                <div class="form-group">
                                                    <label>Paid Amount</label>
                                                    <input type="number" step="0.01" name="paid_amount" value="{{ $paymentDetails->paid_amount }}" class="form-control" placeholder="Amount Paid">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cheque Date Fields (Show if mode is Cheque) -->
                                        <div class="row cheque-date-fields" style="{{ $paymentDetails->mode == 'Cheque' ? '' : 'display: none;' }}">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Cheque Issue Date</label>
                                                    <input type="date" name="cheque_issue_date" value="{{ $paymentDetails->cheque_issue_date }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Cheque Cleared Date</label>
                                                    <input type="date" name="cheque_cleared_date" value="{{ $paymentDetails->cheque_cleared_date }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Payment/Bill/Refund Fields -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Bill Amount</label>
                                                    <input type="text" name="bill_amount" value="{{ $paymentDetails->bill_amount ?? '' }}" class="form-control" placeholder="Bill Amount">
                                                </div>
                                                <div class="form-group">
                                                    <label>Bill Submission Date</label>
                                                    <input type="date" name="bill_submission_date" value="{{ $paymentDetails->bill_submission_date ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Refund Amount</label>
                                                    <input type="text" name="refund_amount" value="{{ $paymentDetails->refund_amount ?? '' }}" class="form-control" placeholder="Refund Amount">
                                                </div>
                                                <div class="form-group">
                                                    <label>Refund Date</label>
                                                    <input type="date" name="refund_date" value="{{ $paymentDetails->refund_date ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                                                                
                                                <!-- Expenditure Category Dropdown -->
                                                <div class="form-group">
                                                    <label>Expenditure Category</label>
                                                    <select name="expenditure_category" class="form-control" id="expenditure-category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="Accredation and Ranking" {{ $paymentDetails->expenditure_category == 'Accredation and Ranking' ? 'selected' : '' }}>Accredation and Ranking</option>
                                                        <option value="Admission/ Advertisement" {{ $paymentDetails->expenditure_category == 'Admission/ Advertisement' ? 'selected' : '' }}>Admission/ Advertisement</option>
                                                        <option value="AIU Tournament" {{ $paymentDetails->expenditure_category == 'AIU Tournament' ? 'selected' : '' }}>AIU Tournament</option>
                                                        <option value="Alumni Meet - Chennai campus" {{ $paymentDetails->expenditure_category == 'Alumni Meet - Chennai campus' ? 'selected' : '' }}>Alumni Meet - Chennai campus</option>
                                                        <option value="Alumni Meet - Chennai; PB related" {{ $paymentDetails->expenditure_category == 'Alumni Meet - Chennai; PB related' ? 'selected' : '' }}>Alumni Meet - Chennai; PB related</option>
                                                        <option value="AMC/ Maintenance of Equipments" {{ $paymentDetails->expenditure_category == 'AMC/ Maintenance of Equipments' ? 'selected' : '' }}>AMC/ Maintenance of Equipments</option>
                                                        <option value="Building Maintenance" {{ $paymentDetails->expenditure_category == 'Building Maintenance' ? 'selected' : '' }}>Building Maintenance</option>
                                                        <option value="Building Rennovation" {{ $paymentDetails->expenditure_category == 'Building Rennovation' ? 'selected' : '' }}>Building Rennovation</option>
                                                        <option value="Chennai salary Contribution" {{ $paymentDetails->expenditure_category == 'Chennai salary Contribution' ? 'selected' : '' }}>Chennai salary Contribution</option>
                                                        <option value="CoE/PhD Contribution" {{ $paymentDetails->expenditure_category == 'CoE/PhD Contribution' ? 'selected' : '' }}>CoE/PhD Contribution</option>
                                                        <option value="Conference Registration Fee" {{ $paymentDetails->expenditure_category == 'Conference Registration Fee' ? 'selected' : '' }}>Conference Registration Fee</option>
                                                        <option value="Deepawali Exgratia" {{ $paymentDetails->expenditure_category == 'Deepawali Exgratia' ? 'selected' : '' }}>Deepawali Exgratia</option>
                                                        <option value="EPALM related" {{ $paymentDetails->expenditure_category == 'EPALM related' ? 'selected' : '' }}>EPALM related</option>
                                                        <option value="EPSI program" {{ $paymentDetails->expenditure_category == 'EPSI program' ? 'selected' : '' }}>EPSI program</option>
                                                        <option value="Flip book Subscription" {{ $paymentDetails->expenditure_category == 'Flip book Subscription' ? 'selected' : '' }}>Flip book Subscription</option>
                                                        <option value="Fuel-Vehicle" {{ $paymentDetails->expenditure_category == 'Fuel-Vehicle' ? 'selected' : '' }}>Fuel-Vehicle</option>
                                                        <option value="Fuel-Vehicle; Generator" {{ $paymentDetails->expenditure_category == 'Fuel-Vehicle; Generator' ? 'selected' : '' }}>Fuel-Vehicle; Generator</option>
                                                        <option value="Hackathan Expenses" {{ $paymentDetails->expenditure_category == 'Hackathan Expenses' ? 'selected' : '' }}>Hackathan Expenses</option>
                                                        <option value="HPE Conference Registration Fee" {{ $paymentDetails->expenditure_category == 'HPE Conference Registration Fee' ? 'selected' : '' }}>HPE Conference Registration Fee</option>
                                                        <option value="ICTIH'26 conference" {{ $paymentDetails->expenditure_category == "ICTIH'26 conference" ? 'selected' : '' }}>ICTIH'26 conference</option>
                                                        <option value="ICTIH'26 conference Expenses" {{ $paymentDetails->expenditure_category == "ICTIH'26 conference Expenses" ? 'selected' : '' }}>ICTIH'26 conference Expenses</option>
                                                        <option value="Ideathon Expenses" {{ $paymentDetails->expenditure_category == 'Ideathon Expenses' ? 'selected' : '' }}>Ideathon Expenses</option>
                                                        <option value="ISR" {{ $paymentDetails->expenditure_category == 'ISR' ? 'selected' : '' }}>ISR</option>
                                                        <option value="Land purchase" {{ $paymentDetails->expenditure_category == 'Land purchase' ? 'selected' : '' }}>Land purchase</option>
                                                        <option value="Legal Fee" {{ $paymentDetails->expenditure_category == 'Legal Fee' ? 'selected' : '' }}>Legal Fee</option>
                                                        <option value="Marathon Expenses; VINSPORTS-25" {{ $paymentDetails->expenditure_category == 'Marathon Expenses; VINSPORTS-25' ? 'selected' : '' }}>Marathon Expenses; VINSPORTS-25</option>
                                                        <option value="Meeting Expenses" {{ $paymentDetails->expenditure_category == 'Meeting Expenses' ? 'selected' : '' }}>Meeting Expenses</option>
                                                        <option value="Miscellaneous Expenses" {{ $paymentDetails->expenditure_category == 'Miscellaneous Expenses' ? 'selected' : '' }}>Miscellaneous Expenses</option>
                                                        <option value="Mobile & Telephone" {{ $paymentDetails->expenditure_category == 'Mobile & Telephone' ? 'selected' : '' }}>Mobile & Telephone</option>
                                                        <option value="NASCENT MR 2025 Conference Registration Fee" {{ $paymentDetails->expenditure_category == 'NASCENT MR 2025 Conference Registration Fee' ? 'selected' : '' }}>NASCENT MR 2025 Conference Registration Fee</option>
                                                        <option value="Other Income" {{ $paymentDetails->expenditure_category == 'Other Income' ? 'selected' : '' }}>Other Income</option>
                                                        <option value="Patent related" {{ $paymentDetails->expenditure_category == 'Patent related' ? 'selected' : '' }}>Patent related</option>
                                                        <option value="PB related" {{ $paymentDetails->expenditure_category == 'PB related' ? 'selected' : '' }}>PB related</option>
                                                        <option value="Printing and Stationaries" {{ $paymentDetails->expenditure_category == 'Printing and Stationaries' ? 'selected' : '' }}>Printing and Stationaries</option>
                                                        <option value="Purchase of Computer and its accessories" {{ $paymentDetails->expenditure_category == 'Purchase of Computer and its accessories' ? 'selected' : '' }}>Purchase of Computer and its accessories</option>
                                                        <option value="Purchase of Container" {{ $paymentDetails->expenditure_category == 'Purchase of Container' ? 'selected' : '' }}>Purchase of Container</option>
                                                        <option value="Purchase of Equipments" {{ $paymentDetails->expenditure_category == 'Purchase of Equipments' ? 'selected' : '' }}>Purchase of Equipments</option>
                                                        <option value="Purchase of Furniture" {{ $paymentDetails->expenditure_category == 'Purchase of Furniture' ? 'selected' : '' }}>Purchase of Furniture</option>
                                                        <option value="Purchase of Licenses including softwares" {{ $paymentDetails->expenditure_category == 'Purchase of Licenses including softwares' ? 'selected' : '' }}>Purchase of Licenses including softwares</option>
                                                        <option value="Purchase of Xerox" {{ $paymentDetails->expenditure_category == 'Purchase of Xerox' ? 'selected' : '' }}>Purchase of Xerox</option>
                                                        <option value="Refunds" {{ $paymentDetails->expenditure_category == 'Refunds' ? 'selected' : '' }}>Refunds</option>
                                                        <option value="Research Day Expenses" {{ $paymentDetails->expenditure_category == 'Research Day Expenses' ? 'selected' : '' }}>Research Day Expenses</option>
                                                        <option value="Research Incentives -15th Phase" {{ $paymentDetails->expenditure_category == 'Research Incentives -15th Phase' ? 'selected' : '' }}>Research Incentives -15th Phase</option>
                                                        <option value="Research Incentives" {{ $paymentDetails->expenditure_category == 'Research Incentives' ? 'selected' : '' }}>Research Incentives</option>
                                                        <option value="SALEM Electricity" {{ $paymentDetails->expenditure_category == 'SALEM Electricity' ? 'selected' : '' }}>SALEM Electricity</option>
                                                        <option value="Salem Salary & Other Benefits" {{ $paymentDetails->expenditure_category == 'Salem Salary & Other Benefits' ? 'selected' : '' }}>Salem Salary & Other Benefits</option>
                                                        <option value="Seed Money" {{ $paymentDetails->expenditure_category == 'Seed Money' ? 'selected' : '' }}>Seed Money</option>
                                                        <option value="Sponser to Institute for conducting National conferences" {{ $paymentDetails->expenditure_category == 'Sponser to Institute for conducting National conferences' ? 'selected' : '' }}>Sponser to Institute for conducting National conferences</option>
                                                        <option value="Sponsorship" {{ $paymentDetails->expenditure_category == 'Sponsorship' ? 'selected' : '' }}>Sponsorship</option>
                                                        <option value="Students Club festival - Salem" {{ $paymentDetails->expenditure_category == 'Students Club festival - Salem' ? 'selected' : '' }}>Students Club festival - Salem</option>
                                                        <option value="TA/DA Expenses" {{ $paymentDetails->expenditure_category == 'TA/DA Expenses' ? 'selected' : '' }}>TA/DA Expenses</option>
                                                        <option value="TDS Payment" {{ $paymentDetails->expenditure_category == 'TDS Payment' ? 'selected' : '' }}>TDS Payment</option>
                                                        <option value="Tea Expenses" {{ $paymentDetails->expenditure_category == 'Tea Expenses' ? 'selected' : '' }}>Tea Expenses</option>
                                                        <option value="University Fencing work" {{ $paymentDetails->expenditure_category == 'University Fencing work' ? 'selected' : '' }}>University Fencing work</option>
                                                        <option value="Vehicle EMI" {{ $paymentDetails->expenditure_category == 'Vehicle EMI' ? 'selected' : '' }}>Vehicle EMI</option>
                                                        <option value="Vehicle Insurance" {{ $paymentDetails->expenditure_category == 'Vehicle Insurance' ? 'selected' : '' }}>Vehicle Insurance</option>
                                                        <option value="Vehicle Maintenance" {{ $paymentDetails->expenditure_category == 'Vehicle Maintenance' ? 'selected' : '' }}>Vehicle Maintenance</option>
                                                        <option value="Vehicle Rent" {{ $paymentDetails->expenditure_category == 'Vehicle Rent' ? 'selected' : '' }}>Vehicle Rent</option>
                                                        <option value="VINSPORTS-25" {{ $paymentDetails->expenditure_category == 'VINSPORTS-25' ? 'selected' : '' }}>VINSPORTS-25</option>
                                                        <option value="V-Lead Conference Expenses - HPE" {{ $paymentDetails->expenditure_category == 'V-Lead Conference Expenses - HPE' ? 'selected' : '' }}>V-Lead Conference Expenses - HPE</option>
                                                        <option value="VMKVASC Expenses" {{ $paymentDetails->expenditure_category == 'VMKVASC Expenses' ? 'selected' : '' }}>VMKVASC Expenses</option>
                                                        <!-- Always show "Other" option -->
                                                        <option value="other">Other (Please specify)</option>
                                                    </select>
                                                </div>                                                
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Custom category input (initially hidden) -->
                                                <div class="form-group custom-category-group" style="display: none;">
                                                    <label>Specify Other Category</label>
                                                    <input type="text" name="custom_expenditure_category" class="form-control" 
                                                        placeholder="Enter custom expenditure category" 
                                                        value="{{ $paymentDetails->expenditure_category }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_tds_applicable" id="is_tds_applicable" {{ $paymentDetails->tds_amount > 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_tds_applicable">TDS Applicable</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group tds-amount-group" style="{{ $paymentDetails->tds_amount > 0 ? '' : 'display: none;' }}">
                                                    <label>TDS Amount</label>
                                                    <input type="number" step="0.01" name="tds_amount" value="{{ $paymentDetails->tds_amount ?? 0 }}" class="form-control" placeholder="Enter TDS amount">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Remarks</label>
                                                    <textarea name="remarks" class="form-control" placeholder="Any additional remarks">{{ $paymentDetails->remarks }}</textarea>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="form-group">
                                            <input type="hidden" name="status" value="Paid">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Payment</button>
                                    </form>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const tdsCheckbox = document.getElementById('is_tds_applicable');
                                            const tdsAmountGroup = document.querySelector('.tds-amount-group');
                                            const tdsAmountInput = document.querySelector('input[name="tds_amount"]');
                                            const paymentModeSelect = document.querySelector('select[name="mode"]');
                                            const chequeDateFields = document.querySelector('.cheque-date-fields');
                                            const expenditureCategorySelect = document.getElementById('expenditure-category');
                                            const customCategoryGroup = document.querySelector('.custom-category-group');
                                            const customCategoryInput = document.querySelector('input[name="custom_expenditure_category"]');
                                            
                                            // Initialize TDS based on current value
                                            if(parseFloat(tdsAmountInput.value) > 0) {
                                                tdsAmountGroup.style.display = 'block';
                                            }
                                            
                                            tdsCheckbox.addEventListener('change', function() {
                                                if(this.checked) {
                                                    tdsAmountGroup.style.display = 'block';
                                                    if(!parseFloat(tdsAmountInput.value)) {
                                                        tdsAmountInput.value = '';
                                                    }
                                                } else {
                                                    tdsAmountGroup.style.display = 'none';
                                                    tdsAmountInput.value = '0';
                                                }
                                            });
                                            
                                            // Show/Hide cheque date fields based on payment mode
                                            paymentModeSelect.addEventListener('change', function() {
                                                if (this.value === 'Cheque') {
                                                    chequeDateFields.style.display = 'flex';
                                                } else {
                                                    chequeDateFields.style.display = 'none';
                                                }
                                            });
                                            
                                            // Initialize expenditure category dropdown
                                            const storedCategory = "{{ $paymentDetails->expenditure_category }}";
                                            const categoryOptions = expenditureCategorySelect.options;
                                            let foundPredefined = false;
                                            
                                            // Check if stored value matches any predefined option
                                            for (let i = 0; i < categoryOptions.length; i++) {
                                                if (categoryOptions[i].value === storedCategory) {
                                                    foundPredefined = true;
                                                    expenditureCategorySelect.value = storedCategory;
                                                    break;
                                                }
                                            }
                                            
                                            // If not found in predefined options, select "other" and show custom input
                                            if (!foundPredefined && storedCategory) {
                                                expenditureCategorySelect.value = 'other';
                                                customCategoryGroup.style.display = 'block';
                                                customCategoryInput.value = storedCategory;
                                            }
                                            
                                            // Show/Hide custom category input based on selection
                                            expenditureCategorySelect.addEventListener('change', function() {
                                                if (this.value === 'other') {
                                                    customCategoryGroup.style.display = 'block';
                                                    // Keep existing custom value if any
                                                    if (!customCategoryInput.value && storedCategory && !foundPredefined) {
                                                        customCategoryInput.value = storedCategory;
                                                    }
                                                } else {
                                                    customCategoryGroup.style.display = 'none';
                                                    // Don't clear the value, just hide the field
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @include('frontend.body.footer')
    </div>
</div>
@endsection