<?php
$xmlFile = __DIR__ . "/xml/data.xml";

if (!file_exists($xmlFile)) {
    die("Error: XML file not found.");
}

$xml = simplexml_load_file($xmlFile);
if ($xml === false) {
    die("Error: Failed to parse XML file.");
}

echo "<h1>Hello, World!</h1>";
echo "<h1>Hello, World!</h1>";
echo "<pre>";
print_r($xml);
echo "</pre>";
?>
