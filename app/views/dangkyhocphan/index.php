<?php
$title = 'Test1 - Đăng Ký Học Phần';
ob_start();
?>

<h1>ĐĂNG KÝ HỌC PHẦN</h1>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <?php 
    if ($_GET['success'] === 'deleted') {
        echo 'Đã hủy đăng ký học phần thành công!';
    }
    ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger">
    <?php 
    switch ($_GET['error']) {
        case 'unauthorized':
            echo 'Bạn không có quyền hủy đăng ký này!';
            break;
        case 'delete_failed':
            echo 'Có lỗi xảy ra khi hủy đăng ký. Vui lòng thử lại!';
            break;
        case 'invalid_request':
            echo 'Yêu cầu không hợp lệ!';
            break;
    }
    ?>
</div>
<?php endif; ?>

<div class="student-info">
    <h2>Thông tin sinh viên</h2>
    <table class="info-table">
        <tr>
            <th>Mã sinh viên:</th>
            <td><?php echo $student['MaSV']; ?></td>
        </tr>
        <tr>
            <th>Họ tên:</th>
            <td><?php echo $student['HoTen']; ?></td>
        </tr>
        <tr>
            <th>Ngành học:</th>
            <td><?php echo $student['TenNganh']; ?></td>
        </tr>
    </table>
</div>

<div class="registered-courses">
    <h2>Danh sách học phần đã đăng ký</h2>
    <?php if (empty($registeredCoursesArray)): ?>
        <p class="no-courses">Bạn chưa đăng ký học phần nào.</p>
        <a href="index.php?controller=hocphan" class="btn-primary">Đăng ký học phần mới</a>
    <?php else: ?>
        <table class="courses-table">
            <tr>
                <th>Mã học phần</th>
                <th>Tên học phần</th>
                <th>Số tín chỉ</th>
                <th>Ngày đăng ký</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($registeredCoursesArray as $course): ?>
            <tr>
                <td><?php echo $course['MaHP']; ?></td>
                <td><?php echo $course['TenHP']; ?></td>
                <td><?php echo $course['SoTinChi']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($course['NgayDK'])); ?></td>
                <td>
                    <a href="index.php?controller=dangkyhocphan&action=delete&madk=<?php echo $course['MaDK']; ?>&mahp=<?php echo $course['MaHP']; ?>" 
                       class="btn-delete"
                       onclick="return confirm('Bạn có chắc chắn muốn hủy đăng ký học phần này?')">
                        Hủy đăng ký
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="summary">
            <p>Tổng số học phần: <strong><?php echo count($registeredCoursesArray); ?></strong></p>
            <p>Tổng số tín chỉ: <strong><?php echo $totalCredits; ?></strong></p>
        </div>

        <div class="actions">
            <a href="index.php?controller=hocphan" class="btn-primary">Đăng ký thêm học phần</a>
        </div>
    <?php endif; ?>
</div>

<style>
.student-info {
    margin-bottom: 30px;
}

.info-table {
    width: 100%;
    max-width: 600px;
    margin-bottom: 20px;
}

.info-table th {
    width: 150px;
    text-align: right;
    padding-right: 20px;
}

.info-table td {
    padding: 8px;
}

.courses-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.courses-table th,
.courses-table td {
    border: 1px solid #ddd;
    padding: 8px 15px;
    text-align: left;
}

.courses-table th {
    background-color: #f5f5f5;
}

.summary {
    margin: 20px 0;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.actions {
    margin-top: 20px;
}

.btn-primary {
    display: inline-block;
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-delete {
    display: inline-block;
    padding: 5px 10px;
    background-color: #dc3545;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
}

.btn-delete:hover {
    background-color: #c82333;
}

.no-courses {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 4px;
    text-align: center;
    margin-bottom: 20px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 