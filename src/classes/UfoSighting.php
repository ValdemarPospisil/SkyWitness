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

    public function getPaginated($limit, $offset) {
        $query = "SELECT * FROM ufo_sightings ORDER BY date_time DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        
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
}