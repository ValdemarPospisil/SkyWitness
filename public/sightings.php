<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/classes/Database.php';
require_once __DIR__.'/../src/classes/UfoSighting.php';

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$ufoSighting = new UfoSighting($db);

// Filtry
$filters = [
    'country' => isset($_GET['country']) ? (is_array($_GET['country']) ? $_GET['country'] : [$_GET['country']]) : [],
    'shape' => isset($_GET['shape']) ? (is_array($_GET['shape']) ? $_GET['shape'] : [$_GET['shape']]) : [],
    'year_min' => $_GET['year_min'] ?? '',
    'year_max' => $_GET['year_max'] ?? '',
    'month_min' => $_GET['month_min'] ?? '',
    'month_max' => $_GET['month_max'] ?? '',
    'hour_min' => $_GET['hour_min'] ?? '',
    'hour_max' => $_GET['hour_max'] ?? '',
    'season' => isset($_GET['season']) ? (is_array($_GET['season']) ? $_GET['season'] : [$_GET['season']]) : []
];


$filters['country'] = array_filter($filters['country'], function($v) { return $v !== ''; });
$filters['shape'] = array_filter($filters['shape'], function($v) { return $v !== ''; });
$filters['season'] = array_filter($filters['season'], function($v) { return $v !== ''; });

// Sorting parameters
$sortColumn = $_GET['sort'] ?? 'date_time'; // Default sort by date
$sortOrder = $_GET['order'] ?? 'desc'; // Default order is descending

// Validate sort column to prevent SQL injection
$allowedSortColumns = [
    'date' => 'date_time',
    'location' => 'country',
    'shape' => 'ufo_shape',
    'duration' => 'encounter_seconds'
];

// Map the frontend column name to database column
if (isset($allowedSortColumns[$sortColumn])) {
    $sortColumnDb = $allowedSortColumns[$sortColumn];
} else {
    $sortColumnDb = 'date_time'; // Default if invalid column
}

// Validate sort order
$sortOrderDb = (strtolower($sortOrder) === 'asc') ? 'ASC' : 'DESC';

// Získání seznamu zemí a tvarů pro filtry

$countries = $ufoSighting->getDistinctValues('country');
$shapes = $ufoSighting->getDistinctValues('ufo_shape');
$years = $ufoSighting->getDistinctValues('year');
$seasons = $ufoSighting->getDistinctValues('season');
$months = array_combine(range(1, 12), ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']);
$hours = array_combine(range(0, 23), range(0, 23));


// Počet záznamů na stránku
$limit = 50;

// Aktuální stránka (získaná z query parametru)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Získání dat s filtry a řazením
$sightings = $ufoSighting->getPaginatedWithFiltersAndSort($limit, $offset, $filters, $sortColumnDb, $sortOrderDb);
$totalRecords = $ufoSighting->getTotalCountWithFilters($filters);
$totalPages = ceil($totalRecords / $limit);

$title = "UFO Sightings Database";
include __DIR__.'/../templates/header.php';

// Funkce pro vytvoření URL s filtry a stránkováním
function buildQueryString($params) {
    return http_build_query($params);
}

// Připravíme parametry pro stránkování a zachování filtru/řazení
$urlParams = array_merge($filters, [
    'sort' => $_GET['sort'] ?? 'date',
    'order' => $_GET['order'] ?? 'desc'
]);
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
                    <!-- Country multiselect -->
                    <label for="country">
                    Country:
                    <select name="country[]" id="country" multiple size="4">
                        <?php foreach ($countries as $country): ?>
                        <option value="<?= htmlspecialchars($country) ?>" <?= in_array($country, $filters['country']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    </label>

                    <!-- UFO Shape multiselect -->
                    <label for="shape">
                    UFO Shape:
                    <select name="shape[]" id="shape" multiple size="4">
                        <?php foreach ($shapes as $shape): ?>
                        <option value="<?= htmlspecialchars($shape) ?>" <?= in_array($shape, $filters['shape']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($shape) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    </label>

                    <!-- Season multiselect -->
                    <label for="season">
                    Season:
                    <select name="season[]" id="season" multiple size="4">
                        <?php foreach ($seasons as $season): ?>
                        <option value="<?= htmlspecialchars($season) ?>" <?= in_array($season, $filters['season']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($season) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    </label>

                    <!-- Year range -->
                    <div>
                        <label for="year_min">
                        Year Range:
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <select name="year_min" id="year_min" style="width: 45%;">
                                <option value="">Min</option>
                                <?php foreach ($years as $year): ?>
                                <option value="<?= htmlspecialchars($year) ?>" <?= $filters['year_min'] === (string)$year ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span>to</span>
                            <select name="year_max" id="year_max" style="width: 45%;">
                                <option value="">Max</option>
                                <?php foreach ($years as $year): ?>
                                <option value="<?= htmlspecialchars($year) ?>" <?= $filters['year_max'] === (string)$year ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        </label>
                    </div>

                    <!-- Month range -->
                    <div>
                        <label for="month_min">
                        Month Range:
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <select name="month_min" id="month_min" style="width: 45%;">
                                <option value="">Min</option>
                                <?php foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= $filters['month_min'] === (string)$num ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($name) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span>to</span>
                            <select name="month_max" id="month_max" style="width: 45%;">
                                <option value="">Max</option>
                                <?php foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= $filters['month_max'] === (string)$num ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($name) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        </label>
                    </div>

                    <!-- Hour range -->
                    <div>
                        <label for="hour_min">
                        Hour Range:
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <select name="hour_min" id="hour_min" style="width: 45%;">
                                <option value="">Min</option>
                                <?php foreach ($hours as $hour): ?>
                                <option value="<?= $hour ?>" <?= $filters['hour_min'] === (string)$hour ? 'selected' : '' ?>>
                                    <?= str_pad($hour, 2, '0', STR_PAD_LEFT) ?>:00
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span>to</span>
                            <select name="hour_max" id="hour_max" style="width: 45%;">
                                <option value="">Max</option>
                                <?php foreach ($hours as $hour): ?>
                                <option value="<?= $hour ?>" <?= $filters['hour_max'] === (string)$hour ? 'selected' : '' ?>>
                                    <?= str_pad($hour, 2, '0', STR_PAD_LEFT) ?>:00
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        </label>
                    </div>
                </div>
                <div class="filter-actions">
                    <!-- Preserve sorting when applying filters -->
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? 'date') ?>">
                    <input type="hidden" name="order" value="<?= htmlspecialchars($_GET['order'] ?? 'desc') ?>">
                    <button type="submit" class="primary">Apply Filters</button>
                    <a href="sightings.php" role="button" class="accent">
                        <i class="ph ph-arrow-bend-up-left"></i> Reset
                    </a>
                </div>
                </form>
            </div>
        </details>

        <!-- XML Export Buttons -->
        <div class="xml-export-actions mb-3">
            <button id="select-all-btn" class="primary" onclick="toggleSelectAll()">
                <i class="ph ph-check-square"></i> Select All
            </button>
            <button id="view-xml-btn" class="accent" onclick="viewSelectedXml()" disabled>
                <i class="ph ph-file-arrow-up"></i> View as XML
            </button>
            <button id="download-xml-btn" class="accent" onclick="downloadSelectedXml()" disabled>
                <i class="ph ph-download"></i> Download XML
            </button>
            <button id="view-styled-xml-btn" class="accent" onclick="viewStyledXml()" disabled>
                <i style="color: black" class="ph ph-file-css"></i> View Styled XML
            </button>
            <span id="selection-count">0 sightings selected</span>
        </div>

        <form id="xml-export-form" method="post" action="export_xml.php" target="_blank">
            <input type="hidden" name="selected_ids" id="selected-ids-input" value="">
            <input type="hidden" name="action" id="xml-action" value="">
        </form>

        <div class="table-responsive sighting-card">
            <table role="grid">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-checkbox" aria-label="Select all sightings"></th>
                        <th data-sort="date" scope="col"><i class="pg ph ph-calendar"></i> Date</th>
                        <th data-sort="location" scope="col"><i class="pg ph ph-map-pin"></i> Location</th>
                        <th data-sort="shape" scope="col"><i class="pg ph ph-shapes"></i> Shape</th>
                        <th data-sort="duration" scope="col"><i class="pg ph ph-clock"></i> Duration</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sightings as $sighting): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="sighting-checkbox" value="<?= $sighting['id'] ?>" aria-label="Select sighting">
                        </td>
                        <td><?= date('M j, Y', strtotime($sighting['date_time'])) ?></td>
                        <td>
                            <?= $sighting['country'] ?? ''?>
                            <?= $sighting['region'] ? ', '.$sighting['region'] : '' ?>
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


<script src="assets/js/sightings.js"></script>
<?php include __DIR__.'/../templates/footer.php'; ?>
