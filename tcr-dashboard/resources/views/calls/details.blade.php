@extends('layouts.app')

@section('title', 'Call Details - ' . $extensionName)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('calls.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('calls.monthly', $yearMonth) }}">{{ $displayMonth }}</a>
            </li>
            <li class="breadcrumb-item active">{{ $extensionName }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h2 mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i>
                Call Details
            </h1>
            <p class="text-muted">{{ $displayMonth }} / {{ $extensionName }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('calls.monthly', $yearMonth) }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Back to Monthly Summary
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Extension</h6>
                    <h5 class="mb-0">{{ $extensionName }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card calls">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Calls</h6>
                    <h4 class="mb-0 text-primary">{{ number_format($totalCalls) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card cost">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Cost</h6>
                    <h4 class="mb-0 text-success">R {{ number_format($totalCost, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Call Details Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Individual Call Records</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="callDetailsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="sortable" data-column="0" style="cursor: pointer;">
                                        Extension
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-column="1" style="cursor: pointer;">
                                        Destination
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-column="2" style="cursor: pointer;">
                                        Time of Call
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-column="3" style="cursor: pointer;">
                                        Duration
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-column="4" style="cursor: pointer;">
                                        Cost
                                        <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($callDetails as $call)
                                <tr>
                                    <td data-sort="{{ $call->CallFrom }}">
                                        <span class="fw-semibold">{{ $call->CallFrom }}</span>
                                    </td>
                                    <td data-sort="{{ $call->CallTo }}">
                                        {{ $call->CallTo }}
                                    </td>
                                    <td data-sort="{{ $call->CallTime->timestamp }}">
                                        <small>{{ $call->CallTime->format('Y-m-d H:i:s') }}</small>
                                    </td>
                                    <td data-sort="{{ $call->Duration }}">
                                        {{ $call->Duration }}
                                    </td>
                                    <td data-sort="{{ $call->Cost }}">
                                        <span class="badge bg-success">R {{ number_format($call->Cost, 2) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr class="no-data">
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No calls found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($callDetails->hasPages())
                <div class="card-footer bg-white">
                    {{ $callDetails->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('callDetailsTable');
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
