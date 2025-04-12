document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    initMap();
    initLeafletMap();
    // Initialize encounter timer
    initEncounterTimer();
});

function initMap() {
    const coordValues = document.querySelectorAll('.coord-value');
    if (coordValues.length < 2) {
        document.getElementById('sighting-map').innerHTML = '<div class="map-error">Location coordinates not available</div>';
        return;
    }

    const latitude = parseFloat(coordValues[0].textContent);
    const longitude = parseFloat(coordValues[1].textContent);
    if (isNaN(latitude) || isNaN(longitude)) return;

    const marker = document.getElementById('map-marker');
    const mapImage = document.querySelector('#sighting-map img');

    // Použij přímo rozměry po načtení DOMu
    const mapWidth = mapImage.clientWidth;
    const mapHeight = mapImage.clientHeight;

    const LAT_MIN = -59.90;
    const LAT_MAX = 102.74;

    const latitudeRange = LAT_MAX - LAT_MIN;
    const y = ((LAT_MAX - latitude) / latitudeRange) * mapHeight;
    const x = ((longitude + 180) / 360) * mapWidth;

    console.log(`lat: ${latitude}, lng: ${longitude}, x: ${x}, y: ${y}`);
    console.log(`mapWidth: ${mapWidth}, mapHeight: ${mapHeight}`);

    
    marker.style.left = `${x}px`;
    marker.style.top = `${y}px`;
}

function initLeafletMap() {
    const coordValues = document.querySelectorAll('.coord-value');
    if (coordValues.length < 2) {
        document.getElementById('leaflet-map').innerHTML = '<div class="map-error">Location coordinates not available</div>';
        return;
    }

    const latitude = parseFloat(coordValues[0].textContent);
    const longitude = parseFloat(coordValues[1].textContent);
    if (isNaN(latitude) || isNaN(longitude)) return;

    // Inicializace Leaflet mapy
    const map = L.map('leaflet-map').setView([latitude, longitude], 4);

    // Tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker
    const marker = L.marker([latitude, longitude]).addTo(map)
        .bindPopup('Tady byl výskyt!')
}

document.getElementById('xml_file').addEventListener('change', function(e) {
    const fileNameSpan = document.getElementById('file-name');
    const selectedFileInfo = document.getElementById('file-selected-info');
    const selectedFileName = document.getElementById('selected-file-name');
    
    if (this.files.length > 0) {
        // Pokud byl vybrán soubor
        const fileName = this.files[0].name;
        fileNameSpan.textContent = fileName;
        selectedFileName.textContent = fileName;
        selectedFileInfo.style.display = 'flex';
        
        // Změna stylu upload boxu
        document.querySelector('.custom-file-upload').style.borderColor = 'var(--success)';
        document.querySelector('.custom-file-upload i').style.color = 'var(--success)';
    } else {
        // Pokud nebyl vybrán žádný soubor
        fileNameSpan.textContent = 'Klikněte pro výběr souboru';
        selectedFileInfo.style.display = 'none';
        
        // Vrátíme původní styl
        document.querySelector('.custom-file-upload').style.borderColor = 'var(--border)';
        document.querySelector('.custom-file-upload i').style.color = 'var(--text-2)';
    }
});

function initEncounterTimer() {
    const timerDisplay = document.getElementById('timer-display');
    const countdownBar = document.getElementById('countdown-bar');
    const startButton = document.getElementById('timer-start');
    const encounterSeconds = parseInt(document.getElementById('encounter-seconds').value);
    
    // If encounter time is not available or invalid
    if (isNaN(encounterSeconds) || encounterSeconds <= 0) {
        timerDisplay.textContent = 'Duration not available';
        startButton.disabled = true;
        return;
    }
    
    let remainingSeconds = encounterSeconds;
    let countdownInterval;
    let isRunning = false;
    
    // Format seconds to MM:SS or HH:MM:SS
    function formatTime(seconds) {
        if (seconds > 3600) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        }
    }
    
    // Update timer display
    function updateTimer() {
        timerDisplay.textContent = formatTime(remainingSeconds);
        
        // Update progress bar
        const progressPercentage = (remainingSeconds / encounterSeconds) * 100;
        countdownBar.style.width = `${progressPercentage}%`;
        
        // Countdown logic
        if (remainingSeconds <= 0) {
            clearInterval(countdownInterval);
            timerDisplay.textContent = "Encounter ended";
            startButton.innerHTML = '<i class="ph ph-arrows-clockwise"></i> Restart';
            isRunning = false;
        } else {
            remainingSeconds--;
        }
    }
    
    // Start/pause button click handler
    startButton.addEventListener('click', function() {
        if (isRunning) {
            // Stop the timer
            clearInterval(countdownInterval);
            startButton.innerHTML = '<i class="ph ph-play"></i> Continue';
            isRunning = false;
        } else {
            // Check if we need to reset
            if (remainingSeconds <= 0) {
                remainingSeconds = encounterSeconds;
            }
            
            // Start the timer
            updateTimer(); // Update immediately
            countdownInterval = setInterval(updateTimer, 1000);
            startButton.innerHTML = '<i class="ph ph-pause"></i> Pause';
            isRunning = true;
        }
    });
}