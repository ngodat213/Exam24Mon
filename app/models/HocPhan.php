<?php
class HocPhan {
    private $conn;
    private $table = "HocPhan";

    public $MaHP;
    public $TenHP;
    public $SoTinChi;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $result = $this->conn->query($query);
        return $result;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE MaHP = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }
} 