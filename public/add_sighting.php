<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/classes/Database.php';
require_once __DIR__.'/../src/classes/UfoSighting.php';
require_once __DIR__.'/../src/classes/XmlProcessor.php';


$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
$ufoSighting = new UfoSighting($db);
$xmlProcessor = new XmlProcessor($db);

$title = "SkyWitness - Add New Sighting";
$message = [];
$formData = [];

// Processing XML file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_xml'])) {
    if (isset($_FILES['xml_file']) && $_FILES['xml_file']['error'] === UPLOAD_ERR_OK) {
        $xmlFile = $_FILES['xml_file']['tmp_name'];
        
        try {
            // Validate XML against XSD
            $isValid = $xmlProcessor->validateXml($xmlFile, __DIR__.'/xml/sighting_validation.xsd');
            
            if ($isValid) {
                // Process XML and save to database
                $result = $xmlProcessor->processXmlFile($xmlFile);
                $message['success'] = "XML file successfully processed. $result records added.";
            } else {
                $message['error'] = "XML file is not valid according to the XSD schema.";
            }
        } catch (Exception $e) {
            $message['error'] = "Error processing XML: " . $e->getMessage();
        }
    } else {
        $message['error'] = "Failed to upload file. Check file type and size.";
    }
}

// Processing HTML form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_form'])) {
    // Get form data
    $formData = [
        'date_time' => $_POST['date_time'] ?? '',
        'year' => $_POST['year'] ?? '',
        'month' => $_POST['month'] ?? '',
        'hour' => $_POST['hour'] ?? '',
        'season' => $_POST['season'] ?? '',
        'country_code' => $_POST['country_code'] ?? '',
        'country' => $_POST['country'] ?? '',
        'region' => $_POST['region'] ?? '',
        'locale' => $_POST['locale'] ?? '',
        'latitude' => $_POST['latitude'] ?? '',
        'longitude' => $_POST['longitude'] ?? '',
        'ufo_shape' => $_POST['ufo_shape'] ?? '',
        'encounter_seconds' => $_POST['encounter_seconds'] ?? '',
        'encounter_duration' => $_POST['encounter_duration'] ?? '',
        'description' => $_POST['description'] ?? ''
    ];
    
    // Form validation
    $errors = [];
    if (empty($formData['date_time'])) $errors[] = "Date and time are required.";
    if (empty($formData['country'])) $errors[] = "Country is required.";
    if (!is_numeric($formData['latitude']) || $formData['latitude'] < -90 || $formData['latitude'] > 90) {
        $errors[] = "Invalid latitude value.";
    }
    if (!is_numeric($formData['longitude']) || $formData['longitude'] < -180 || $formData['longitude'] > 180) {
        $errors[] = "Invalid longitude value.";
    }
    
    if (empty($errors)) {
        try {
            // Generate XML from form data
            $xml = $xmlProcessor->generateXmlFromFormData($formData);
            
            // Validate generated XML
            $xmlFilePath = tempnam(sys_get_temp_dir(), 'ufo');
            file_put_contents($xmlFilePath, $xml);
            
            $isValid = $xmlProcessor->validateXml($xmlFilePath, __DIR__.'/xml/sighting_validation.xsd');
            
            if ($isValid) {
                // Process XML and save to database
                $result = $xmlProcessor->processXml($xml);
                $message['success'] = "Record successfully added via form.";
                // Clear form after successful submission
                $formData = [];
            } else {
                $message['error'] = "Generated XML is not valid according to the XSD schema.";
            }
            
            // Remove temporary file
            unlink($xmlFilePath);
        } catch (Exception $e) {
            $message['error'] = "Error processing form: " . $e->getMessage();
        }
    } else {
        $message['error'] = implode(' ', $errors);
    }
}

// Get list of available UFO shapes and seasons for the form
$ufoShapes = $ufoSighting->getDistinctValues('ufo_shape');
$seasons = $ufoSighting->getDistinctValues('season');

include __DIR__.'/../templates/header.php';
?>

<div class="container mt-4">
    <header class="page-header">
        <h2><i class="ph ph-file-plus"></i> Add a Sighting</h2>
        <p>Contribute to our database by reporting your UFO sightings. You can either fill out the form or upload an XML file.</p>
    </header>
    
    <?php if (isset($message['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($message['success']) ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($message['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($message['error']) ?>
        </div>
    <?php endif; ?>
    
    <div class="xml-operations-grid">
    <!-- XML Upload Section -->
        <div class="card">
            <h2>Upload Sighting via XML</h2>
            <p>Upload an XML file with UFO sightings. The file must be valid according to <a href="/xml/sighting_validation.xsd" target="_blank">this XSD schema</a>.</p>
            
            <form method="POST" enctype="multipart/form-data" class="xml-upload-form">
                <div class="form-group file-upload-container">
                    <label for="xml_file">XML File:</label>
                    <div class="file-input-wrapper">
                        <div class="custom-file-upload">
                            <i class="ph ph-cloud-arrow-up"></i>
                            <span id="file-name">Click to select a file</span>
                            <small>Or drag and drop file here</small>
                            <input type="file" name="xml_file" id="xml_file" accept=".xml" required>
                        </div>
                    </div>
                    <div id="file-selected-info" class="file-selected-info" style="display: none;">
                        <i class="ph ph-check-circle"></i>
                        <span>Selected file: </span>
                        <strong id="selected-file-name"></strong>
                    </div>
                </div>
                <button type="submit" name="submit_xml" class="btn btn-primary">
                    <i class="ph ph-upload"></i> Upload XML
                </button>
            </form>
        </div>
        
        <!-- HTML Form Section -->
        <div class="card">
            <h2>Add New Sighting</h2>
            <p>Fill out the form to add a new UFO sighting. The data will be converted to XML and validated before saving.</p>
            
            <form method="POST" class="sighting-form">
                <div class="form-group">
                    <label for="date_time">Date and Time of Sighting:</label>
                    <input type="datetime-local" name="date_time" id="date_time" value="<?= htmlspecialchars($formData['date_time'] ?? '') ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="ufo_shape">UFO Shape:</label>
                        <select name="ufo_shape" id="ufo_shape" required>
                            <option value="">-- Select shape --</option>
                            <?php foreach ($ufoShapes as $shape): ?>
                                <option value="<?= htmlspecialchars($shape) ?>" <?= ($formData['ufo_shape'] ?? '') === $shape ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($shape) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="season">Season:</label>
                        <select name="season" id="season" required>
                            <option value="">-- Select season --</option>
                            <?php foreach ($seasons as $season): ?>
                                <option value="<?= htmlspecialchars($season) ?>" <?= ($formData['season'] ?? '') === $season ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($season) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <input type="text" name="country" id="country" value="<?= htmlspecialchars($formData['country'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="country_code">Country Code:</label>
                        <input type="text" name="country_code" id="country_code" value="<?= htmlspecialchars($formData['country_code'] ?? '') ?>" maxlength="6">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="region">Region:</label>
                        <input type="text" name="region" id="region" value="<?= htmlspecialchars($formData['region'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="locale">Locality:</label>
                        <input type="text" name="locale" id="locale" value="<?= htmlspecialchars($formData['locale'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">Latitude:</label>
                        <input type="number" name="latitude" id="latitude" step="0.00000001" min="-90" max="90" value="<?= htmlspecialchars($formData['latitude'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="longitude">Longitude:</label>
                        <input type="number" name="longitude" id="longitude" step="0.00000001" min="-180" max="180" value="<?= htmlspecialchars($formData['longitude'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="encounter_seconds">Encounter Duration (seconds):</label>
                        <input type="number" name="encounter_seconds" id="encounter_seconds" min="1" value="<?= htmlspecialchars($formData['encounter_seconds'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="encounter_duration">Encounter Duration (text):</label>
                        <input type="text" name="encounter_duration" id="encounter_duration" value="<?= htmlspecialchars($formData['encounter_duration'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Sighting Description:</label>
                    <textarea name="description" id="description" rows="5" required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                </div>
                
                <input type="hidden" name="year" id="year" value="">
                <input type="hidden" name="month" id="month" value="">
                <input type="hidden" name="hour" id="hour" value="">
                
                <button type="submit" name="submit_form" class="btn btn-primary">
                    <i class="ph ph-floppy-disk"></i> Save Sighting
                </button>
            </form>
        </div>
    </div>
    
    <div class="card mt-4 user-guide-card">
        <h2>Průvodce uživatele</h2>
        
        <div class="tabs">
            <div class="tab-header">
                <button class="tab-btn active" data-tab="guide-tab">Jak vložit pozorování</button>
                <button class="tab-btn" data-tab="faq-tab">Často kladené otázky</button>
                <button class="tab-btn" data-tab="xml-info">O XML formátu</button>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane active" id="guide-tab">
                    <h3>Jak přidat nové pozorování UFO</h3>
                    <p>Pro přidání nového pozorování máte dvě možnosti:</p>
                    <ol>
                        <li><strong>Pomocí formuláře</strong> - Jednoduše vyplňte formulář vpravo. Všechna povinná pole jsou označena a systém vás upozorní, pokud něco chybí.</li>
                        <li><strong>Nahrání XML souboru</strong> - Pokud máte data v XML formátu, můžete je nahrát přímo. XML musí odpovídat schématu uvedenému v dokumentaci.</li>
                    </ol>
                    <p>Po úspěšném přidání uvidíte potvrzující zprávu a záznam bude přidán do databáze.</p>
                </div>
                
                <div class="tab-pane" id="faq-tab">
                    <h3>Často kladené otázky</h3>
                    <div class="faq-item">
                        <h4>Jaké údaje jsou povinné?</h4>
                        <p>Povinné jsou: datum a čas, země, souřadnice (zeměpisná šířka a délka), tvar UFO, doba setkání a popis.</p>
                    </div>
                    <div class="faq-item">
                        <h4>Jak určit přesné souřadnice místa?</h4>
                        <p>Můžete použít Google Maps nebo jinou mapovou službu k získání přesných souřadnic. Klikněte pravým tlačítkem na místo a vyberte "Co je tady?"</p>
                    </div>
                    <div class="faq-item">
                        <h4>Co když neznám přesný tvar UFO?</h4>
                        <p>Vyberte nejbližší možnost nebo "unknown" pokud si nejste jisti.</p>
                    </div>
                </div>
                
                <div class="tab-pane" id="xml-info">
                    <h3>O XML formátu</h3>
                    <p>XML (eXtensible Markup Language) je formát používaný k ukládání a přenosu strukturovaných dat.</p>
                    <p>Výhody použití XML:</p>
                    <ul>
                        <li>Standardizovaný formát pro výměnu dat</li>
                        <li>Možnost validace proti schématu</li>
                        <li>Čitelnost pro člověka i stroj</li>
                        <li>Snadná konverze do jiných formátů</li>
                    </ul>
                    <p>Náš systém používá XML ke standardizaci dat o pozorováních UFO. Všechna data jsou validována proti XSD schématu, což zajišťuje konzistenci a správnost.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript reference -->
<script src="/assets/js/add-sighting.js"></script>

<?php include __DIR__.'/../templates/footer.php'; ?>