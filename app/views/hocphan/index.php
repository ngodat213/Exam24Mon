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
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['MaHP']; ?></td>
        <td><?php echo $row['TenHP']; ?></td>
        <td><?php echo $row['SoTinChi']; ?></td>
        <td>
            <form action="index.php?controller=dangkyhocphan&action=addToCart" method="POST" style="display: inline;">
                <input type="hidden" name="MaHP" value="<?php echo $row['MaHP']; ?>">
                <button type="submit" class="btn-register">Đăng ký</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
<div class="alert alert-success">
    Đã thêm học phần vào giỏ đăng ký!
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
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 