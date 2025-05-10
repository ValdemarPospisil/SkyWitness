<?php
class UfoSighting {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAll($limit = 100) {
        return $this->db->query(
            "SELECT * FROM ufo_sightings ORDER BY date_time DESC LIMIT :limit",
            ['limit' => $limit]
        )->fetchAll();
    }

    public function getById($id) {
        return $this->db->query(
            "SELECT * FROM ufo_sightings WHERE id = :id",
            ['id' => $id]
        )->fetch();
    }
    
    public function getByIds(array $ids) {
        if (empty($ids)) {
            return [];
        }
        
        // Create placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "SELECT * FROM ufo_sightings WHERE id IN ($placeholders) ORDER BY date_time DESC";
        $stmt = $this->db->prepare($query);
        
        // Bind each ID as a parameter
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaginated($limit, $offset) {
        $query = "SELECT * FROM ufo_sightings ORDER BY date_time DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) AS total FROM ufo_sightings";
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn();
    }

    /**
     * Získá záznamy s aplikovanými filtry
     */
    public function getPaginatedWithFilters($limit, $offset, $filters) {
        $query = "SELECT * FROM ufo_sightings WHERE 1=1";
        $params = [];

        // Přidáme podmínky podle filtrů
        $query .= $this->buildFilterConditions($filters, $params);
        
        // Přidáme řazení a limit
        $query .= " ORDER BY date_time DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        
        // Nastavíme parametry pro filtry
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Nastavíme parametry pro limit a offset
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets records with applied filters and sorting
     */
    public function getPaginatedWithFiltersAndSort($limit, $offset, $filters, $sortColumn, $sortOrder) {
        // Validate sort column and order to prevent SQL injection
        $allowedColumns = ['date_time', 'country', 'region', 'ufo_shape', 'encounter_seconds'];
        $sortColumn = in_array($sortColumn, $allowedColumns) ? $sortColumn : 'date_time';
        $sortOrder = ($sortOrder === 'ASC') ? 'ASC' : 'DESC';
        
        $query = "SELECT * FROM ufo_sightings WHERE 1=1";
        $params = [];

        // Add filter conditions
        $query .= $this->buildFilterConditions($filters, $params);
        
        // Add sorting, limit and offset
        $query .= " ORDER BY $sortColumn $sortOrder LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        
        // Set parameters for filters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Set parameters for limit and offset
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Spočítá celkový počet záznamů s aplikovanými filtry
     */
    public function getTotalCountWithFilters($filters) {
        $query = "SELECT COUNT(*) AS total FROM ufo_sightings WHERE 1=1";
        $params = [];

        // Přidáme podmínky podle filtrů
        $query .= $this->buildFilterConditions($filters, $params);
        
        $stmt = $this->db->prepare($query);
        
        // Nastavíme parametry pro filtry
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Konstruuje SQL podmínky pro filtry
     */
    private function buildFilterConditions($filters, &$params) {
        $conditions = "";
        
        // Country - multi-select
        if (!empty($filters['country'])) {
            $placeholders = [];
            foreach ($filters['country'] as $index => $country) {
                $paramName = ":country_$index";
                $placeholders[] = $paramName;
                $params[$paramName] = $country;
            }
            if ($placeholders) {
                $conditions .= " AND country IN (" . implode(',', $placeholders) . ")";
            }
        }
        
        // Shape - multi-select
        if (!empty($filters['shape'])) {
            $placeholders = [];
            foreach ($filters['shape'] as $index => $shape) {
                $paramName = ":shape_$index";
                $placeholders[] = $paramName;
                $params[$paramName] = $shape;
            }
            if ($placeholders) {
                $conditions .= " AND ufo_shape IN (" . implode(',', $placeholders) . ")";
            }
        }
        
        // Year range
        if (!empty($filters['year_min'])) {
            $conditions .= " AND year >= :year_min";
            $params[':year_min'] = $filters['year_min'];
        }
        
        if (!empty($filters['year_max'])) {
            $conditions .= " AND year <= :year_max";
            $params[':year_max'] = $filters['year_max'];
        }
        
        // Month range
        if (!empty($filters['month_min'])) {
            $conditions .= " AND month >= :month_min";
            $params[':month_min'] = $filters['month_min'];
        }
        
        if (!empty($filters['month_max'])) {
            $conditions .= " AND month <= :month_max";
            $params[':month_max'] = $filters['month_max'];
        }
        
        // Hour range
        if (!empty($filters['hour_min'])) {
            $conditions .= " AND hour >= :hour_min";
            $params[':hour_min'] = $filters['hour_min'];
        }
        
        if (!empty($filters['hour_max'])) {
            $conditions .= " AND hour <= :hour_max";
            $params[':hour_max'] = $filters['hour_max'];
        }
        
        // Season - multi-select
        if (!empty($filters['season'])) {
            $placeholders = [];
            foreach ($filters['season'] as $index => $season) {
                $paramName = ":season_$index";
                $placeholders[] = $paramName;
                $params[$paramName] = $season;
            }
            if ($placeholders) {
                $conditions .= " AND season IN (" . implode(',', $placeholders) . ")";
            }
        }
        
        return $conditions;
    }
    
    public function getDistinctValues($column) {
        $query = "SELECT DISTINCT {$column} FROM ufo_sightings WHERE {$column} IS NOT NULL ORDER by {$column}";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
