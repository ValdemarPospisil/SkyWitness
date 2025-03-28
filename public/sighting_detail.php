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

include __DIR__.'/../templates/header.php';
?>

<div class="container mt-4">
    <h1><?= htmlspecialchars($sighting['ufo_shape']) ?> Sighting</h1>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <?= htmlspecialchars($sighting['country']) ?>, 
                <?= htmlspecialchars($sighting['region']) ?>
            </h5>
            <h6 class="card-subtitle mb-2 text-muted">
                <?= htmlspecialchars($sighting['date_time']) ?>
            </h6>
            <p class="card-text">
                <strong>Duration:</strong> <?= htmlspecialchars($sighting['encounter_duration']) ?><br>
                <strong>Coordinates:</strong> <?= $sighting['latitude'] ?>, <?= $sighting['longitude'] ?>
            </p>
            <p class="card-text">
                <?= nl2br(htmlspecialchars($sighting['description'])) ?>
            </p>
            <a href="sightings.php" class="btn btn-primary">Back to list</a>
        </div>
    </div>
</div>
