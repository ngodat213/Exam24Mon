<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/SinhVien.php';
require_once __DIR__ . '/../models/NganhHoc.php';

class SinhVienController {
    private $db;
    private $sinhVien;
    private $nganhHoc;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->sinhVien = new SinhVien($this->db);
        $this->nganhHoc = new NganhHoc($this->db);
    }

    private function uploadImage($file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileName = basename($file['name']);
            $targetPath = 'Content/images/' . $fileName;
            
            // Kiểm tra và tạo thư mục nếu chưa tồn tại
            if (!file_exists('Content/images')) {
                mkdir('Content/images', 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $targetPath;  // Trả về đường dẫn không có dấu / ở đầu
            }
        }
        return null;
    }

    public function index() {
        $result = $this->sinhVien->getAll();
        require_once __DIR__ . '/../views/sinhvien/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sinhVien->MaSV = $_POST['MaSV'];
            $this->sinhVien->HoTen = $_POST['HoTen'];
            $this->sinhVien->GioiTinh = $_POST['GioiTinh'];
            $this->sinhVien->NgaySinh = $_POST['NgaySinh'];
            $this->sinhVien->MaNganh = $_POST['MaNganh'];

            if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->uploadImage($_FILES['Hinh']);
                if ($imagePath) {
                    $this->sinhVien->Hinh = $imagePath;
                }
            }

            if ($this->sinhVien->create()) {
                header("Location: index.php?controller=sinhvien&action=index");
                exit();
            }
        }
        $nganh_result = $this->nganhHoc->getAll();
        require_once __DIR__ . '/../views/sinhvien/create.php';
    }

    public function edit($id) {
        $result = $this->sinhVien->getById($id);
        $student = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sinhVien->MaSV = $id;
            $this->sinhVien->HoTen = $_POST['HoTen'];
            $this->sinhVien->GioiTinh = $_POST['GioiTinh'];
            $this->sinhVien->NgaySinh = $_POST['NgaySinh'];
            $this->sinhVien->MaNganh = $_POST['MaNganh'];
            $this->sinhVien->Hinh = $student['Hinh'];

            if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] === UPLOAD_ERR_OK) {
                // Xóa ảnh cũ nếu tồn tại
                if ($student['Hinh'] && file_exists($student['Hinh'])) {
                    unlink($student['Hinh']);
                }
                
                $imagePath = $this->uploadImage($_FILES['Hinh']);
                if ($imagePath) {
                    $this->sinhVien->Hinh = $imagePath;
                }
            }

            if ($this->sinhVien->update()) {
                header("Location: index.php?controller=sinhvien&action=index");
                exit();
            }
        }
        require_once __DIR__ . '/../views/sinhvien/edit.php';
    }

    public function delete($id) {
        $result = $this->sinhVien->getById($id);
        $student = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xóa file ảnh trước khi xóa record
            if ($student['Hinh'] && file_exists($student['Hinh'])) {
                unlink($student['Hinh']);
            }
            
            $this->sinhVien->MaSV = $id;
            if ($this->sinhVien->delete()) {
                header("Location: index.php?controller=sinhvien&action=index");
                exit();
            }
        }
        require_once __DIR__ . '/../views/sinhvien/delete.php';
    }

    public function details($id) {
        $result = $this->sinhVien->getById($id);
        $student = $result->fetch_assoc();
        require_once __DIR__ . '/../views/sinhvien/details.php';
    }
} 