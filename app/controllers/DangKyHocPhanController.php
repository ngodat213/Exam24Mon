<?php
class DangKyHocPhanController {
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

        // Get student information
        $sql = "SELECT sv.*, nh.TenNganh 
                FROM SinhVien sv 
                LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh 
                WHERE sv.MaSV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $masv);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        
        // Store student info in session
        $_SESSION['HoTen'] = $student['HoTen'];
        $_SESSION['NgaySinh'] = $student['NgaySinh'];
        $_SESSION['MaNganh'] = $student['MaNganh'];

        // Get all courses for selection
        $sql = "SELECT * FROM HocPhan";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $availableCourses = $stmt->get_result();

        require_once 'app/views/dangkyhocphan/index.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=dangkyhocphan');
            exit;
        }

        $masv = $_SESSION['MaSV'];
        $selectedCourses = isset($_SESSION['selected_courses']) ? $_SESSION['selected_courses'] : [];
        
        if (empty($selectedCourses)) {
            header('Location: index.php?controller=dangkyhocphan&error=no_courses');
            exit;
        }

        // Start transaction
        $this->conn->begin_transaction();

        try {
            // Create new DangKy record
            $sql = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $masv);
            
            if (!$stmt->execute()) {
                throw new Exception("Error inserting into DangKy: " . $stmt->error);
            }
            
            $madk = $this->conn->insert_id;
            if (!$madk) {
                throw new Exception("No MaDK was generated");
            }

            // Create ChiTietDangKy records for each selected course
            $sql = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            foreach ($selectedCourses as $mahp) {
                $stmt->bind_param("is", $madk, $mahp);
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting into ChiTietDangKy: " . $stmt->error);
                }
            }

            // Clear selected courses from session after successful registration
            unset($_SESSION['selected_courses']);
            $_SESSION['registration_success'] = true;
            $_SESSION['last_registration'] = [
                'madk' => $madk,
                'courses' => $selectedCourses,
                'date' => date('Y-m-d')
            ];

            $this->conn->commit();
            header('Location: index.php?controller=dangkyhocphan&success=1');
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Registration error: " . $e->getMessage());
            header('Location: index.php?controller=dangkyhocphan&error=1&msg=' . urlencode($e->getMessage()));
        }
    }

    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['MaHP'])) {
            header('Location: index.php?controller=hocphan');
            exit;
        }

        $mahp = $_POST['MaHP'];
        
        if (!isset($_SESSION['selected_courses'])) {
            $_SESSION['selected_courses'] = [];
        }

        // Check if course is already registered
        $masv = $_SESSION['MaSV'];
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

        // Add to session if not already there
        if (!in_array($mahp, $_SESSION['selected_courses'])) {
            $_SESSION['selected_courses'][] = $mahp;
            $_SESSION['cart_updated'] = true;
        }

        header('Location: index.php?controller=dangkyhocphan');
    }

    public function removeFromCart() {
        if (!isset($_GET['mahp'])) {
            header('Location: index.php?controller=dangkyhocphan');
            exit;
        }

        $mahp = $_GET['mahp'];
        
        if (isset($_SESSION['selected_courses'])) {
            $key = array_search($mahp, $_SESSION['selected_courses']);
            if ($key !== false) {
                unset($_SESSION['selected_courses'][$key]);
                $_SESSION['selected_courses'] = array_values($_SESSION['selected_courses']);
                $_SESSION['cart_updated'] = true;
            }
        }

        header('Location: index.php?controller=dangkyhocphan');
    }

    public function delete() {
        if (!isset($_GET['mahp'])) {
            header('Location: index.php?controller=dangkyhocphan');
            exit;
        }

        $masv = $_SESSION['MaSV'];
        $mahp = $_GET['mahp'];

        // Start transaction
        $this->conn->begin_transaction();

        try {
            // First get the MaDK
            $sql = "SELECT dk.MaDK 
                   FROM DangKy dk 
                   JOIN ChiTietDangKy ct ON dk.MaDK = ct.MaDK 
                   WHERE dk.MaSV = ? AND ct.MaHP = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $masv, $mahp);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row) {
                $madk = $row['MaDK'];
                
                // Delete from ChiTietDangKy
                $sql = "DELETE FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("is", $madk, $mahp);
                $stmt->execute();

                // Check if this was the last course for this registration
                $sql = "SELECT COUNT(*) as count FROM ChiTietDangKy WHERE MaDK = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $madk);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_assoc()['count'];

                // If no more courses, delete the DangKy record
                if ($count == 0) {
                    $sql = "DELETE FROM DangKy WHERE MaDK = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("i", $madk);
                    $stmt->execute();
                }
            }

            $this->conn->commit();
            header('Location: index.php?controller=dangkyhocphan&success=2');
        } catch (Exception $e) {
            $this->conn->rollback();
            header('Location: index.php?controller=dangkyhocphan&error=2');
        }
    }
} 