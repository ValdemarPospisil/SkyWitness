<?php
require_once __DIR__ . '/../src/config/config.php';
include __DIR__.'/../templates/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test DB Connection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Test Database Connection</h1>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2>Procedurální připojení (pg_connect)</h2>
            </div>
            <div class="card-body">
                <?php
                if (function_exists('pg_connect')) {
                    $conn = pg_connect("host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER." password=".DB_PASSWORD);
                    if ($conn) {
                        echo "<div class='alert alert-success'>Úspěšně připojeno k PostgreSQL!</div>";
                        
                        // Ukázkový dotaz
                        $result = pg_query($conn, "SELECT id, country, description FROM ufo_sightings LIMIT 10");
                        $row = pg_fetch_assoc($result);
                        
                        echo "<h4>První záznam:</h4>";
                        echo "<pre>" . print_r($row, true) . "</pre>";
                        
                        pg_close($conn);
                    } else {
                        echo "<div class='alert alert-danger'>Chyba připojení.</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>Rozšíření pgsql není nainstalováno.</div>";
                }
                ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h2>Objektově orientované připojení (PDO)</h2>
            </div>
            <div class="card-body">
                <?php
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
                    
                    // Test dotazu
                    $stmt = $db->query("SELECT id, country, description FROM ufo_sightings LIMIT 10");
                    $result = $stmt->fetch();
                    
                    echo "<h4>První záznam:</h4>";
                    echo "<pre>" . print_r($result, true) . "</pre>";
                    
                } catch(PDOException $e) {
                    echo "<div class='alert alert-danger'>Chyba PDO: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>