@extends('frontend.frontend_master')

@section('content')

<style>

    .approval-flowchart-container {
        overflow-x: auto;
        padding: 10px;
        max-width:500px;
    }

    .approval-flowchart {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
    }

    .step {
        text-align: center;
        min-width: 250px;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        background-color: #fff;
        position: relative;
    }

    .step .vc{
        color: red;
    }

    .step .provc{
        color: green;
    }

    .step .registrar{
        color: #19b91f;
    }

    .icon {
        font-size: 24px;
        color: #ff7a00;
    }

    p {
        font-weight: bold;
        margin: 5px 0;
    }

    /* Connector styling */
    .connector {
        font-size: 24px;
        color: #ff7a00;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .approval-flowchart {
            gap: 10px;
        }
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .col-summary {
        min-width: 0;
    }
    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
        }
    }

    .status-filter-bar {
        margin-bottom: 20px;
    }

    .status-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .status-filter-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px 15px;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        text-decoration: none;
        color: inherit;
        transition: all 0.3s ease;
        min-width: 80px;
        position: relative;
        overflow: hidden;
    }

    .status-filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        text-decoration: none;
        color: inherit;
    }

    /* Color classes for each status */
    .status-filter-btn.new {
        border-left-color: #6c757d;
    }
    .status-filter-btn.new.active {
        background-color: #6c757d;
        color: white;
        border-color: #6c757d;
    }

    .status-filter-btn.approved {
        border-left-color: #28a745;
    }
    .status-filter-btn.approved.active {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .status-filter-btn.approved-principle {
        border-left-color: #007bff;
    }
    .status-filter-btn.approved-principle.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .status-filter-btn.discussion {
        border-left-color: #17a2b8;
    }
    .status-filter-btn.discussion.active {
        background-color: #17a2b8;
        color: white;
        border-color: #17a2b8;
    }

    .status-filter-btn.forwarded {
        border-left-color: #6f42c1;
    }
    .status-filter-btn.forwarded.active {
        background-color: #6f42c1;
        color: white;
        border-color: #6f42c1;
    }

    .status-filter-btn.commented {
        border-left-color: #fd7e14;
    }
    .status-filter-btn.commented.active {
        background-color: #fd7e14;
        color: white;
        border-color: #fd7e14;
    }

    .status-filter-btn.no-action {
        border-left-color: #20c997;
    }
    .status-filter-btn.no-action.active {
        background-color: #20c997;
        color: white;
        border-color: #20c997;
    }

    .status-filter-btn.hold {
        border-left-color: #343a40;
    }
    .status-filter-btn.hold.active {
        background-color: #343a40;
        color: white;
        border-color: #343a40;
    }

    .status-filter-btn.rejected {
        border-left-color: #dc3545;
    }
    .status-filter-btn.rejected.active {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .status-filter-btn.pending {
        border-left-color: #ffc107;
    }
    .status-filter-btn.pending.active {
        background-color: #ffc107;
        color: #212529;
        border-color: #ffc107;
    }

    .status-filter-btn.total {
        background: #2d3748;
        color: white;
        border-color: #2d3748;
        border-left-color: #1a202c;
    }

    /* Active state styles */
    .status-filter-btn.active {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-weight: 600;
    }

    .filter-count {
        font-size: 1.125rem;
        font-weight: bold;
        margin-bottom: 2px;
    }

    .filter-label {
        font-size: 0.75rem;
        opacity: 0.9;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .status-filters {
            gap: 6px;
        }
        
        .status-filter-btn {
            padding: 8px 12px;
            min-width: 70px;
        }
        
        .filter-count {
            font-size: 1rem;
        }
        
        .filter-label {
            font-size: 0.7rem;
        }
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
            <h1>Hold Documents</h1>
          </div>
          
          @include('frontend.document.vc.status-bar')

          <div class="section-body">
            <h2 class="section-title">Hold Documents</h2>
            <p class="section-lead">
              Following Documents are put on hold by you.
            </p>

            @include('frontend.document.vc.document-table')

          </div>
        </section>
      </div>
        @include('frontend.body.footer')
        </div>
  </div>
  <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                // The page was restored from the bfcache, so reload it
                window.location.reload();
            }
        });
  </script>
@endsection