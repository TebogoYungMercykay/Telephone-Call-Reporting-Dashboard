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
<script>
    // Initialize ECharts
    var chartDom = document.getElementById('callChart');
    var myChart = echarts.init(chartDom);

    var option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
            data: ['Number of Calls', 'Total Cost (R)']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: @json($chartData['months'])
        },
        yAxis: [
            {
                type: 'value',
                name: 'Number of Calls',
                position: 'left',
                axisLabel: {
                    formatter: '{value}'
                }
            },
            {
                type: 'value',
                name: 'Total Cost (R)',
                position: 'right',
                axisLabel: {
                    formatter: 'R{value}'
                }
            }
        ],
        series: [
            {
                name: 'Number of Calls',
                type: 'bar',
                data: @json($chartData['numCalls']),
                itemStyle: {
                    color: '#0d6efd'
                }
            },
            {
                name: 'Total Cost (R)',
                type: 'bar',
                yAxisIndex: 1,
                data: @json($chartData['totalCost']),
                itemStyle: {
                    color: '#198754'
                }
            }
        ]
    };

    myChart.setOption(option);

    // Make chart responsive
    window.addEventListener('resize', function() {
        myChart.resize();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('historicalTable');
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
