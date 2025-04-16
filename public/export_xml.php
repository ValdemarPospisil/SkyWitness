<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/classes/Database.php';
require_once __DIR__.'/../src/classes/UfoSighting.php';

// Check if we have selected IDs
if (!isset($_POST['selected_ids']) || empty($_POST['selected_ids'])) {
    header('Location: sightings.php');
    exit;
}

$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$ufoSighting = new UfoSighting($db);

// Get action type (view, download, or view_styled)
$action = isset($_POST['action']) ? $_POST['action'] : 'view';

// Get the selected IDs
$selectedIds = explode(',', $_POST['selected_ids']);

// Get sightings data
$sightings = $ufoSighting->getByIds($selectedIds);

// Create XML Document
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

// Create root element
$rootElement = $xml->createElement('ufo_sightings');
$xml->appendChild($rootElement);

// Add the collection timestamp and count attributes
$rootElement->setAttribute('exported_at', date('Y-m-d H:i:s'));
$rootElement->setAttribute('count', count($sightings));

// Add metadata element
$metaElement = $xml->createElement('metadata');
$rootElement->appendChild($metaElement);

// Add title element
$titleElement = $xml->createElement('title', 'SkyWitness UFO Sightings Data Export');
$metaElement->appendChild($titleElement);

// Add description element
$descElement = $xml->createElement('description', 'This XML file contains selected UFO sighting reports from the SkyWitness database.');
$metaElement->appendChild($descElement);

// Create sightings list element
$sightingsElement = $xml->createElement('sightings');
$rootElement->appendChild($sightingsElement);

// Loop through each sighting
foreach ($sightings as $sighting) {
    // Create a sighting element
    $sightingElement = $xml->createElement('sighting');
    $sightingsElement->appendChild($sightingElement);
    
    // Set sighting ID as attribute
    $sightingElement->setAttribute('id', $sighting['id']);
    
    // Add basic info
    $sightingElement->appendChild($xml->createElement('date_time', $sighting['date_time']));
    $sightingElement->appendChild($xml->createElement('year', $sighting['year']));
    $sightingElement->appendChild($xml->createElement('season', $sighting['season']));
    
    // Add location info in a nested element
    $locationElement = $xml->createElement('location');
    $sightingElement->appendChild($locationElement);
    
    $locationElement->appendChild($xml->createElement('country', htmlspecialchars($sighting['country'])));
    
    if (!empty($sighting['region'])) {
        $locationElement->appendChild($xml->createElement('region', htmlspecialchars($sighting['region'])));
    }
    
    if (!empty($sighting['latitude']) && !empty($sighting['longitude'])) {
        $geoElement = $xml->createElement('geo_coordinates');
        $locationElement->appendChild($geoElement);
        
        $geoElement->appendChild($xml->createElement('latitude', $sighting['latitude']));
        $geoElement->appendChild($xml->createElement('longitude', $sighting['longitude']));
    }
    
    // Add sighting details
    if (!empty($sighting['ufo_shape'])) {
        $sightingElement->appendChild($xml->createElement('shape', htmlspecialchars($sighting['ufo_shape'])));
    }
    
    if (!empty($sighting['encounter_duration'])) {
        $sightingElement->appendChild($xml->createElement('duration', htmlspecialchars($sighting['encounter_duration'])));
    }
    
    // Add witness report if available
    if (!empty($sighting['witness_report'])) {
        $reportElement = $xml->createElement('witness_report');
        $reportText = $xml->createCDATASection(htmlspecialchars($sighting['witness_report']));
        $reportElement->appendChild($reportText);
        $sightingElement->appendChild($reportElement);
    }
}

// Create XML string
$xmlString = $xml->saveXML();

// Handle different output formats
switch ($action) {
    case 'download':
        // Set headers for download
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="ufo_sightings_export.xml"');
        header('Content-Length: ' . strlen($xmlString));
        echo $xmlString;
        break;
    
    case 'view_styled':
        // Add XSL processing instruction
        $xslPI = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="assets/xsl/sightings.xsl"');
        $xml->insertBefore($xslPI, $xml->documentElement);
        
        // Output with appropriate headers
        header('Content-Type: application/xml');
        echo $xml->saveXML();
        break;
    
    case 'view':
    default:
        // Output plain XML
        header('Content-Type: application/xml');
        echo $xmlString;
        break;
}