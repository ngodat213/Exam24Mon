<?php
$title = 'Test1 - Thông tin chi tiết';
ob_start();
?>

<h1>Thông tin chi tiết</h1>
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
<div>
    <a href="index.php?controller=sinhvien&action=edit&id=<?php echo $student['MaSV']; ?>">Edit</a> |
    <a href="index.php?controller=sinhvien&action=index">Back to List</a>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 