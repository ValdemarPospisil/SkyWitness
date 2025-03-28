<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/classes/Database.php';
require_once __DIR__.'/../src/classes/UfoSighting.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$ufoSighting = new UfoSighting($db);

// Získání dat
$sightings = $ufoSighting->getAll(150);

// Zobrazení
include __DIR__.'/../templates/header.php';
?>

<div class="container mt-4">
    <h3 class="mb-4">UFO Sightings</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Location</th>
                <th>Shape</th>
                <th>Duration</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sightings as $sighting): ?>
            <tr>
                <td><?= htmlspecialchars($sighting['date_time']) ?></td>
                <td>
                    <?= htmlspecialchars($sighting['country']) ?>,
                    <?= htmlspecialchars($sighting['region']) ?>
                </td>
                <td><?= htmlspecialchars($sighting['ufo_shape'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($sighting['encounter_duration']) ?></td>
                <td>
                    <a href="sighting_detail.php?id=<?= $sighting['id'] ?>" class="btn btn-sm btn-info">
                        Detail
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
