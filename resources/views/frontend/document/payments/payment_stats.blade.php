@if(Auth::user()->department == 'Students Welfare')
    <div class="row justify-content-center">

        {{-- NEW --}}
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('payment.new') }}">
                <div class="card card-statistic-1 
                    {{ $activeDropdown=='new_doc' ? 'bg-primary text-white' : '' }}">

                    <div class="card-icon 
                        {{ $activeDropdown=='new_doc' ? 'bg-white' : 'bg-primary' }}">
                        <i class="far fa-user"
                           style="{{ $activeDropdown=='new_doc' ? 'color:#2a6d98;' : 'color:#fff;' }}"></i>
                    </div>

                    <div class="card-wrap">
                        <div class="card-header">
                            <h4 style="{{ $activeDropdown=='new_doc' ? 'color:#fff;' : '' }}">New</h4>
                        </div>
                        <div class="card-body" style="{{ $activeDropdown=='new_doc' ? 'color:#fff;' : '' }}">
                            {{ $stats->new_docs ?? 0 }}
                        </div>
                    </div>

                </div>
            </a>
        </div>


        {{-- IN PROGRESS --}}
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('payment.in_progress') }}">
                <div class="card card-statistic-1 
                    {{ $activeDropdown=='inprogress_doc' ? 'bg-warning text-white' : '' }}">

                    <div class="card-icon 
                        {{ $activeDropdown=='inprogress_doc' ? 'bg-white' : 'bg-warning' }}">
                        <i class="far fa-newspaper"
                           style="{{ $activeDropdown=='inprogress_doc' ? 'color:#8a6d1c;' : 'color:#fff;' }}"></i>
                    </div>

                    <div class="card-wrap">
                        <div class="card-header">
                            <h4 style="{{ $activeDropdown=='inprogress_doc' ? 'color:#fff;' : '' }}">
                                Assigned
                            </h4>
                        </div>
                        <div class="card-body" style="{{ $activeDropdown=='inprogress_doc' ? 'color:#fff;' : '' }}">
                            {{ $stats->inProgress_docs ?? 0 }}
                        </div>
                    </div>

                </div>
            </a>
        </div>


        {{-- ADVANCE --}}
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('payment.advance') }}">
                <div class="card card-statistic-1 
                    {{ $activeDropdown=='advance_payment_doc' ? 'bg-info text-white' : '' }}">

                    <div class="card-icon 
                        {{ $activeDropdown=='advance_payment_doc' ? 'bg-white' : 'bg-info' }}">
                        <i class="far fa-newspaper"
                           style="{{ $activeDropdown=='advance_payment_doc' ? 'color:#0c5460;' : 'color:#fff;' }}"></i>
                    </div>

                    <div class="card-wrap">
                        <div class="card-header">
                            <h4 style="{{ $activeDropdown=='advance_payment_doc' ? 'color:#fff;' : '' }}">
                                Advance Payments
                            </h4>
                        </div>
                        <div class="card-body" style="{{ $activeDropdown=='advance_payment_doc' ? 'color:#fff;' : '' }}">
                            {{ $stats->advance_payments ?? 0 }}
                        </div>
                    </div>

                </div>
            </a>
        </div>


        {{-- FULL --}}
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('payment.full') }}">
                <div class="card card-statistic-1 
                    {{ $activeDropdown=='full_payment_doc' ? 'bg-dark text-white' : '' }}">

                    <div class="card-icon 
                        {{ $activeDropdown=='full_payment_doc' ? 'bg-white' : 'bg-dark' }}">
                        <i class="far fa-newspaper"
                           style="{{ $activeDropdown=='full_payment_doc' ? 'color:#000;' : 'color:#fff;' }}"></i>
                    </div>

                    <div class="card-wrap">
                        <div class="card-header">
                            <h4 style="{{ $activeDropdown=='full_payment_doc' ? 'color:#fff;' : '' }}">
                                Full Payments
                            </h4>
                        </div>
                        <div class="card-body" style="{{ $activeDropdown=='full_payment_doc' ? 'color:#fff;' : '' }}">
                            {{ $stats->full_payments ?? 0 }}
                        </div>
                    </div>

                </div>
            </a>
        </div>

    </div>



    <div class="row justify-content-center">

        {{-- ON HOLD --}}
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('payment.hold') }}">
                <div class="card card-statistic-1 
                    {{ $activeDropdown=='hold_doc' ? 'bg-danger text-white' : '' }}">

                    <div class="card-icon 
                        {{ $activeDropdown=='hold_doc' ? 'bg-white' : 'bg-danger' }}">
                        <i class="fas fa-circle"
                           style="{{ $activeDropdown=='hold_doc' ? 'color:#8a1f1c;' : 'color:#fff;' }}"></i>
                    </div>

                    <div class="card-wrap">
                        <div class="card-header">
                            <h4 style="{{ $activeDropdown=='hold_doc' ? 'color:#fff;' : '' }}">
                                On Hold
                            </h4>
                        </div>
                        <div class="card-body" style="{{ $activeDropdown=='hold_doc' ? 'color:#fff;' : '' }}">
                            {{ $stats->hold_docs ?? 0 }}
                        </div>
                    </div>

                </div>
            </a>
        </div>


        {{-- COMPLETED --}}
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('payment.completed') }}">
                <div class="card card-statistic-1 
                    {{ $activeDropdown=='completed_doc' ? 'bg-success text-white' : '' }}">

                    <div class="card-icon 
                        {{ $activeDropdown=='completed_doc' ? 'bg-white' : 'bg-success' }}">
                        <i class="far fa-file"
                           style="{{ $activeDropdown=='completed_doc' ? 'color:#155724;' : 'color:#fff;' }}"></i>
                    </div>

                    <div class="card-wrap">
                        <div class="card-header">
                            <h4 style="{{ $activeDropdown=='completed_doc' ? 'color:#fff;' : '' }}">
                                Completed
                            </h4>
                        </div>
                        <div class="card-body" style="{{ $activeDropdown=='completed_doc' ? 'color:#fff;' : '' }}">
                            {{ $stats->completed_docs ?? 0 }}
                        </div>
                    </div>

                </div>
            </a>
        </div>

    </div>
@endif
