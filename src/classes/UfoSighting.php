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
}