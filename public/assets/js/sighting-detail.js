document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tabButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Hide all panes
            tabPanes.forEach(pane => pane.classList.remove('active'));
            // Show selected pane
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });
    
    // Auto-fill year, month, hour from datetime
    const dateTimeInput = document.getElementById('date_time');
    const yearInput = document.getElementById('year');
    const monthInput = document.getElementById('month');
    const hourInput = document.getElementById('hour');
    
    dateTimeInput.addEventListener('change', function() {
        if (this.value) {
            const dateObj = new Date(this.value);
            yearInput.value = dateObj.getFullYear();
            monthInput.value = dateObj.getMonth() + 1; // +1 because getMonth() returns 0-11
            hourInput.value = dateObj.getHours();
        }
    });
    
    // Calculate encounter duration text from seconds
    const secondsInput = document.getElementById('encounter_seconds');
    const durationInput = document.getElementById('encounter_duration');
    
    secondsInput.addEventListener('change', function() {
        if (this.value) {
            const seconds = parseInt(this.value);
            if (seconds < 60) {
                durationInput.value = seconds + " seconds";
            } else if (seconds < 3600) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                durationInput.value = minutes + " minute" + (minutes > 1 ? "s" : "") + 
                    (remainingSeconds > 0 ? " " + remainingSeconds + " second" + (remainingSeconds > 1 ? "s" : "") : "");
            } else {
                const hours = Math.floor(seconds / 3600);
                const remainingMinutes = Math.floor((seconds % 3600) / 60);
                durationInput.value = hours + " hour" + (hours > 1 ? "s" : "") + 
                    (remainingMinutes > 0 ? " " + remainingMinutes + " minute" + (remainingMinutes > 1 ? "s" : "") : "");
            }
        }
    });
    
    // File upload handling - showing selected file name
    const fileInput = document.getElementById('xml_file');
    const fileNameSpan = document.getElementById('file-name');
    const selectedFileNameSpan = document.getElementById('selected-file-name');
    const fileSelectedInfo = document.getElementById('file-selected-info');
    const customFileUpload = document.querySelector('.custom-file-upload');
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            selectedFileNameSpan.textContent = this.files[0].name;
            fileSelectedInfo.style.display = 'flex';
            customFileUpload.classList.add('file-selected');
            fileNameSpan.textContent = 'File selected';
        } else {
            fileSelectedInfo.style.display = 'none';
            customFileUpload.classList.remove('file-selected');
            fileNameSpan.textContent = 'Click to select a file';
        }
    });
    
    // Drag and drop functionality
    const dropZone = document.querySelector('.custom-file-upload');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
        dropZone.classList.add('highlight');
    }
    
    function unhighlight() {
        dropZone.classList.remove('highlight');
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Trigger change event manually
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
});