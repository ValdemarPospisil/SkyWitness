<?php
class XmlProcessor {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }
}