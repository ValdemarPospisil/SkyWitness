// table-sort.js
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => {
            const table = th.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const columnIndex = Array.from(th.parentNode.children).indexOf(th);
            const type = th.dataset.sort;
            const ascending = !th.classList.contains('asc');

            rows.sort((a, b) => {
                const cellA = a.children[columnIndex].textContent.trim().toLowerCase();
                const cellB = b.children[columnIndex].textContent.trim().toLowerCase();

                if (type === 'date') {
                    return ascending
                        ? new Date(cellA) - new Date(cellB)
                        : new Date(cellB) - new Date(cellA);
                }

                return ascending
                    ? cellA.localeCompare(cellB)
                    : cellB.localeCompare(cellA);
            });

            table.querySelectorAll('th').forEach(t => t.classList.remove('asc', 'desc'));
            th.classList.add(ascending ? 'asc' : 'desc');

            rows.forEach(row => tbody.appendChild(row));
        });
    });
});
