<?php
session_start();
require_once __DIR__ . '/app/controllers/SinhVienController.php';
require_once __DIR__ . '/app/controllers/HocPhanController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/DangKyHocPhanController.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'sinhvien';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Database connection
require_once __DIR__ . '/config/database.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Kiểm tra đăng nhập cho các trang cần bảo vệ
if (!isset($_SESSION['MaSV']) && 
    !($controller === 'auth' && $action === 'login') &&
    !($controller === 'sinhvien' && $action === 'index')) {
    header("Location: index.php?controller=auth&action=login");
    exit();
}

switch ($controller) {
    case 'sinhvien':
        $controller = new SinhVienController($db);
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->edit($id);
                break;
            case 'delete':
                $controller->delete($id);
                break;
            case 'details':
                $controller->details($id);
                break;
            default:
                $controller->index();
        }
        break;
    case 'hocphan':
        $controller = new HocPhanController($db);
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'register':
                $controller->register();
                break;
            default:
                $controller->index();
        }
        break;
    case 'dangkyhocphan':
        $controller = new DangKyHocPhanController($db);
        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'register':
                $controller->register();
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
        }
        break;
    case 'auth':
        $controller = new AuthController($db);
        switch ($action) {
            case 'login':
                $controller->login();
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                $controller->login();
        }
        break;
    default:
        $controller = new SinhVienController($db);
        $controller->index();
} 