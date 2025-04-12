<?php
class XmlProcessor {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Validuje XML soubor proti XSD schématu
     * 
     * @param string $xmlFile Cesta k XML souboru
     * @param string $xsdFile Cesta k XSD schématu
     * @return bool True pokud je XML validní, jinak false
     * @throws Exception Při chybě validace
     */
    public function validateXml($xmlFile, $xsdFile) {
        libxml_use_internal_errors(true);
        
        $xml = new DOMDocument();
        $xml->load($xmlFile);
        
        if (!$xml->schemaValidate($xsdFile)) {
            $errors = libxml_get_errors();
            $errorMessages = [];
            
            foreach ($errors as $error) {
                $errorMessages[] = "Řádek {$error->line}: {$error->message}";
            }
            
            libxml_clear_errors();
            throw new Exception("XML validace selhala: " . implode(", ", $errorMessages));
        }
        
        return true;
    }

    /**
     * Zpracuje XML soubor a uloží data do databáze
     * 
     * @param string $xmlFile Cesta k XML souboru
     * @return int Počet zpracovaných záznamů
     * @throws Exception Při chybě zpracování
     */
    public function processXmlFile($xmlFile) {
        $xml = new SimpleXMLElement(file_get_contents($xmlFile));
        return $this->processXml($xml->asXML());
    }

    /**
     * Zpracuje XML řetězec a uloží data do databáze
     * 
     * @param string $xml XML řetězec
     * @return int Počet zpracovaných záznamů
     * @throws Exception Při chybě zpracování
     */
    public function processXml($xml) {
        $doc = new SimpleXMLElement($xml);
        $counter = 0;
        
        foreach ($doc->sighting as $sighting) {
            $dateTime = (string)$sighting->{'date-time'};
            $dateObj = new DateTime($dateTime);
            
            $data = [
                'date_time' => $dateObj->format('Y-m-d H:i:s'),
                'date_documented' => (string)$sighting->{'date-documented'},
                'year' => (int)$sighting->year,
                'month' => (int)$sighting->month,
                'hour' => (int)$sighting->hour,
                'season' => (string)$sighting->season,
                'country_code' => (string)$sighting->{'country-code'},
                'country' => (string)$sighting->country,
                'region' => (string)$sighting->region,
                'locale' => (string)$sighting->locale,
                'latitude' => (float)$sighting->latitude,
                'longitude' => (float)$sighting->longitude,
                'ufo_shape' => (string)$sighting->{'ufo-shape'},
                'encounter_seconds' => (int)$sighting->{'encounter-seconds'},
                'encounter_duration' => (string)$sighting->{'encounter-duration'},
                'description' => (string)$sighting->description
            ];
            
            // SQL pro vložení dat - odstraněno ID pole, aby se vygenerovalo automaticky
            $sql = "INSERT INTO ufo_sightings (
                date_time, date_documented, year, month, hour, season, 
                country_code, country, region, locale, latitude, longitude, 
                ufo_shape, encounter_seconds, encounter_duration, description
            ) VALUES (
                :date_time, :date_documented, :year, :month, :hour, :season,
                :country_code, :country, :region, :locale, :latitude, :longitude,
                :ufo_shape, :encounter_seconds, :encounter_duration, :description
            )";
            
            $this->db->query($sql, $data);
            $counter++;
        }
        
        return $counter;
    }

    /**
     * Generuje XML z dat formuláře
     * 
     * @param array $formData Data z formuláře
     * @return string XML řetězec
     */
    public function generateXmlFromFormData($formData) {
        $dateTime = new DateTime($formData['date_time']);
        $now = new DateTime();
        
        // Pokud year, month nebo hour nejsou nastaveny, doplníme je z datumu
        if (empty($formData['year'])) {
            $formData['year'] = $dateTime->format('Y');
        }
        if (empty($formData['month'])) {
            $formData['month'] = $dateTime->format('n');
        }
        if (empty($formData['hour'])) {
            $formData['hour'] = $dateTime->format('G');
        }
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ufo-sightings></ufo-sightings>');
        $sighting = $xml->addChild('sighting');
        
        $sighting->addChild('date-time', $dateTime->format('Y-m-d\TH:i:s'));
        $sighting->addChild('date-documented', $now->format('Y-m-d\TH:i:s'));
        $sighting->addChild('year', $formData['year']);
        $sighting->addChild('month', $formData['month']);
        $sighting->addChild('hour', $formData['hour']);
        $sighting->addChild('season', $formData['season']);
        $sighting->addChild('country-code', $formData['country_code']);
        $sighting->addChild('country', $formData['country']);
        $sighting->addChild('region', $formData['region']);
        $sighting->addChild('locale', $formData['locale']);
        $sighting->addChild('latitude', $formData['latitude']);
        $sighting->addChild('longitude', $formData['longitude']);
        $sighting->addChild('ufo-shape', $formData['ufo_shape']);
        $sighting->addChild('encounter-seconds', $formData['encounter_seconds']);
        $sighting->addChild('encounter-duration', $formData['encounter_duration']);
        $sighting->addChild('description', $formData['description']);
        
        return $xml->asXML();
    }
}