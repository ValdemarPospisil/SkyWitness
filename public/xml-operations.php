<?php
require_once __DIR__ . '/../src/config/config.php';

// 1. Načtení XML ze souboru
$xmlFile = simplexml_load_file('xml/sightings.xml');
echo "<h2>1. XML načtené ze souboru:</h2>";
echo "<pre>" . htmlspecialchars($xmlFile->asXML()) . "</pre>";

// 2. Načtení XML z řetězce
$xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sightings>
    <sighting>
        <title>Testovací pozorování</title>
        <date>2023-07-01</date>
        <location>Testovací lokace</location>
        <description>Toto je testovací XML z řetězce</description>
    </sighting>
</sightings>
XML;

$xmlFromString = simplexml_load_string($xmlString);
echo "<h2>2. XML načtené z řetězce:</h2>";
echo "<pre>" . htmlspecialchars($xmlFromString->asXML()) . "</pre>";

// 3. Navigace v SimpleXML objektu
echo "<h2>3. Navigace v XML objektu:</h2>";
echo "<ul>";
foreach ($xmlFile->sighting as $sighting) {
    echo "<li>";
    echo "<strong>" . htmlspecialchars($sighting->title) . "</strong><br>";
    echo "Date: " . htmlspecialchars($sighting->date) . "<br>";
    echo "Location: " . htmlspecialchars($sighting->location);
    echo "</li>";
}
echo "</ul>";

// 4. Vytvoření nového XML pomocí SimpleXML
$newXml = new SimpleXMLElement('<?xml version="1.0"?><sightings></sightings>');
$sighting = $newXml->addChild('sighting');
$sighting->addChild('title', 'Nové pozorování');
$sighting->addChild('date', date('Y-m-d'));
$sighting->addChild('location', 'Nová lokace');
$sighting->addChild('description', 'Toto pozorování bylo vytvořeno programově');

echo "<h2>4. Nově vytvořené XML:</h2>";
echo "<pre>" . htmlspecialchars($newXml->asXML()) . "</pre>";

// 5. Čtení z databáze a generování XML
try {
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
        
        echo "Connected successfully!";
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    
    $stmt = $db->query("SELECT id, title, description, sighting_date, location FROM sightings");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $dbXml = new SimpleXMLElement('<?xml version="1.0"?><sightings></sightings>');
    
    foreach ($results as $row) {
        $sighting = $dbXml->addChild('sighting');
        $sighting->addChild('id', $row['id']);
        $sighting->addChild('title', $row['title']);
        $sighting->addChild('description', $row['description']);
        $sighting->addChild('date', $row['sighting_date']);
        $sighting->addChild('location', $row['location']);
    }
    
    echo "<h2>5. XML generované z databáze:</h2>";
    echo "<pre>" . htmlspecialchars($dbXml->asXML()) . "</pre>";
    
    // Uložení do databáze
    $insertStmt = $db->prepare("UPDATE sightings SET xml_data = ? WHERE id = ?");
    foreach ($dbXml->sighting as $sighting) {
        $insertStmt->execute([$sighting->asXML(), (string)$sighting->id]);
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>Database error: " . $e->getMessage() . "</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>XML Operations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">XML Operations with SimpleXML</h1>
        <?php include 'xml-operations-content.php'; ?>
    </div>
</body>
</html>