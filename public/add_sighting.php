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

// Zpracování nahrání XML souboru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_xml'])) {
    if (isset($_FILES['xml_file']) && $_FILES['xml_file']['error'] === UPLOAD_ERR_OK) {
        $xmlFile = $_FILES['xml_file']['tmp_name'];
        
        try {
            // Validace XML podle XSD
            $isValid = $xmlProcessor->validateXml($xmlFile, __DIR__.'/xml/sighting_validation.xsd');
            
            if ($isValid) {
                // Zpracování XML a uložení do databáze
                $result = $xmlProcessor->processXmlFile($xmlFile);
                $message['success'] = "XML soubor byl úspěšně zpracován. Přidáno $result záznamů.";
            } else {
                $message['error'] = "XML soubor není validní podle XSD schématu.";
            }
        } catch (Exception $e) {
            $message['error'] = "Chyba při zpracování XML: " . $e->getMessage();
        }
    } else {
        $message['error'] = "Nepodařilo se nahrát soubor. Zkontrolujte typ a velikost souboru.";
    }
}

// Zpracování HTML formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_form'])) {
    // Získání dat z formuláře
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
    
    // Validace formuláře
    $errors = [];
    if (empty($formData['date_time'])) $errors[] = "Datum a čas jsou povinné.";
    if (empty($formData['country'])) $errors[] = "Země je povinná.";
    if (!is_numeric($formData['latitude']) || $formData['latitude'] < -90 || $formData['latitude'] > 90) {
        $errors[] = "Neplatná hodnota zeměpisné šířky.";
    }
    if (!is_numeric($formData['longitude']) || $formData['longitude'] < -180 || $formData['longitude'] > 180) {
        $errors[] = "Neplatná hodnota zeměpisné délky.";
    }
    
    if (empty($errors)) {
        try {
            // Generování XML z dat formuláře
            $xml = $xmlProcessor->generateXmlFromFormData($formData);
            
            // Validace vygenerovaného XML
            $xmlFilePath = tempnam(sys_get_temp_dir(), 'ufo');
            file_put_contents($xmlFilePath, $xml);
            
            $isValid = $xmlProcessor->validateXml($xmlFilePath, __DIR__.'/xml/sighting_validation.xsd');
            
            if ($isValid) {
                // Zpracování XML a uložení do databáze
                $result = $xmlProcessor->processXml($xml);
                $message['success'] = "Záznam byl úspěšně přidán přes formulář.";
                // Vyčistit formulář po úspěšném odeslání
                $formData = [];
            } else {
                $message['error'] = "Vygenerované XML není validní podle XSD schématu.";
            }
            
            // Odstranění dočasného souboru
            unlink($xmlFilePath);
        } catch (Exception $e) {
            $message['error'] = "Chyba při zpracování formuláře: " . $e->getMessage();
        }
    } else {
        $message['error'] = implode(' ', $errors);
    }
}

// Získání seznamu dostupných tvarů UFO a sezón pro formulář
$ufoShapes = $ufoSighting->getDistinctValues('ufo_shape');
$seasons = $ufoSighting->getDistinctValues('season');

include __DIR__.'/../templates/header.php';
?>

<div class="container mt-4">
    <h2><i class="ph ph-file-plus"></i> Add a sighting</h2>
    
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
            <h2>Nahrát pozorování přes XML</h2>
            <p>Nahrajte XML soubor s pozorováními UFO. Soubor musí být validní podle <a href="/xml/sighting_validation.xsd" target="_blank">tohoto XSD schématu</a>.</p>
            
            <form method="POST" enctype="multipart/form-data" class="xml-upload-form">
            <div class="form-group file-upload-container">
                <label for="xml_file">XML Soubor:</label>
                <div class="file-input-wrapper">
                    <div class="custom-file-upload">
                        <i class="ph ph-cloud-arrow-up"></i>
                        <span id="file-name">Klikněte pro výběr souboru</span>
                        <small>Nebo přetáhněte soubor sem</small>
                        <input type="file" name="xml_file" id="xml_file" accept=".xml" required>
                    </div>
                </div>
                <div id="file-selected-info" class="file-selected-info" style="display: none;">
                    <i class="ph ph-check-circle"></i>
                    <span>Vybraný soubor: </span>
                    <strong id="selected-file-name"></strong>
                </div>
            </div>
                <button type="submit" name="submit_xml" class="btn btn-primary">
                    <i class="ph ph-upload"></i> Nahrát XML
                </button>
                
            </form>
        </div>
        
        <!-- HTML Form Section -->
        <div class="card">
            <h2>Přidat nové pozorování</h2>
            <p>Vyplňte formulář pro přidání nového pozorování UFO. Data budou konvertována do XML a validována před uložením.</p>
            
            <form method="POST" class="sighting-form">
                <div class="form-group">
                    <label for="date_time">Datum a čas pozorování:</label>
                    <input type="datetime-local" name="date_time" id="date_time" value="<?= htmlspecialchars($formData['date_time'] ?? '') ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="ufo_shape">Tvar UFO:</label>
                        <select name="ufo_shape" id="ufo_shape" required>
                            <option value="">-- Vyberte tvar --</option>
                            <?php foreach ($ufoShapes as $shape): ?>
                                <option value="<?= htmlspecialchars($shape) ?>" <?= ($formData['ufo_shape'] ?? '') === $shape ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($shape) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="season">Roční období:</label>
                        <select name="season" id="season" required>
                            <option value="">-- Vyberte období --</option>
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
                        <label for="country">Země:</label>
                        <input type="text" name="country" id="country" value="<?= htmlspecialchars($formData['country'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="country_code">Kód země:</label>
                        <input type="text" name="country_code" id="country_code" value="<?= htmlspecialchars($formData['country_code'] ?? '') ?>" maxlength="6">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="region">Region:</label>
                        <input type="text" name="region" id="region" value="<?= htmlspecialchars($formData['region'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="locale">Lokalita:</label>
                        <input type="text" name="locale" id="locale" value="<?= htmlspecialchars($formData['locale'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">Zeměpisná šířka:</label>
                        <input type="number" name="latitude" id="latitude" step="0.00000001" min="-90" max="90" value="<?= htmlspecialchars($formData['latitude'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="longitude">Zeměpisná délka:</label>
                        <input type="number" name="longitude" id="longitude" step="0.00000001" min="-180" max="180" value="<?= htmlspecialchars($formData['longitude'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="encounter_seconds">Doba setkání (sekundy):</label>
                        <input type="number" name="encounter_seconds" id="encounter_seconds" min="1" value="<?= htmlspecialchars($formData['encounter_seconds'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="encounter_duration">Doba setkání (text):</label>
                        <input type="text" name="encounter_duration" id="encounter_duration" value="<?= htmlspecialchars($formData['encounter_duration'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Popis pozorování:</label>
                    <textarea name="description" id="description" rows="5" required><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                </div>
                
                <input type="hidden" name="year" id="year" value="">
                <input type="hidden" name="month" id="month" value="">
                <input type="hidden" name="hour" id="hour" value="">
                
                <button type="submit" name="submit_form" class="btn btn-primary">
                    <i class="ph ph-floppy-disk"></i> Uložit pozorování
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