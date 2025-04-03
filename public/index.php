<?php
$title = "SkyWitness - UFO Sighting Database";
require_once __DIR__ . '/../src/config/config.php';
include __DIR__.'/../templates/header.php';

// Test DB připojení
try {
    $db = new PDO(
        "pgsql:host=".DB_HOST.";dbname=".DB_NAME, 
        DB_USER, 
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "<div class='alert alert-success'>Úspěšně připojeno přes PDO!</div>";
    
    } 
    catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Chyba PDO: " . $e->getMessage() . "</div>";
}
?>

    <!-- Statistiky -->
    <section>
        <h2><i class="ph ph-trend-up"></i> Global Statistics</h2>
        <div class="grid">
            <article>
                <h3><i class="ph ph-globe-hemisphere-west"></i> Top Countries</h3>
                <ol>
                    <li>United States</li>
                    <li>Canada</li>
                    <li>United Kingdom</li>
                </ol>
            </article>
            
            <article>
                <h3><i class="ph ph-shapes"></i> Common Shapes</h3>
                <ul>
                    <li>Light</li>
                    <li>Disk</li>
                    <li>Triangle</li>
                </ul>
            </article>
            
            <article>
                <h3><i class="ph ph-clock"></i> Recent Activity</h3>
                <p>Last sighting: 2 days ago</p>
                <p>New reports this week: 12</p>
            </article>
        </div>
    </section>

   
</main>

<?php include __DIR__.'/../templates/footer.php'; ?>
