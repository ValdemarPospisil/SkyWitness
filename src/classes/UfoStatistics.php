<?php
/**
 * UfoStatistics.php - Class for retrieving UFO sighting statistics from the database
 */
class UfoStatistics {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    /**
     * Get top countries with the most sightings
     * @param int $limit Number of countries to return
     * @return array Array of countries with sighting counts
     */
    public function getTopCountries($limit = 5) {
        $query = "SELECT country, COUNT(*) as count 
                 FROM ufo_sightings 
                 WHERE country IS NOT NULL AND country != '' 
                 GROUP BY country 
                 ORDER BY count DESC 
                 LIMIT :limit";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get most common UFO shapes
     * @param int $limit Number of shapes to return
     * @return array Array of shapes with sighting counts
     */
    public function getCommonShapes($limit = 5) {
        $query = "SELECT ufo_shape, COUNT(*) as count 
                 FROM ufo_sightings 
                 WHERE ufo_shape IS NOT NULL AND ufo_shape != '' 
                 GROUP BY ufo_shape 
                 ORDER BY count DESC 
                 LIMIT :limit";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get most active seasons
     * @return array Array of seasons with sighting counts
     */
    public function getSightingsBySeasons() {
        $query = "SELECT season, COUNT(*) as count 
                 FROM ufo_sightings 
                 WHERE season IS NOT NULL AND season != '' 
                 GROUP BY season 
                 ORDER BY count DESC";
                 
        return $this->db->query($query)->fetchAll();
    }

    /**
     * Get sightings count by year for the last 10 years
     * @param int $years Number of years to include
     * @return array Array of years with sighting counts
     */
    public function getSightingsByYear($years = 10) {
        $query = "SELECT year, COUNT(*) as count 
                 FROM ufo_sightings 
                 WHERE year IS NOT NULL
                 GROUP BY year 
                 ORDER BY year DESC 
                 LIMIT :years";
                 
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':years', $years, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get sightings count by hour of day
     * @return array Array of hours with sighting counts
     */
    public function getSightingsByHour() {
        $query = "SELECT hour, COUNT(*) as count 
                 FROM ufo_sightings 
                 WHERE hour IS NOT NULL
                 GROUP BY hour 
                 ORDER BY hour ASC";
                 
        return $this->db->query($query)->fetchAll();
    }

    /**
     * Get average sighting duration in seconds
     * @return float Average duration
     */
    public function getAverageDuration() {
        $query = "SELECT AVG(encounter_seconds) as avg_duration 
                 FROM ufo_sightings 
                 WHERE encounter_seconds > 0 AND encounter_seconds < 86400"; // Filter out unreasonable values (>24 hours)
                 
        return $this->db->query($query)->fetchColumn();
    }

    /**
     * Get random sighting
     * @return array Random sighting record
     */
    public function getRandomSighting() {
        // Using TABLESAMPLE is more efficient for large tables than ORDER BY RANDOM()
        $query = "SELECT * FROM ufo_sightings TABLESAMPLE SYSTEM(1) 
                 WHERE description IS NOT NULL AND description != '' 
                 LIMIT 1";
                 
        $result = $this->db->query($query)->fetch();
        
        // Fallback if TABLESAMPLE doesn't return results
        if (!$result) {
            $query = "SELECT * FROM ufo_sightings 
                     WHERE description IS NOT NULL AND description != '' 
                     ORDER BY RANDOM() 
                     LIMIT 1";
            $result = $this->db->query($query)->fetch();
        }
        
        return $result;
    }

    /**
     * Get total number of sightings
     * @return int Total count
     */
    public function getTotalSightings() {
        $query = "SELECT COUNT(*) FROM ufo_sightings";
        return $this->db->query($query)->fetchColumn();
    }

}