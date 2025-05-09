<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/classes/Database.php';
require_once __DIR__.'/../src/classes/UfoSighting.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$ufoSighting = new UfoSighting($db);

$id = $_GET['id'] ?? null;
$sighting = $id ? $ufoSighting->getById($id) : null;

if (!$sighting) {
    header("Location: sightings.php");
    exit;
}

// Add page title
$title = "SkyWitness - {$sighting['ufo_shape']} Sighting";

// Set season-based class
$seasonClass = strtolower($sighting['season'] ?? 'unknown');

include __DIR__.'/../templates/header.php';
?>

<div class="container mt-4 sighting-detail <?= $seasonClass ?>-theme">
    <div class="back-nav">
        <a href="sightings.php" class="btn btn-outline back-btn">
            <i class="ph ph-arrow-left"></i> Back to list
        </a>
    </div>

    <header class="sighting-header">
        <div class="sighting-title">
            <h1><?= htmlspecialchars($sighting['ufo_shape']) ?> Sighting</h1>
            <span class="season-badge"><?= htmlspecialchars($sighting['season']) ?></span>
        </div>
        <div class="location-badge">
            <i class="ph ph-map-pin"></i>
            <?= $sighting['country'] ?? 'Unknown country' ?>, 
            <?= $sighting['region'] ?? 'Unknown region' ?>
        </div>
    </header>
    
    <div class="sighting-grid">
        <!-- Main information card -->
        <div class="card main-info">
            <div class="card-date">
                <i class="ph ph-calendar"></i>
                <time datetime="<?= htmlspecialchars($sighting['date_time']) ?>">
                    <?= date("F j, Y", strtotime($sighting['date_time'])) ?>
                </time>
                <span class="time">
                    <i class="ph ph-clock"></i>
                    <?= date("g:i A", strtotime($sighting['date_time'])) ?>
                </span>
            </div>
            
            <div class="encounter-timer">
                <h3>Encounter Duration</h3>
                <div class="timer-display" id="timer-display">
                    <?= htmlspecialchars($sighting['encounter_duration']) ?>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" id="countdown-bar"></div>
                </div>
                <div class="timer-control">
                    <button id="timer-start" class="btn btn-primary">
                        <i class="ph ph-play"></i> Experience Duration
                    </button>
                </div>
                <input type="hidden" id="encounter-seconds" value="<?= (int)$sighting['encounter_seconds'] ?>">
            </div>
            
            <div class="sighting-description">
                <h3>Witness Description</h3>
                <p><?= nl2br($sighting['description']) ?></p>
            </div>
        </div>

        <!-- New Interactive Map Section -->
        <div class="card interactive-map">
            <h3>Interactive Map</h3>
            <div id="leaflet-map" style="height: 500px; margin-top: 1rem;"></div>
        </div>

        
        <!-- Map and coordinates -->
        <div class="card location-card">
            <h3>Sighting Location</h3>
            <div class="coordinates">
                <div class="coord-item">
                    <span class="coord-label">Latitude</span>
                    <span class="coord-value"><?= $sighting['latitude'] ?></span>
                </div>
                <div class="coord-item">
                    <span class="coord-label">Longitude</span>
                    <span class="coord-value"><?= $sighting['longitude'] ?></span>
                </div>
            </div>
            <div id="sighting-map" class="image-map">
                <img src="/assets/images/MapChart_Map.png" alt="World Map">
                <div class="marker" id="map-marker"></div>
            </div>
        </div>

        <!-- Metadata -->
        <div class="card metadata-card">
            <h3>Additional Information</h3>
            <table class="metadata-table">
                <tr>
                    <th>UFO Shape:</th>
                    <td><?= htmlspecialchars($sighting['ufo_shape']) ?></td>
                </tr>
                <tr>
                    <th>Season:</th>
                    <td><?= htmlspecialchars($sighting['season']) ?></td>
                </tr>
                <tr>
                    <th>Locale:</th>
                    <td><?= $sighting['locale'] ?? 'Unknown locale' ?></td>
                </tr>
                <tr>
                    <th>Year:</th>
                    <td><?= htmlspecialchars($sighting['year']) ?></td>
                </tr>
                <tr>
                    <th>Month:</th>
                    <td><?= date("F", mktime(0, 0, 0, $sighting['month'], 1)) ?></td>
                </tr>
                <tr>
                    <th>Hour:</th>
                    <td><?= $sighting['hour'] ?>:00</td>
                </tr>
                <tr>
                    <th>Date Documented:</th>
                    <td><?= date("F j, Y", strtotime($sighting['date_documented'])) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="assets/js/sighting-detail.js"></script>

<?php include __DIR__.'/../templates/footer.php'; ?>
