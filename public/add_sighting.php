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
        <h2>User Guide</h2>
        
        <div class="tabs">
            <div class="tab-header">
                <button class="tab-btn active" data-tab="guide-tab">How to Add a Sighting</button>
                <button class="tab-btn" data-tab="faq-tab">Frequently Asked Questions</button>
                <button class="tab-btn" data-tab="xml-info">About XML Format</button>
                <button class="tab-btn" data-tab="xml-example">XML Example</button>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane active" id="guide-tab">
                    <h3>How to Add a New UFO Sighting</h3>
                    <p>You have two options for adding a new sighting:</p>
                    <ol>
                        <li><strong>Using the form</strong> - Simply fill out the form on the right. All required fields are marked, and the system will alert you if anything is missing.</li>
                        <li><strong>Uploading an XML file</strong> - If you have data in XML format, you can upload it directly. The XML must conform to the schema described in the documentation.</li>
                    </ol>
                    <p>After a successful submission, you'll see a confirmation message and the record will be added to the database.</p>
                </div>
                
                <div class="tab-pane" id="faq-tab">
                    <h3>Frequently Asked Questions</h3>
                    <div class="faq-item">
                        <h4>Which fields are required?</h4>
                        <p>Required fields are: date and time, country, coordinates (latitude and longitude), UFO shape, encounter duration, and description.</p>
                    </div>
                    <div class="faq-item">
                        <h4>How to determine exact location coordinates?</h4>
                        <p>You can use Google Maps or another mapping service to get precise coordinates. Right-click on the location and select "What's here?"</p>
                    </div>
                    <div class="faq-item">
                        <h4>What if I don't know the exact UFO shape?</h4>
                        <p>Select the closest option or "unknown" if you're unsure.</p>
                    </div>
                </div>
                
                <div class="tab-pane" id="xml-info">
                    <h3>About XML Format</h3>
                    <p>XML (eXtensible Markup Language) is a format used for storing and transferring structured data.</p>
                    <p>Benefits of using XML:</p>
                    <ul>
                        <li>Standardized format for data exchange</li>
                        <li>Validation against a schema</li>
                        <li>Human and machine readable</li>
                        <li>Easy conversion to other formats</li>
                    </ul>
                    <p>Our system uses XML to standardize UFO sighting data. All data is validated against an XSD schema, which ensures consistency and correctness.</p>
                </div>
                
                <div class="tab-pane" id="xml-example">
                    <h3>XML Structure Example</h3>
                    <p>Below is an example of the XML structure required for UFO sighting submissions. You can view the full example file <a href="/xml/ufo_sightings.xml" target="_blank">here</a>.</p>
                    <pre class="code-block"><?= htmlspecialchars(file_get_contents(__DIR__.'/xml/example.xml')) ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript reference -->
<script src="/assets/js/add-sighting.js"></script>

<?php include __DIR__.'/../templates/footer.php'; ?>