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

        // Initialize variables
        $availableCourses = null;
        $totalCourses = 0;
        $totalCredits = 0;

        // Get selected courses from session
        if (isset($_SESSION['selected_courses']) && is_array($_SESSION['selected_courses']) && !empty($_SESSION['selected_courses'])) {
            $placeholders = str_repeat('?,', count($_SESSION['selected_courses']) - 1) . '?';
            $sql = "SELECT * FROM HocPhan WHERE MaHP IN ($placeholders)";
            $stmt = $this->conn->prepare($sql);
            
            // Create array of parameters for bind_param
            $types = str_repeat('s', count($_SESSION['selected_courses']));
            $params = array_merge(array($types), $_SESSION['selected_courses']);
            
            // Use call_user_func_array to bind parameters
            call_user_func_array(array($stmt, 'bind_param'), $params);
            
            $stmt->execute();
            $availableCourses = $stmt->get_result();
        } else {
            // Create empty result set if no courses selected
            $sql = "SELECT * FROM HocPhan WHERE 1=0";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $availableCourses = $stmt->get_result();
        }

        // Get registered courses with registration details
        $sql = "SELECT hp.*, dk.NgayDK, dk.MaDK 
                FROM HocPhan hp 
                JOIN ChiTietDangKy ct ON hp.MaHP = ct.MaHP 
                JOIN DangKy dk ON ct.MaDK = dk.MaDK 
                WHERE dk.MaSV = ?
                ORDER BY dk.NgayDK DESC, hp.MaHP ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $masv);
        $stmt->execute();
        $registeredCourses = $stmt->get_result();

        // Calculate total credits
        $totalCredits = 0;
        $registeredCoursesArray = [];
        while ($row = $registeredCourses->fetch_assoc()) {
            $totalCredits += $row['SoTinChi'];
            $registeredCoursesArray[] = $row;
        }

        require_once 'app/views/dangkyhocphan/index.php';
    }

    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['MaHP'])) {
            header('Location: index.php?controller=hocphan');
            exit;
        }

        $mahp = $_POST['MaHP'];
        
        // Initialize session array if not exists
        if (!isset($_SESSION['selected_courses']) || !is_array($_SESSION['selected_courses'])) {
            $_SESSION['selected_courses'] = array();
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

        // Check if course is already in cart
        if (!in_array($mahp, $_SESSION['selected_courses'])) {
            // Get course details to verify it exists
            $sql = "SELECT * FROM HocPhan WHERE MaHP = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $mahp);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $_SESSION['selected_courses'][] = $mahp;
                $_SESSION['cart_updated'] = true;
                header('Location: index.php?controller=hocphan&success=added');
            } else {
                header('Location: index.php?controller=hocphan&error=course_not_found');
            }
        } else {
            header('Location: index.php?controller=hocphan&error=already_in_cart');
        }
        exit;
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
        exit;
    }

    public function clearCart() {
        if (isset($_SESSION['selected_courses'])) {
            $_SESSION['selected_courses'] = array();
            $_SESSION['cart_updated'] = true;
        }
        header('Location: index.php?controller=dangkyhocphan');
        exit;
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
        exit;
    }

    public function delete() {
        if (!isset($_GET['madk']) || !isset($_GET['mahp'])) {
            header('Location: index.php?controller=dangkyhocphan&error=invalid_request');
            exit;
        }

        $madk = $_GET['madk'];
        $mahp = $_GET['mahp'];
        $masv = $_SESSION['MaSV'];

        // Verify ownership of registration
        $sql = "SELECT 1 FROM DangKy WHERE MaDK = ? AND MaSV = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $madk, $masv);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            header('Location: index.php?controller=dangkyhocphan&error=unauthorized');
            exit;
        }

        // Start transaction
        $this->conn->begin_transaction();

        try {
            // Delete from ChiTietDangKy
            $sql = "DELETE FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $madk, $mahp);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting course registration");
            }

            // Check if this was the last course in this registration
            $sql = "SELECT 1 FROM ChiTietDangKy WHERE MaDK = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $madk);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                // Delete the registration if no courses left
                $sql = "DELETE FROM DangKy WHERE MaDK = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("i", $madk);
                if (!$stmt->execute()) {
                    throw new Exception("Error deleting empty registration");
                }
            }

            $this->conn->commit();
            header('Location: index.php?controller=dangkyhocphan&success=deleted');
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Delete registration error: " . $e->getMessage());
            header('Location: index.php?controller=dangkyhocphan&error=delete_failed');
        }
        exit;
    }
} 