document.addEventListener('DOMContentLoaded', function() {
    initMap();
    initLeafletMap();
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

    const mapElement = document.getElementById('leaflet-map');
    if (!mapElement) {
        console.error('Map element not found!');
        return;
    }

    const map = L.map('leaflet-map').setView([latitude, longitude], 4);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([latitude, longitude]).addTo(map)
        .bindPopup('Sighting location')
        .openPopup();
        
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
}


function initEncounterTimer() {
    const timerDisplay = document.getElementById('timer-display');
    const countdownBar = document.getElementById('countdown-bar');
    const startButton = document.getElementById('timer-start');
    const encounterSeconds = parseInt(document.getElementById('encounter-seconds').value);
    
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
    
    function updateTimer() {
        timerDisplay.textContent = formatTime(remainingSeconds);
        
        const progressPercentage = (remainingSeconds / encounterSeconds) * 100;
        countdownBar.style.width = `${progressPercentage}%`;
        
        if (remainingSeconds <= 0) {
            clearInterval(countdownInterval);
            timerDisplay.textContent = "Encounter ended";
            startButton.innerHTML = '<i class="ph ph-arrows-clockwise"></i> Restart';
            isRunning = false;
        } else {
            remainingSeconds--;
        }
    }
    
    startButton.addEventListener('click', function() {
        if (isRunning) {
            clearInterval(countdownInterval);
            startButton.innerHTML = '<i class="ph ph-play"></i> Continue';
            isRunning = false;
        } else {
            if (remainingSeconds <= 0) {
                remainingSeconds = encounterSeconds;
            }
            
            updateTimer();
            countdownInterval = setInterval(updateTimer, 1000);
            startButton.innerHTML = '<i class="ph ph-pause"></i> Pause';
            isRunning = true;
        }
    });
}
