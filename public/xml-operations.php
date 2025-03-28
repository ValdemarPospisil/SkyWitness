<?php
require_once __DIR__ . '/../src/config/config.php';
include __DIR__.'/../templates/header.php';
// 1. Načtení XML ze souboru
echo "<div class='container mt-4'>";
echo "<h2 class='mb-4'>XML Operations with UFO Sightings</h2>";

$xmlFile = simplexml_load_file('xml/ufo_sightings.xml');
echo "<h3>1. XML načtené ze souboru:</h3>";
echo "<div class='row mb-4'>";
echo "<div class='col-md-6'><h4>Struktura SimpleXML:</h4><pre class='bg-light p-3'>" . htmlspecialchars(print_r($xmlFile, true)) . "</pre></div>";
echo "<div class='col-md-6'><h4>Zdrojové XML:</h4><pre class='bg-light p-3'>" . htmlspecialchars(file_get_contents('xml/ufo_sightings.xml')) . "</pre></div>";
echo "</div>";

// 2. Načtení XML z řetězce
$xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sightings>
    <sighting id="999">
        <title>Testovací pozorování</title>
        <date>2023-07-01</date>
        <location>Testovací lokace</location>
        <description>Toto je testovací XML z řetězce</description>
    </sighting>
</sightings>
XML;

$xmlFromString = simplexml_load_string($xmlString);
echo "<h3>2. XML načtené z řetězce:</h3>";
echo "<pre class='bg-light p-3'>" . htmlspecialchars(print_r($xmlFromString, true)) . "</pre>";

// 3. Navigace v SimpleXML objektu
echo "<h3>3. Navigace v XML objektu:</h3>";
echo "<div class='card mb-4'>";
echo "<div class='card-header'>Rekurzivní průchod stromem</div>";
echo "<div class='card-body'><pre>";

function traverseSimpleXML($xml, $level = 0) {
    $indent = str_repeat('  ', $level);
    
    foreach ($xml->attributes() as $name => $value) {
        echo $indent . "[ATTR] $name: $value\n";
    }
    
    foreach ($xml->children() as $name => $element) {
        if ($element->count() > 0) {
            echo $indent . "$name:\n";
            traverseSimpleXML($element, $level + 1);
        } else {
            echo $indent . "$name: " . (string)$element . "\n";
        }
    }
}

traverseSimpleXML($xmlFile);
echo "</pre></div></div>";

// 4. Příklady přímého přístupu k datům
echo "<h3>4. Příklady přímého přístupu k datům</h3>";
echo "<div class='card mb-4'>";
echo "<div class='card-header'>Přímý přístup k elementům</div>";
echo "<div class='card-body'>";

if (isset($xmlFile->sighting[0])) {
    $firstSighting = $xmlFile->sighting[0];
    echo "<p><strong>První pozorování:</strong> " . htmlspecialchars($firstSighting->title) . "</p>";
    echo "<p><strong>Datum:</strong> " . htmlspecialchars($firstSighting->date) . "</p>";
    echo "<p><strong>Lokace:</strong> " . htmlspecialchars($firstSighting->location) . "</p>";
}

echo "<h5 class='mt-3'>XPath dotaz (všechny pozorování v USA):</h5>";
$usSightings = $xmlFile->xpath('//sighting[country="United States"]');
echo "<pre>" . htmlspecialchars(print_r($usSightings, true)) . "</pre>";
echo "</div></div>";

// 5. Vytvoření nového XML
echo "<h3>5. Vytvoření nového XML</h3>";
$newXml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sightings></sightings>');

// Přidání stylu
$newXml->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

// Přidání pozorování
$sighting = $newXml->addChild('sighting');
$sighting->addAttribute('id', '1000');
$sighting->addChild('title', 'Nové pozorování');
$sighting->addChild('date', date('Y-m-d'));
$sighting->addChild('location', 'Nová lokace');
$sighting->addChild('country', 'Czech Republic');
$sighting->addChild('description', 'Toto pozorování bylo vytvořeno programově pomocí SimpleXML');

echo "<div class='row mb-4'>";
echo "<div class='col-md-6'><h4>Kód pro vytvoření:</h4><pre class='bg-light p-3'>";
echo htmlspecialchars(<<<'CODE'
$newXml = new SimpleXMLElement('<?xml version="1.0"?><sightings></sightings>');
$sighting = $newXml->addChild('sighting');
$sighting->addAttribute('id', '1000');
$sighting->addChild('title', 'Nové pozorování');
// ...
CODE);
echo "</pre></div>";
echo "<div class='col-md-6'><h4>Výsledné XML:</h4><pre class='bg-light p-3'>" . htmlspecialchars($newXml->asXML()) . "</pre></div>";
echo "</div>";

// 6. Export do souboru
$exportPath = 'xml/generated_sightings.xml';
$newXml->asXML($exportPath);
echo "<div class='alert alert-success'>Nové XML bylo uloženo do: $exportPath</div>";

// Zobrazení odkazu na stažení
echo "<a href='$exportPath' class='btn btn-primary mb-4' download>Stáhnout generované XML</a>";

// Ukázka HTTP hlavičky pro XML
echo "<h2>6. HTTP XML výstup</h2>";
echo "<p><a href='xml-output.php' class='btn btn-info'>Zobrazit čistý XML výstup</a></p>";
?>
</div>

<!-- Vytvoření samostatného xml-output.php -->
<?php
file_put_contents('xml-output.php', '<?php
header("Content-Type: application/xml");
echo \'' . str_replace("'", "\'", $newXml->asXML()) . '\';
?>');
?>