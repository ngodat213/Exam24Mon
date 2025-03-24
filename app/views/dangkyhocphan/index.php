<?php
$title = 'Test1';
ob_start();
?>

<form action="index.php?controller=dangkyhocphan&action=register" method="POST">
    <div class="container">
        <h1>Đăng Kí học phần</h1>

        <table class="course-list">
            <tr>
                <th>MaHP</th>
                <th>Tên Học Phần</th>
                <th>Số Chỉ Chỉ</th>
            </tr>
            <?php 
            $selectedCourses = isset($_SESSION['selected_courses']) ? $_SESSION['selected_courses'] : [];
            $totalCourses = 0;
            $totalCredits = 0;
            
            while($row = $availableCourses->fetch_assoc()): 
                if (in_array($row['MaHP'], $selectedCourses)) {
                    $totalCourses++;
                    $totalCredits += $row['SoTinChi'];
                }
            ?>
            <tr>
                <td><?php echo $row['MaHP']; ?></td>
                <td><?php echo $row['TenHP']; ?></td>
                <td><?php echo $row['SoTinChi']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>

        <div class="summary">
            <div class="summary-right">
                <p class="red-text">Số lượng học phần: <?php echo $totalCourses; ?></p>
                <p class="red-text">Tổng số tín chỉ: <?php echo $totalCredits; ?></p>
                <p class="back-link"><a href="index.php?controller=hocphan">Trở về giỏ hàng</a></p>
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
                <button type="submit" class="btn-confirm">Xác Nhận</button>
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