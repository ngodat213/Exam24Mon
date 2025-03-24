<?php
$title = 'Test1 - Xóa Thông Tin';
ob_start();
?>

<h1>XÓA THÔNG TIN</h1>
<h3>Are you sure you want to delete this?</h3>
<div class="form-group">
    <label>HoTen:</label>
    <span><?php echo $student['HoTen']; ?></span>
</div>
<div class="form-group">
    <label>GioiTinh:</label>
    <span><?php echo $student['GioiTinh']; ?></span>
</div>
<div class="form-group">
    <label>NgaySinh:</label>
    <span><?php echo date('d/m/Y', strtotime($student['NgaySinh'])); ?></span>
</div>
<div class="form-group">
    <label>Hinh:</label>
    <?php if($student['Hinh']): ?>
        <img src="<?php echo $student['Hinh']; ?>" class="student-image">
    <?php endif; ?>
</div>
<div class="form-group">
    <label>MaNganh:</label>
    <span><?php echo $student['MaNganh']; ?></span>
</div>
<form method="post">
    <input type="submit" value="Delete"> |
    <a href="index.php?controller=sinhvien&action=index">Back to List</a>
</form>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 