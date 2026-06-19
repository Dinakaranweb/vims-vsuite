<div class="row">
    {{-- NEW --}}
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('staffpayment.new') }}">
            @php $active = Request::routeIs('staffpayment.new'); @endphp
            <div class="card card-statistic-1 {{ $active ? 'bg-primary text-white' : '' }}">
                <div class="card-icon {{ $active ? 'bg-white' : 'bg-primary' }}">
                    <i class="far fa-user" style="{{ $active ? 'color:#2a6d98;' : 'color:#fff;' }}"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $active ? 'color:#fff;' : '' }}">New</h4>
                    </div>
                    <div class="card-body" style="{{ $active ? 'color:#fff;' : '' }}">
                        {{ $new_reqs ?? 0 }}
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- IN PROGRESS --}}
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('staffpayment.in_progress') }}">
            @php $active = Request::routeIs('staffpayment.in_progress'); @endphp
            <div class="card card-statistic-1 {{ $active ? 'bg-warning text-white' : '' }}">
                <div class="card-icon {{ $active ? 'bg-white' : 'bg-warning' }}">
                    <i class="far fa-newspaper" style="{{ $active ? 'color:#8a6d1c;' : 'color:#fff;' }}"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $active ? 'color:#fff;' : '' }}">In Progress</h4>
                    </div>
                    <div class="card-body" style="{{ $active ? 'color:#fff;' : '' }}">
                        {{ $inprogress_reqs ?? 0 }}
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- ADVANCE --}}
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('staffpayment.advance') }}">
            @php $active = Request::routeIs('staffpayment.advance'); @endphp
            <div class="card card-statistic-1 {{ $active ? 'bg-info text-white' : '' }}">
                <div class="card-icon {{ $active ? 'bg-white' : 'bg-info' }}">
                    <i class="far fa-newspaper" style="{{ $active ? 'color:#0c5460;' : 'color:#fff;' }}"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $active ? 'color:#fff;' : '' }}">Advance</h4>
                    </div>
                    <div class="card-body" style="{{ $active ? 'color:#fff;' : '' }}">
                        {{ $advance_reqs ?? 0 }}
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- FULL --}}
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('staffpayment.full') }}">
            @php $active = Request::routeIs('staffpayment.full'); @endphp
            <div class="card card-statistic-1 {{ $active ? 'bg-dark text-white' : '' }}">
                <div class="card-icon {{ $active ? 'bg-white' : 'bg-dark' }}">
                    <i class="far fa-newspaper" style="{{ $active ? 'color:#000;' : 'color:#fff;' }}"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $active ? 'color:#fff;' : '' }}">Full</h4>
                    </div>
                    <div class="card-body" style="{{ $active ? 'color:#fff;' : '' }}">
                        {{ $full_reqs ?? 0 }}
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row justify-content-center" style="margin-top:10px;">
    {{-- ON HOLD --}}
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('staffpayment.hold') }}">
            @php $active = Request::routeIs('staffpayment.hold'); @endphp
            <div class="card card-statistic-1 {{ $active ? 'bg-danger text-white' : '' }}">
                <div class="card-icon {{ $active ? 'bg-white' : 'bg-danger' }}">
                    <i class="fas fa-circle" style="{{ $active ? 'color:#8a1f1c;' : 'color:#fff;' }}"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $active ? 'color:#fff;' : '' }}">On Hold</h4>
                    </div>
                    <div class="card-body" style="{{ $active ? 'color:#fff;' : '' }}">
                        {{ $hold_reqs ?? 0 }}
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- COMPLETED --}}
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ route('staffpayment.completed') }}">
            @php $active = Request::routeIs('staffpayment.completed'); @endphp
            <div class="card card-statistic-1 {{ $active ? 'bg-success text-white' : '' }}">
                <div class="card-icon {{ $active ? 'bg-white' : 'bg-success' }}">
                    <i class="far fa-file" style="{{ $active ? 'color:#155724;' : 'color:#fff;' }}"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4 style="{{ $active ? 'color:#fff;' : '' }}">Completed</h4>
                    </div>
                    <div class="card-body" style="{{ $active ? 'color:#fff;' : '' }}">
                        {{ $completed_reqs ?? 0 }}
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
