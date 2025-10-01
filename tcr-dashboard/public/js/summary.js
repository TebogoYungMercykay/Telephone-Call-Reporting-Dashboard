// tcr-dashboard/resources/views/calls/summary.blade.php scripts

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
