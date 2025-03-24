<?php
$title = 'Test1 - Đăng Ký Học Phần';
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng Ký Học Phần</title>
    <style>
        .container { width: 80%; margin: 0 auto; }
        .student-info { margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        .course-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .course-table th, .course-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .course-table th { background: #f0f0f0; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-primary { background: #007bff; color: white; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .summary { margin: 20px 0; padding: 15px; background: #e9ecef; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>ĐĂNG KÝ HỌC PHẦN</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                if ($_GET['success'] === 'deleted') {
                    echo 'Đã hủy đăng ký học phần thành công.';
                } else {
                    echo 'Đăng ký học phần thành công.';
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                if ($_GET['error'] === 'unauthorized') {
                    echo 'Bạn không có quyền thực hiện thao tác này.';
                } elseif ($_GET['error'] === 'delete_failed') {
                    echo 'Không thể hủy đăng ký học phần. Vui lòng thử lại.';
                } else {
                    echo 'Có lỗi xảy ra. Vui lòng thử lại.';
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="student-info">
            <h2>Thông tin sinh viên</h2>
            <p><strong>Mã sinh viên:</strong> <?php echo htmlspecialchars($_SESSION['MaSV']); ?></p>
            <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($_SESSION['HoTen']); ?></p>
            <p><strong>Ngày sinh:</strong> <?php echo htmlspecialchars($_SESSION['NgaySinh']); ?></p>
            <p><strong>Ngành học:</strong> <?php echo htmlspecialchars($student['TenNganh']); ?></p>
        </div>

        <h2>Danh sách học phần đã đăng ký</h2>
        <?php if (!empty($registeredCoursesArray)): ?>
            <table class="course-table">
                <thead>
                    <tr>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th>Số tín chỉ</th>
                        <th>Số lượng còn lại</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registeredCoursesArray as $course): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['MaHP']); ?></td>
                            <td><?php echo htmlspecialchars($course['TenHP']); ?></td>
                            <td><?php echo htmlspecialchars($course['SoTinChi']); ?></td>
                            <td><?php echo htmlspecialchars($course['SoLuong']); ?></td>
                            <td><?php echo htmlspecialchars($course['NgayDK']); ?></td>
                            <td>
                                <a href="index.php?controller=dangkyhocphan&action=delete&madk=<?php echo $course['MaDK']; ?>&mahp=<?php echo $course['MaHP']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Bạn có chắc muốn hủy đăng ký học phần này?')">
                                    Hủy đăng ký
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary">
                <p><strong>Tổng số học phần:</strong> <?php echo count($registeredCoursesArray); ?></p>
                <p><strong>Tổng số tín chỉ:</strong> <?php echo $totalCredits; ?></p>
            </div>

            <div style="margin-top: 20px;">
                <a href="index.php?controller=hocphan" class="btn btn-primary">Đăng ký thêm học phần</a>
            </div>
        <?php else: ?>
            <p>Bạn chưa đăng ký học phần nào.</p>
            <div style="margin-top: 20px;">
                <a href="index.php?controller=hocphan" class="btn btn-primary">Đăng ký học phần</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 