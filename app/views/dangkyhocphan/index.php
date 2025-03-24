<?php
$title = 'Test1';
ob_start();
?>

<?php if (isset($_SESSION['registration_success'])): ?>
<div class="alert alert-success">
    <h4>Đăng ký học phần thành công!</h4>
    <p>Mã đăng ký: <?php echo $_SESSION['last_registration']['madk']; ?></p>
    <p>Ngày đăng ký: <?php echo date('d/m/Y', strtotime($_SESSION['last_registration']['date'])); ?></p>
    <?php unset($_SESSION['registration_success']); ?>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger">
    <?php 
    if ($_GET['error'] === 'no_courses') {
        echo 'Vui lòng chọn ít nhất một học phần để đăng ký!';
    } elseif ($_GET['error'] === 'already_registered') {
        echo 'Học phần này đã được đăng ký!';
    } else {
        echo isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Có lỗi xảy ra khi đăng ký học phần!';
    }
    ?>
</div>
<?php endif; ?>

<form action="index.php?controller=dangkyhocphan&action=register" method="POST">
    <div class="container">
        <h1>Đăng Kí học phần</h1>

        <table class="course-list">
            <tr>
                <th>MaHP</th>
                <th>Tên Học Phần</th>
                <th>Số Chỉ Chỉ</th>
                <th>Actions</th>
            </tr>
            <?php 
            $selectedCourses = isset($_SESSION['selected_courses']) ? $_SESSION['selected_courses'] : [];
            $totalCourses = 0;
            $totalCredits = 0;
            
            mysqli_data_seek($availableCourses, 0); // Reset pointer
            while($row = $availableCourses->fetch_assoc()): 
                if (in_array($row['MaHP'], $selectedCourses)):
                    $totalCourses++;
                    $totalCredits += $row['SoTinChi'];
            ?>
            <tr>
                <td><?php echo $row['MaHP']; ?></td>
                <td><?php echo $row['TenHP']; ?></td>
                <td><?php echo $row['SoTinChi']; ?></td>
                <td>
                    <a href="index.php?controller=dangkyhocphan&action=removeFromCart&mahp=<?php echo $row['MaHP']; ?>" 
                       class="btn-remove"
                       onclick="return confirm('Bạn có chắc muốn xóa học phần này?');">
                        Xóa
                    </a>
                </td>
            </tr>
            <?php 
                endif;
            endwhile; 
            ?>
        </table>

        <div class="summary">
            <div class="summary-right">
                <p class="red-text">Số lượng học phần: <?php echo $totalCourses; ?></p>
                <p class="red-text">Tổng số tín chỉ: <?php echo $totalCredits; ?></p>
                <p class="back-link"><a href="index.php?controller=hocphan">Trở về danh sách học phần</a></p>
            </div>
        </div>

        <div class="registration-info">
            <h2>Thông tin Đăng kí</h2>
            <div class="info-grid">
                <div class="info-row">
                    <label>Mã số sinh viên:</label>
                    <span><?php echo isset($_SESSION['MaSV']) ? $_SESSION['MaSV'] : ''; ?></span>
                </div>
                <div class="info-row">
                    <label>Họ Tên Sinh Viên:</label>
                    <span><?php echo isset($_SESSION['HoTen']) ? $_SESSION['HoTen'] : ''; ?></span>
                </div>
                <div class="info-row">
                    <label>Ngày Sinh:</label>
                    <span><?php echo isset($_SESSION['NgaySinh']) ? date('d/m/Y', strtotime($_SESSION['NgaySinh'])) : ''; ?></span>
                </div>
                <div class="info-row">
                    <label>Ngành Học:</label>
                    <span><?php echo isset($_SESSION['MaNganh']) ? $_SESSION['MaNganh'] : ''; ?></span>
                </div>
                <div class="info-row">
                    <label>Ngày Đăng Kí:</label>
                    <span><?php echo date('d/m/Y'); ?></span>
                </div>
            </div>
            <div class="button-container">
                <?php if (!empty($selectedCourses)): ?>
                <button type="submit" class="btn-confirm">Xác Nhận Đăng Ký</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<style>
.container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert h4 {
    margin-top: 0;
    margin-bottom: 10px;
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

.btn-remove {
    background-color: #dc3545;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
}

.btn-remove:hover {
    background-color: #c82333;
}

h1 {
    font-size: 24px;
    margin-bottom: 20px;
}

.course-list {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.course-list th, .course-list td {
    border: 1px solid #ddd;
    padding: 8px 15px;
    text-align: left;
}

.course-list th {
    background-color: #f5f5f5;
}

.summary {
    margin: 20px 0;
}

.summary-right {
    text-align: right;
}

.red-text {
    color: red;
    margin: 5px 0;
}

.back-link a {
    color: blue;
    text-decoration: none;
}

.registration-info {
    margin-top: 30px;
}

.registration-info h2 {
    font-size: 20px;
    margin-bottom: 20px;
}

.info-grid {
    display: grid;
    gap: 15px;
    margin-bottom: 20px;
}

.info-row {
    display: grid;
    grid-template-columns: 150px auto;
    gap: 10px;
    align-items: center;
}

.info-row label {
    font-weight: bold;
}

.button-container {
    text-align: center;
    margin-top: 20px;
}

.btn-confirm {
    background-color: #4CAF50;
    color: white;
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-confirm:hover {
    background-color: #45a049;
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 