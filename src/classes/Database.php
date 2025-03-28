<?php
class Database {
    private $connection;

    public function __construct($host, $dbname, $username, $password) {
        $this->connection = new PDO(
            "pgsql:host=$host;dbname=$dbname",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}