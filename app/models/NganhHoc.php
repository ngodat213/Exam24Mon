<?php
class NganhHoc {
    private $conn;
    private $table = "NganhHoc";

    public $MaNganh;
    public $TenNganh;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $result = $this->conn->query($query);
        return $result;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE MaNganh = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }
} 