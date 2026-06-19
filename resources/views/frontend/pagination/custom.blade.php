<!-- pagination.blade.php (partial view) -->

@if ($paginator->hasPages())
    <style>
        .custom-pagination-container {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .pagination-form label {
            font-weight: 500;
            margin-right: 0.5rem;
        }

        .pagination-form .form-control {
            max-width: 80px;
        }

        .pagination-form .btn {
            min-width: 50px;
        }

        .pagination-sm .page-link {
            padding: 0.3rem 0.65rem;
            font-size: 0.875rem;
        }

        @media (max-width: 767.98px) {
            .pagination-form {
                justify-content: center;
                margin-bottom: 1rem;
            }

            .pagination-nav {
                justify-content: center !important;
            }
        }
    </style>

    <div class="custom-pagination-container mt-3">
        <div class="row align-items-center">
            {{-- Left: Page Jump Form --}}
            <div class="col-md-6 d-flex pagination-form">
                <form method="GET" action="{{ url()->current() }}" class="form-inline d-flex align-items-center">
                    {{-- Preserve other query parameters --}}
                    @foreach(request()->except('page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <label for="pageInput">Go to page:</label>
                    <div class="input-group input-group-sm">
                        <input
                            type="number"
                            name="page"
                            id="pageInput"
                            class="form-control"
                            min="1"
                            max="{{ $paginator->lastPage() }}"
                            placeholder="{{ $paginator->currentPage() }}"
                            required
                        >
                        <div class="input-group-append" style="margin-left: 0.5rem;">
                            <button type="submit" class="btn btn-primary">Go</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Right: Pagination Links --}}
            <div class="col-md-6">
                <nav aria-label="Pagination">
                    <ul class="pagination pagination-sm justify-content-md-end pagination-nav mb-0">
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">Previous</a></li>
                        @endif

                        {{-- Page Numbers --}}
                        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                            @if ($i == $paginator->currentPage())
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @elseif (
                                $i == 1 ||
                                $i == $paginator->lastPage() ||
                                ($i >= $paginator->currentPage() - 1 && $i <= $paginator->currentPage() + 1)
                            )
                                <li class="page-item"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                            @elseif ($i == $paginator->currentPage() - 2 || $i == $paginator->currentPage() + 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endif
