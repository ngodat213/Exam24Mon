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
        $masv = $_SESSION['MaSV'];

        $this->conn->begin_transaction();

        try {
            // Check if course is already registered
            $sql = "SELECT 1 FROM DangKy dk 
                    JOIN ChiTietDangKy ct ON dk.MaDK = ct.MaDK 
                    WHERE dk.MaSV = ? AND ct.MaHP = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $masv, $mahp);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Học phần đã được đăng ký");
            }

            // Check available slots
            $sql = "SELECT SoLuong FROM HocPhan WHERE MaHP = ? FOR UPDATE";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $mahp);
            $stmt->execute();
            $result = $stmt->get_result();
            $course = $result->fetch_assoc();
            
            if ($course['SoLuong'] <= 0) {
                throw new Exception("Học phần đã hết chỗ");
            }

            // Create new DangKy record
            $sql = "INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $masv);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi tạo đăng ký");
            }
            
            $madk = $this->conn->insert_id;

            // Add course to ChiTietDangKy
            $sql = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $madk, $mahp);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi thêm chi tiết đăng ký");
            }

            // Update available slots
            $sql = "UPDATE HocPhan SET SoLuong = SoLuong - 1 WHERE MaHP = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $mahp);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật số lượng");
            }

            $this->conn->commit();
            header('Location: index.php?controller=hocphan&success=registered');
        } catch (Exception $e) {
            $this->conn->rollback();
            header('Location: index.php?controller=hocphan&error=' . urlencode($e->getMessage()));
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
            // Check available slots for all selected courses
            foreach ($selectedCourses as $mahp) {
                $sql = "SELECT SoLuong FROM HocPhan WHERE MaHP = ? FOR UPDATE";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $mahp);
                $stmt->execute();
                $result = $stmt->get_result();
                $course = $result->fetch_assoc();
                
                if ($course['SoLuong'] <= 0) {
                    throw new Exception("Học phần $mahp đã hết chỗ");
                }
            }

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

            // Create ChiTietDangKy records and update slots for each selected course
            $sql = "INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            $updateSql = "UPDATE HocPhan SET SoLuong = SoLuong - 1 WHERE MaHP = ?";
            $updateStmt = $this->conn->prepare($updateSql);
            
            foreach ($selectedCourses as $mahp) {
                // Add to ChiTietDangKy
                $stmt->bind_param("is", $madk, $mahp);
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting into ChiTietDangKy: " . $stmt->error);
                }
                
                // Update available slots
                $updateStmt->bind_param("s", $mahp);
                if (!$updateStmt->execute()) {
                    throw new Exception("Error updating slots for course: " . $mahp);
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

        $this->conn->begin_transaction();

        try {
            // Delete from ChiTietDangKy
            $sql = "DELETE FROM ChiTietDangKy WHERE MaDK = ? AND MaHP = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $madk, $mahp);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi xóa đăng ký học phần");
            }

            // Increase available slots
            $sql = "UPDATE HocPhan SET SoLuong = SoLuong + 1 WHERE MaHP = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $mahp);
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi cập nhật số lượng");
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
                    throw new Exception("Lỗi khi xóa đăng ký trống");
                }
            }

            $this->conn->commit();
            header('Location: index.php?controller=dangkyhocphan&success=deleted');
        } catch (Exception $e) {
            $this->conn->rollback();
            header('Location: index.php?controller=dangkyhocphan&error=delete_failed');
        }
        exit;
    }
} 