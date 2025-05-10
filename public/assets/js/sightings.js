document.addEventListener('DOMContentLoaded', () => {
    // Set up server-side sorting functionality
    document.querySelectorAll('th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => {
            // Get the sort column name
            const sortColumn = th.dataset.sort;
            if (!sortColumn) return;
            
            // Check if this column is already being sorted
            const currentOrder = th.classList.contains('asc') ? 'asc' : (th.classList.contains('desc') ? 'desc' : '');
            
            // Determine the new sort order
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            
            // Get current URL and parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Set the sort parameters
            urlParams.set('sort', sortColumn);
            urlParams.set('order', newOrder);
            
            // Redirect to the new URL with sorting parameters
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;

            setupRangeFilters();
        });
    });
    
    // Highlight the currently sorted column
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('sort');
    const currentOrder = urlParams.get('order');
    
    if (currentSort && currentOrder) {
        const th = document.querySelector(`th[data-sort="${currentSort}"]`);
        if (th) {
            th.classList.add(currentOrder);
            // Add visual indicators for sort direction
            const indicator = document.createElement('span');
            indicator.className = 'sort-indicator';
            indicator.innerHTML = currentOrder === 'asc' ? ' ↑' : ' ↓';
            th.appendChild(indicator);
        }
    }
});


/**
 * Nastaví chování rozsahových filtrů
 */
function setupRangeFilters() {
    // Zajistí, že minimální hodnota není vyšší než maximální
    function setupRangePair(minId, maxId) {
        const minSelect = document.getElementById(minId);
        const maxSelect = document.getElementById(maxId);
        
        if (!minSelect || !maxSelect) return;
        
        minSelect.addEventListener('change', () => {
            const minVal = minSelect.value;
            if (minVal && maxSelect.value && parseInt(minVal) > parseInt(maxSelect.value)) {
                maxSelect.value = minVal;
            }
        });
        
        maxSelect.addEventListener('change', () => {
            const maxVal = maxSelect.value;
            if (maxVal && minSelect.value && parseInt(maxVal) < parseInt(minSelect.value)) {
                minSelect.value = maxVal;
            }
        });
    }
    // Nastavení pro jednotlivé rozsahové filtry
    setupRangePair('year_min', 'year_max');
    setupRangePair('month_min', 'month_max');
    setupRangePair('hour_min', 'hour_max');
    
    // Přidání funkce pro reset jednotlivých filtrů
    const resetButtons = document.querySelectorAll('.reset-filter');
    resetButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const target = button.dataset.target;
            const elements = document.querySelectorAll(`[name^="${target}"]`);
            
            elements.forEach(el => {
                if (el.tagName === 'SELECT') {
                    if (el.multiple) {
                        Array.from(el.options).forEach(option => option.selected = false);
                    } else {
                        el.value = '';
                    }
                } else if (el.tagName === 'INPUT') {
                    el.value = '';
                }
            });
        });
    });
}






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
