<?php
class HocPhanController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function index() {
        $masv = isset($_SESSION['MaSV']) ? $_SESSION['MaSV'] : null;
        if (!$masv) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Get all available courses with remaining slots
        $sql = "SELECT hp.* FROM HocPhan hp 
                WHERE hp.MaHP NOT IN (
                    SELECT ct.MaHP 
                    FROM DangKy dk 
                    JOIN ChiTietDangKy ct ON dk.MaDK = ct.MaDK 
                    WHERE dk.MaSV = ?
                )
                AND hp.SoLuong > 0
                ORDER BY hp.MaHP ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $masv);
        $stmt->execute();
        $availableCourses = $stmt->get_result();

        // Get already registered courses
        $sql = "SELECT hp.* 
                FROM HocPhan hp 
                JOIN ChiTietDangKy ct ON hp.MaHP = ct.MaHP 
                JOIN DangKy dk ON ct.MaDK = dk.MaDK 
                WHERE dk.MaSV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $masv);
        $stmt->execute();
        $registeredCourses = $stmt->get_result();

        require_once 'app/views/hocphan/index.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['MaHP'])) {
            header('Location: index.php?controller=hocphan');
            exit;
        }

        $masv = $_SESSION['MaSV'];
        $mahp = $_POST['MaHP'];

        // Check if course exists
        $sql = "SELECT * FROM HocPhan WHERE MaHP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $mahp);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            header('Location: index.php?controller=hocphan&error=course_not_found');
            exit;
        }

        // Check if already registered
        $sql = "SELECT 1 FROM DangKy dk 
                JOIN ChiTietDangKy ct ON dk.MaDK = ct.MaDK 
                WHERE dk.MaSV = ? AND ct.MaHP = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $masv, $mahp);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            header('Location: index.php?controller=hocphan&error=already_registered');
            exit;
        }

        // Start transaction
        $this->conn->begin_transaction();

        try {
            // Create new registration
            $sql = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $masv);
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating registration");
            }
            
            $madk = $this->conn->insert_id;
            
            // Add course to registration
            $sql = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $madk, $mahp);
            
            if (!$stmt->execute()) {
                throw new Exception("Error adding course to registration");
            }

            $this->conn->commit();
            header('Location: index.php?controller=hocphan&success=registered');
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Registration error: " . $e->getMessage());
            header('Location: index.php?controller=hocphan&error=registration_failed');
        }
        exit;
    }
} 