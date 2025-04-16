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


document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all checkboxes
    const checkboxes = document.querySelectorAll('.sighting-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionCount);
    });

    // Initialize the header checkbox
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.sighting-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateSelectionCount();
    });

    updateSelectionCount();
});

function updateSelectionCount() {
    const selectedCheckboxes = document.querySelectorAll('.sighting-checkbox:checked');
    const count = selectedCheckboxes.length;
    const countDisplay = document.getElementById('selection-count');
    countDisplay.textContent = count + ' sighting' + (count !== 1 ? 's' : '') + ' selected';
    
    // Update buttons state
    const viewXmlBtn = document.getElementById('view-xml-btn');
    const downloadXmlBtn = document.getElementById('download-xml-btn');
    const viewStyledXmlBtn = document.getElementById('view-styled-xml-btn');
    const disabled = count === 0;
    
    viewXmlBtn.disabled = disabled;
    downloadXmlBtn.disabled = disabled;
    viewStyledXmlBtn.disabled = disabled;
    
    // Update select all button text
    const selectAllBtn = document.getElementById('select-all-btn');
    const allSelected = count === document.querySelectorAll('.sighting-checkbox').length;
    selectAllBtn.innerHTML = allSelected ? 
        '<i class="ph ph-x-square"></i> Deselect All' : 
        '<i class="ph ph-check-square"></i> Select All';
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.sighting-checkbox');
    const allSelected = Array.from(checkboxes).every(checkbox => checkbox.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allSelected;
    });
    
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    selectAllCheckbox.checked = !allSelected;
    
    updateSelectionCount();
}

function collectSelectedIds() {
    const selectedCheckboxes = document.querySelectorAll('.sighting-checkbox:checked');
    return Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
}

function viewSelectedXml() {
    const selectedIds = collectSelectedIds();
    if (selectedIds.length === 0) return;
    
    document.getElementById('selected-ids-input').value = selectedIds.join(',');
    document.getElementById('xml-action').value = 'view';
    document.getElementById('xml-export-form').submit();
}

function downloadSelectedXml() {
    const selectedIds = collectSelectedIds();
    if (selectedIds.length === 0) return;
    
    document.getElementById('selected-ids-input').value = selectedIds.join(',');
    document.getElementById('xml-action').value = 'download';
    document.getElementById('xml-export-form').submit();
}

function viewStyledXml() {
    const selectedIds = collectSelectedIds();
    if (selectedIds.length === 0) return;
    
    document.getElementById('selected-ids-input').value = selectedIds.join(',');
    document.getElementById('xml-action').value = 'view_styled';
    document.getElementById('xml-export-form').submit();
}