@extends('layouts.app')

@section('title', 'Call Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0">
                <i class="bi bi-graph-up text-primary me-2"></i>
                Call Dashboard
            </h1>
            <p class="text-muted">Year {{ $currentYear }} - Current Period Analysis</p>
        </div>
    </div>

    <!-- Chart Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Monthly Overview - {{ $currentYear }}</h5>
                </div>
                <div class="card-body">
                    <div id="callChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historical Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Historical Data - All Periods</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="historicalTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="sortable" data-column="0" style="cursor: pointer;">
                                        Year-Month
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-column="1" style="cursor: pointer;">
                                        Number of Calls
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-column="2" style="cursor: pointer;">
                                        Total Cost
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allTimeData as $row)
                                <tr>
                                    <td data-sort="{{ $row->month_year }}">
                                        <a href="{{ route('calls.monthly', $row->month_year) }}"
                                        class="text-decoration-none fw-semibold">
                                            {{ $row->display_month }}
                                            <i class="bi bi-arrow-right-circle ms-1"></i>
                                        </a>
                                    </td>
                                    <td data-sort="{{ $row->num_calls }}">
                                        <i class="bi bi-telephone text-primary me-2"></i>
                                        {{ number_format($row->num_calls) }}
                                    </td>
                                    <td data-sort="{{ $row->total_cost }}">
                                        <i class="bi bi-currency-exchange text-success me-2"></i>
                                        R {{ number_format($row->total_cost, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr class="no-data">
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No call data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
<script>
    initCallChart({
        months: @json($chartData['months']),
        numCalls: @json($chartData['numCalls']),
        totalCost: @json($chartData['totalCost'])
    });
</script>
@endpush
@endsection
