<?php
$title = "SkyWitness - UFO Sighting Database";
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/classes/Database.php';
require_once __DIR__ . '/../src/classes/UfoSighting.php';
require_once __DIR__ . '/../src/classes/UfoStatistics.php';
include __DIR__.'/../templates/header.php';

// Vytvoření instance Database
try {
    $db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
    
    // Inicializace statistik
    $stats = new UfoStatistics($db);
    
    // Získání náhodného pozorování
    $randomSighting = $stats->getRandomSighting();
    
    // Získání statistik
    $topCountries = $stats->getTopCountries(5);
    $commonShapes = $stats->getCommonShapes(5);
    $sightingsByYear = $stats->getSightingsByYear(10);
    $sightingsByHour = $stats->getSightingsByHour();
    $sightingsBySeasons = $stats->getSightingsBySeasons();
    $totalSightings = $stats->getTotalSightings();
    $avgDuration = $stats->getAverageDuration();
    
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Error connectiong to datbase: " . $e->getMessage() . "</div>";
}
?>

<section class="hero-section">
    <h2><i class="ph ph-flying-saucer"></i> Database UFO sighting</h2>
    <p>Discover <?= number_format($totalSightings) ?> documented ufo sightings all over the world.</p>
    <div class="action-buttons">
        <a href="/sightings.php" role="button" class="primary">
            <i class="ph ph-binoculars"></i> Go through sightings
        </a>
        <a href="/add_sighting.php" role="button" class="secondary">
            <i class="ph ph-plus-circle"></i> Add new
        </a>
    </div>
</section>

<?php if ($randomSighting): ?>
<article class="sighting-card">
    <header>
        <h3><i class="ph ph-shooting-star"></i> Random sighting</h3>
    </header>
    <div class="sighting-info">
        <p><strong>Date:</strong> <?= date('j. F Y', strtotime($randomSighting['date_time'])) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($randomSighting['locale'] . ', ' . $randomSighting['region'] . ', ' . $randomSighting['country']) ?></p>
        <p><strong>Shape:</strong> <?= htmlspecialchars($randomSighting['ufo_shape']) ?></p>
        <p><?= substr($randomSighting['description'], 0, 300) ?>...</p>
    </div>
    <footer>
        <a href="/sighting_detail.php?id=<?= $randomSighting['id'] ?>" role="button" class="accent">
            <i class="ph ph-info"></i> View detail
        </a>
    </footer>
</article>
<?php endif; ?>

<section class="stats-section">
    <h2><i class="ph ph-chart-line"></i> Statistics UFO sightings</h2>
    
    <div class="grid">
        <!-- Top země -->
        <article class="primary-bg stat-card">
            <h3><i class="ph ph-globe-hemisphere-west"></i> Top countries</h3>
            <ol>
                <?php foreach ($topCountries as $country): ?>
                <li><?= htmlspecialchars($country['country']) ?> (<?= number_format($country['count']) ?>)</li>
                <?php endforeach; ?>
            </ol>
        </article>
        
        <!-- Nejčastější tvary -->
        <article class="secondary-bg stat-card">
            <h3><i class="ph ph-shapes"></i> Most common shapes</h3>
            <ul>
                <?php foreach ($commonShapes as $shape): ?>
                <li><?= htmlspecialchars($shape['ufo_shape']) ?> (<?= number_format($shape['count']) ?>)</li>
                <?php endforeach; ?>
            </ul>
        </article>
        
        <!-- Roční období -->
        <article class="stat-card surface-1">
            <h3><i class="ph ph-tree"></i> Seasons</h3>
            <ul>
                <?php foreach ($sightingsBySeasons as $season): ?>
                <li><?= htmlspecialchars($season['season']) ?> (<?= number_format($season['count']) ?>)</li>
                <?php endforeach; ?>
            </ul>
        </article>
        
        <!-- Průměrná doba -->
        <article class="stat-card surface-1">
            <h3><i class="ph ph-clock"></i> Average sighting duration</h3>
            <p class="stat-highlight"><?= round($avgDuration / 60, 1) ?> minutes</p>
            <p class="stat-description">Average suration time of the ufo sighting</p>
        </article>
    </div>
</section>

<!-- Denní statistiky -->
<section class="time-stats">
    <h2><i class="ph ph-clock-countdown"></i> When does the UFO appear?</h2>
    <p>UFO sightings by time (hour)</p>
    
    <div class="hour-chart">
        <?php 
        // Najdeme maximum pro škálování
        $maxCount = 0;
        foreach ($sightingsByHour as $hourData) {
            if ($hourData['count'] > $maxCount) {
                $maxCount = $hourData['count'];
            }
        }
        
        // Vykreslíme graf
        for ($i = 0; $i < 24; $i++) {
            $hourCount = 0;
            foreach ($sightingsByHour as $hourData) {
                if ($hourData['hour'] == $i) {
                    $hourCount = $hourData['count'];
                    break;
                }
            }
            
            $height = ($hourCount / $maxCount) * 100;
            $label = $i . ':00';
        ?>
        <div class="hour-bar">
            <div class="bar-fill" style="height: <?= $height ?>%"></div>
            <div class="bar-label"><?= $label ?></div>
        </div>
        <?php } ?>
    </div>
    
    <p class="time-insight">
        <i class="ph ph-lightbulb"></i> 
        <?php
        // Najdeme hodinu s nejvíce pozorováními
        $maxHour = 0;
        $maxHourCount = 0;
        foreach ($sightingsByHour as $hourData) {
            if ($hourData['count'] > $maxHourCount) {
                $maxHourCount = $hourData['count'];
                $maxHour = $hourData['hour'];
            }
        }
        ?>
        Most ufo sightings were registered in <?= $maxHour ?>:00 hodin.
    </p>
</section>

<!-- Roční trend -->
<section class="year-stats">
    <h2><i class="ph ph-chart-line-up"></i> Trends in sightins</h2>
    <div class="year-chart">
        <?php 
        // Seřadíme roky vzestupně pro graf
        $years = array_reverse($sightingsByYear);
        
        // Najdeme maximum pro škálování
        $maxCount = 0;
        foreach ($years as $yearData) {
            if ($yearData['count'] > $maxCount) {
                $maxCount = $yearData['count'];
            }
        }
        
        // Vykreslíme graf
        foreach ($years as $yearData) {
            $height = ($yearData['count'] / $maxCount) * 100;
        ?>
        <div class="year-bar">
            <div class="bar-fill" style="height: <?= $height ?>%"></div>
            <div class="bar-label"><?= $yearData['year'] ?></div>
        </div>
        <?php } ?>
    </div>
</section>

<!-- Výzva k participaci -->
<section class="cta-section">
    <h2><i class="ph ph-user-plus"></i> Have you ever seen an UFO?</h2>
    <p>Add your own sighting to our global database and help map the unknown</p>
    <a href="/add_sighting.php" role="button" class="primary">
        <i class="ph ph-plus-circle"></i> Add new sighting
    </a>
</section>
   
</main>

<?php include __DIR__.'/../templates/footer.php'; ?>
