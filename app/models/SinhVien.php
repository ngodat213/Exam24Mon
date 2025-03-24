<?php
class SinhVien {
    private $conn;
    private $table = "SinhVien";

    public $MaSV;
    public $HoTen;
    public $GioiTinh;
    public $NgaySinh;
    public $Hinh;
    public $MaNganh;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT sv.*, nh.TenNganh 
                FROM " . $this->table . " sv
                LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh";
        $result = $this->conn->query($query);
        return $result;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT sv.*, nh.TenNganh 
                FROM " . $this->table . " sv
                LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
                WHERE sv.MaSV = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function create() {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table . "
                (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh)
                VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssss", 
            $this->MaSV, 
            $this->HoTen, 
            $this->GioiTinh, 
            $this->NgaySinh,
            $this->Hinh,
            $this->MaNganh
        );

        return $stmt->execute();
    }

    public function update() {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . "
                SET HoTen = ?, GioiTinh = ?, NgaySinh = ?, Hinh = ?, MaNganh = ?
                WHERE MaSV = ?");
        
        $stmt->bind_param("ssssss",
            $this->HoTen,
            $this->GioiTinh,
            $this->NgaySinh,
            $this->Hinh,
            $this->MaNganh,
            $this->MaSV
        );

        return $stmt->execute();
    }

    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE MaSV = ?");
        $stmt->bind_param("s", $this->MaSV);
        return $stmt->execute();
    }
} 