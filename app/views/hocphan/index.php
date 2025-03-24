<?php
$title = 'Test1 - Học Phần';
ob_start();
?>

<h1>DANH SÁCH HỌC PHẦN</h1>
<table>
    <tr>
        <th>Mã Học Phần</th>
        <th>Tên Học Phần</th>
        <th>Số Tín Chỉ</th>
        <th>Số lượng còn lại</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $availableCourses->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['MaHP']; ?></td>
        <td><?php echo $row['TenHP']; ?></td>
        <td><?php echo $row['SoTinChi']; ?></td>
        <td><?php echo $row['SoLuong']; ?></td>
        <td>
            <?php if ($row['SoLuong'] > 0): ?>
            <form action="index.php?controller=dangkyhocphan&action=addToCart" method="POST" style="display: inline;">
                <input type="hidden" name="MaHP" value="<?php echo $row['MaHP']; ?>">
                <button type="submit" class="btn-register">Đăng ký</button>
            </form>
            <?php else: ?>
            <span class="text-danger">Hết chỗ</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
<div class="alert alert-success">
    Đã thêm học phần vào giỏ đăng ký!
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger">
    <?php 
    switch ($_GET['error']) {
        case 'already_registered':
            echo 'Học phần này đã được đăng ký!';
            break;
        case 'already_in_cart':
            echo 'Học phần này đã có trong giỏ đăng ký!';
            break;
        case 'course_not_found':
            echo 'Không tìm thấy học phần này!';
            break;
        case 'no_slots':
            echo 'Học phần này đã hết chỗ!';
            break;
    }
    ?>
</div>
<?php endif; ?>

<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px 15px;
    text-align: left;
}

th {
    background-color: #f5f5f5;
}

.btn-register {
    background-color: #4CAF50;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-register:hover {
    background-color: #45a049;
}

.text-danger {
    color: #dc3545;
    font-weight: bold;
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