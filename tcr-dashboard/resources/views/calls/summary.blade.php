@extends('layouts.app')

@section('title', 'Monthly Summary - ' . $displayMonth)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('calls.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">{{ $displayMonth }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0">
                <i class="bi bi-calendar-month text-primary me-2"></i>
                Monthly Summary
            </h1>
            <p class="text-muted">{{ $displayMonth }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('calls.dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card stat-card calls">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Calls</h6>
                            <h2 class="mb-0 text-primary">{{ number_format($totalCalls) }}</h2>
                        </div>
                        <div class="text-primary" style="font-size: 3rem; opacity: 0.2;">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card cost">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Cost</h6>
                            <h2 class="mb-0 text-success">R {{ number_format($totalCost, 2) }}</h2>
                        </div>
                        <div class="text-success" style="font-size: 3rem; opacity: 0.2;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Extension Summary Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Calls by Extension</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="extensionTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="sortable" data-column="0" style="cursor: pointer;">
                                        Extension
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
                                @forelse($extensionSummary as $row)
                                <tr>
                                    <td data-sort="{{ $row->CallFrom }}">
                                        <a href="{{ route('calls.details', [$yearMonth, $row->extension]) }}"
                                        class="text-decoration-none fw-semibold">
                                            {{ $row->CallFrom }}
                                            <i class="bi bi-arrow-right-circle ms-1"></i>
                                        </a>
                                    </td>
                                    <td data-sort="{{ $row->num_calls }}">
                                        {{ number_format($row->num_calls) }}
                                    </td>
                                    <td data-sort="{{ $row->total_cost }}">
                                        R {{ number_format($row->total_cost, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr class="no-data">
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No calls found for this month
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('extensionTable');
    if (!table) return;

    const headers = table.querySelectorAll('.sortable');

    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = parseInt(this.dataset.column);
            const isAsc = this.classList.contains('asc');
            const newOrder = isAsc ? 'desc' : 'asc';

            // Remove sorting classes from all headers
            headers.forEach(h => {
                h.classList.remove('asc', 'desc');
                const icon = h.querySelector('.sort-icon');
                icon.className = 'bi bi-arrow-down-up ms-1 sort-icon';
            });

            // Add sorting class to current header
            this.classList.add(newOrder);
            const icon = this.querySelector('.sort-icon');
            icon.className = newOrder === 'asc'
                ? 'bi bi-sort-up ms-1 sort-icon'
                : 'bi bi-sort-down ms-1 sort-icon';

            // Sort the table
            sortTable(column, newOrder);
        });
    });

    function sortTable(column, order) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr:not(.no-data)'));

        if (rows.length === 0) return;

        rows.sort((a, b) => {
            const aValue = a.cells[column].dataset.sort;
            const bValue = b.cells[column].dataset.sort;

            const aNum = parseFloat(aValue);
            const bNum = parseFloat(bValue);

            let comparison = 0;

            if (!isNaN(aNum) && !isNaN(bNum)) {
                comparison = aNum - bNum;
            } else {
                comparison = aValue.localeCompare(bValue);
            }

            return order === 'asc' ? comparison : -comparison;
        });

        rows.forEach(row => tbody.appendChild(row));
    }
});
</script>

<style>
    .sortable {
        user-select: none;
        transition: background-color 0.2s;
    }

    .sortable:hover {
        background-color: #e9ecef;
    }

    .sort-icon {
        opacity: 0.3;
        transition: opacity 0.2s;
    }

    .sortable:hover .sort-icon {
        opacity: 0.6;
    }

    .sortable.asc .sort-icon,
    .sortable.desc .sort-icon {
        opacity: 1;
    }
</style>
@endpush
@endsection
