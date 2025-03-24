<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/SinhVien.php';

class AuthController {
    private $db;
    private $sinhVien;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->sinhVien = new SinhVien($this->db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $masv = $_POST['MaSV'];
            $result = $this->sinhVien->getById($masv);
            
            if ($result->num_rows > 0) {
                $_SESSION['MaSV'] = $masv;
                header("Location: index.php?controller=sinhvien&action=index");
                exit();
            }
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
} 