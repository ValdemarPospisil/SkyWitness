<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>XML Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .sighting { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>UFO Sightings from XML</h2>
    
    <?php
    // Load XML file
    $xml = simplexml_load_file('xml/sightings.xml');
    
    if ($xml) {
        foreach ($xml->sighting as $sighting) {
            echo '<div class="sighting">';
            echo '<h2>' . htmlspecialchars($sighting->title) . '</h2>';
            echo '<p><strong>Date:</strong> ' . htmlspecialchars($sighting->date) . '</p>';
            echo '<p><strong>Location:</strong> ' . htmlspecialchars($sighting->location) . '</p>';
            echo '<p>' . htmlspecialchars($sighting->description) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>Failed to load XML file.</p>';
    }
    ?>
    
    <h2>Raw XML Data</h2>
    <pre><?php echo htmlspecialchars(file_get_contents('xml/sightings.xml')); ?></pre>
</body>
</html>