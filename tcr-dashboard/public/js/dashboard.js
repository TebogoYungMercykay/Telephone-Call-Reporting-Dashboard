// tcr-dashboard/resources/views/calls/dashboard.blade.php scripts

// Initialize ECharts
function initCallChart(chartData) {
    var chartDom = document.getElementById('callChart');
    if (!chartDom) return;

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
            data: chartData.months
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
                data: chartData.numCalls,
                itemStyle: {
                    color: '#0d6efd'
                }
            },
            {
                name: 'Total Cost (R)',
                type: 'bar',
                yAxisIndex: 1,
                data: chartData.totalCost,
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
}

// Table sorting functionality
function initTableSorting() {
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
            sortTable(table, column, newOrder);
        });
    });
}

function sortTable(table, column, order) {
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

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initTableSorting();
});
