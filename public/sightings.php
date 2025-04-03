<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/classes/Database.php';
require_once __DIR__.'/../src/classes/UfoSighting.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$ufoSighting = new UfoSighting($db);

// Počet záznamů na stránku
$limit = 50;

// Aktuální stránka (získaná z query parametru)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Získání dat
$sightings = $ufoSighting->getPaginated($limit, $offset);
$totalRecords = $ufoSighting->getTotalCount();
$totalPages = ceil($totalRecords / $limit);

$title = "UFO Sightings Database";
include __DIR__.'/../templates/header.php';
?>

<main class="container">
    <article>
        <header>
            <h2><i class="ph ph-binoculars"></i> UFO Sightings</h2>
            <p>Browse through our database of reported unidentified aerial phenomena</p>
        </header>

        <div class="table-responsive">
            <table role="grid">
                <thead>
                    <tr>
                        <th scope="col"><i class="ph ph-calendar"></i> Date</th>
                        <th scope="col"><i class="ph ph-map-pin"></i> Location</th>
                        <th scope="col"><i class="ph ph-shapes"></i> Shape</th>
                        <th scope="col"><i class="ph ph-clock"></i> Duration</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sightings as $sighting): ?>
                    <tr>
                        <td><?= date('M j, Y', strtotime($sighting['date_time'])) ?></td>
                        <td>
                            <?= htmlspecialchars($sighting['country']) ?>
                            <?= $sighting['region'] ? ', '.htmlspecialchars($sighting['region']) : '' ?>
                        </td>
                        <td>
                            <span class="ufo-shape">
                                <?= htmlspecialchars($sighting['ufo_shape'] ?? 'Unknown') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($sighting['encounter_duration']) ?></td>
                        <td>
                            <a href="sighting_detail.php?id=<?= $sighting['id'] ?>" role="button" class="secondary small">
                                <i class="ph ph-info"></i> Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginace -->
        
    <footer>
        <ul class="pagination">
    <!-- Previous Button -->
    <li>
        <a 
            href="?page=<?= max(1, $page - 1) ?>" 
            class="button <?= $page == 1 ? 'secondary disabled' : 'pico-background-jade-500 pico-color-black' ?>" 
            aria-label="Previous">
            &laquo;
        </a>
    </li>

    <!-- Stránky -->
    <?php for ($i = 1; $i <= min($totalPages, 5); $i++): ?>
    <li>
        <a 
            href="?page=<?= $i ?>" 
            class="button <?= $i == $page ? 'contrast' : 'pico-background-jade-500 pico-color-black' ?>">
            <?= $i ?>
        </a>
    </li>
    <?php endfor; ?>

    <!-- Ellipsis -->
    <?php if ($totalPages > 5): ?>
    <li><span>...</span></li>
    <li>
        <a href="?page=<?= $totalPages ?>" class="button pico-background-jade-500 pico-color-black">
            <?= $totalPages ?>
        </a>
    </li>
    <?php endif; ?>

    <!-- Next Button -->
    <li>
        <a 
            href="?page=<?= min($totalPages, $page + 1) ?>" 
            class="button <?= $page == $totalPages ? 'secondary disabled' : 'pico-background-jade-500 pico-color-black' ?>" 
            aria-label="Next">
            &raquo;
        </a>
    </li>
</ul>
        </footer>
  </article>
</main>

<?php include __DIR__.'/../templates/footer.php'; ?>
