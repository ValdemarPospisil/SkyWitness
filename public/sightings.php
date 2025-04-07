<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/classes/Database.php';
require_once __DIR__.'/../src/classes/UfoSighting.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$ufoSighting = new UfoSighting($db);

// Filtry
$filters = [
    'country' => $_GET['country'] ?? '',
    'shape' => $_GET['shape'] ?? '',
    'year' => $_GET['year'] ?? '',
    'season' => $_GET['season'] ?? ''
];

// Získání seznamu zemí a tvarů pro filtry
$countries = $ufoSighting->getDistinctValues('country');
$shapes = $ufoSighting->getDistinctValues('ufo_shape');
$years = $ufoSighting->getDistinctValues('year');
$seasons = $ufoSighting->getDistinctValues('season');

// Počet záznamů na stránku
$limit = 50;

// Aktuální stránka (získaná z query parametru)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Získání dat s filtry
$sightings = $ufoSighting->getPaginatedWithFilters($limit, $offset, $filters);
$totalRecords = $ufoSighting->getTotalCountWithFilters($filters);
$totalPages = ceil($totalRecords / $limit);

$title = "UFO Sightings Database";
include __DIR__.'/../templates/header.php';

// Funkce pro vytvoření URL s filtry a stránkováním
function buildQueryString($params) {
    return http_build_query($params);
}

// Připravíme parametry pro stránkování
$urlParams = $filters;
?>

<main class="container">
    <article>
        <header>
            <h2><i class="ph ph-binoculars"></i> UFO Sightings</h2>
            <p>Browse through our database of reported unidentified aerial phenomena</p>
        </header>

        <!-- Filtry -->
        <details class="filters-container">
            <summary role="button" class="filters-summary">
                <i class="ph ph-funnel"></i> Filters
            </summary>
            <div class="filters-form">
                <form method="get" action="">
                <div class="filter-grid">
                    <label for="country">
                    Country:
                    <select name="country" id="country">
                        <option value="">All Countries</option>
                        <?php foreach ($countries as $country): ?>
                        <option value="<?= htmlspecialchars($country) ?>" <?= $filters['country'] === $country ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    </label>
                    <label for="shape">
                    UFO Shape:
                    <select name="shape" id="shape">
                        <option value="">All Shapes</option>
                        <?php foreach ($shapes as $shape): ?>
                        <option value="<?= htmlspecialchars($shape) ?>" <?= $filters['shape'] === $shape ? 'selected' : '' ?>>
                            <?= htmlspecialchars($shape) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    </label>
                    <label for="year">
                    Year:
                    <select name="year" id="year">
                        <option value="">All Years</option>
                        <?php foreach ($years as $year): ?>
                        <option value="<?= htmlspecialchars($year) ?>" <?= $filters['year'] === $year ? 'selected' : '' ?>>
                            <?= htmlspecialchars($year) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    </label>
                    <label for="season">
                    Season:
                    <select name="season" id="season">
                        <option value="">All Seasons</option>
                        <?php foreach ($seasons as $season): ?>
                        <option value="<?= htmlspecialchars($season) ?>" <?= $filters['season'] === $season ? 'selected' : '' ?>>
                            <?= htmlspecialchars($season) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    </label>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="primary">Apply Filters</button>
                    <a href="sighting_detail.php?id=<?= $sighting['id'] ?>" role="button" class="accent">
                        <i class="ph ph-arrow-bend-up-left"></i> Reset
                    </a>
                </div>
                </form>
            </div>
        </details>

        <div class="table-responsive sighting-card">
            <table role="grid">
                <thead>
                    <tr>
                        <th data-sort="date" scope="col"><i class="ph ph-calendar"></i> Date</th>
                        <th data-sort="location" scope="col"><i class="ph ph-map-pin"></i> Location</th>
                        <th data-sort="shape" scope="col"><i class="ph ph-shapes"></i> Shape</th>
                        <th data-sort="duration" scope="col"><i class="ph ph-clock"></i> Duration</th>
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
                            <a href="sighting_detail.php?id=<?= $sighting['id'] ?>" role="button" class="accent">
                                <i class="ph ph-info"></i> Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


            <ul class="pagination">
                <li>
                    <a 
                        href="?<?= buildQueryString(array_merge($urlParams, ['page' => max(1, $page - 1)])) ?>" 
                        class="button <?= $page == 1 ? 'secondary disabled' : 'accent' ?>" 
                        aria-label="Previous">
                        &laquo;
                    </a>
                </li>
                <!-- První stránka -->
                <li>
                    <a 
                        href="?<?= buildQueryString(array_merge($urlParams, ['page' => 1])) ?>" 
                        class="button <?= $page == 1 ? 'secondary disabled' : 'accent' ?>" 
                        aria-label="First">
                        1
                    </a>
                </li>

                <!-- Ellipsis před aktuální stránkou (když je potřeba) -->
                <?php if ($page > 3): ?>
                <li><span>...</span></li>
                <?php endif; ?>

                <!-- 5 stránek od aktuální (nebo méně, pokud jsme blízko začátku) -->
                <?php
                $startPage = max(2, $page - 2);
                $endPage = min($totalPages - 1, $page + 2);
                
                // Pokud jsme blízko začátku, zobrazujeme více stránek směrem dopředu
                if ($page < 4) {
                    $endPage = min($totalPages - 1, 6);
                }
                
                // Pokud jsme blízko konce, zobrazujeme více stránek směrem dozadu
                if ($page > $totalPages - 3) {
                    $startPage = max(2, $totalPages - 5);
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                <li>
                    <a 
                        href="?<?= buildQueryString(array_merge($urlParams, ['page' => $i])) ?>" 
                        class="button <?= $i == $page ? 'secondary disabled' : 'accent' ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>

                <!-- Ellipsis po aktuální stránce (když je potřeba) -->
                <?php if ($endPage < $totalPages - 1): ?>
                <li><span>...</span></li>
                <?php endif; ?>

                <!-- Poslední stránka (když není stejná jako první) -->
                <?php if ($totalPages > 1): ?>
                <li>
                    <a 
                        href="?<?= buildQueryString(array_merge($urlParams, ['page' => $totalPages])) ?>" 
                        class="button <?= $page == $totalPages ? 'secondary disabled' : 'accent' ?>">
                        <?= $totalPages ?>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Next tlačítko -->
                
                <li>
                    <a 
                        href="?<?= buildQueryString(array_merge($urlParams, ['page' => min($totalPages, $page + 1)])) ?>" 
                        class="button <?= $page == $totalPages ? 'secondary disabled' : 'accent' ?>" 
                        aria-label="Next">
                        &raquo;
                    </a>
                </li>
            </ul>
            <p class="pagination-info">
                <?= $totalRecords ?> záznamů celkem | Stránka <?= $page ?> z <?= $totalPages ?>
            </p>
    </article>
</main>
<script src="assets/js/table-sort.js"></script>
<?php include __DIR__.'/../templates/footer.php'; ?>