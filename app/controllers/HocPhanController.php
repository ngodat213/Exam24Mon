<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/HocPhan.php';

class HocPhanController {
    private $db;
    private $hocPhan;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->hocPhan = new HocPhan($this->db);
    }

    public function index() {
        $result = $this->hocPhan->getAll();
        require_once __DIR__ . '/../views/hocphan/index.php';
    }
} 