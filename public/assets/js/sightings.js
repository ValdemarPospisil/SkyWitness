document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => {
            const sortColumn = th.dataset.sort;
            if (!sortColumn) return;
            
            const currentOrder = th.classList.contains('asc') ? 'asc' : (th.classList.contains('desc') ? 'desc' : '');
            
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            
            const urlParams = new URLSearchParams(window.location.search);
            
            urlParams.set('sort', sortColumn);
            urlParams.set('order', newOrder);
            
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;

            setupRangeFilters();
        });
    });
    
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('sort');
    const currentOrder = urlParams.get('order');
    
    if (currentSort && currentOrder) {
        const th = document.querySelector(`th[data-sort="${currentSort}"]`);
        if (th) {
            th.classList.add(currentOrder);
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
    setupRangePair('year_min', 'year_max');
    setupRangePair('month_min', 'month_max');
    setupRangePair('hour_min', 'hour_max');
    
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
    const checkboxes = document.querySelectorAll('.sighting-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionCount);
    });

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
    
    const viewXmlBtn = document.getElementById('view-xml-btn');
    const downloadXmlBtn = document.getElementById('download-xml-btn');
    const viewStyledXmlBtn = document.getElementById('view-styled-xml-btn');
    const disabled = count === 0;
    
    viewXmlBtn.disabled = disabled;
    downloadXmlBtn.disabled = disabled;
    viewStyledXmlBtn.disabled = disabled;
    
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
