<?php
require_once __DIR__ . '/../src/config/config.php';

// Procedurální styl
$conn = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASSWORD);
echo "<h2>Procedurální připojení:</h2>";
if ($conn) {
    echo "Úspěšně připojeno k PostgreSQL!";
    pg_close($conn);
} else {
    echo "Chyba připojení.";
}

// Objektově orientovaný styl
try {
    $db = new PDO("pgsql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
    echo "<h2>Objektově orientované připojení (PDO):</h2>";
    echo "Úspěšně připojeno přes PDO!";
    
    // Test dotazu
    $stmt = $db->query("SELECT * FROM sightings LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>První záznam:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "Chyba: " . $e->getMessage();
}
?>