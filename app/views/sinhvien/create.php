<?php
$title = 'Test1 - Thêm Sinh Viên';
ob_start();
?>

<h1>THÊM SINH VIÊN</h1>
<form method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>MaSV:</label>
        <input type="text" name="MaSV" required>
    </div>
    <div class="form-group">
        <label>HoTen:</label>
        <input type="text" name="HoTen" required>
    </div>
    <div class="form-group">
        <label>GioiTinh:</label>
        <input type="text" name="GioiTinh">
    </div>
    <div class="form-group">
        <label>NgaySinh:</label>
        <input type="date" name="NgaySinh">
    </div>
    <div class="form-group">
        <label>Hinh:</label>
        <input type="file" name="Hinh" style="display: none;">
        <button type="button" onclick="document.querySelector('input[name=Hinh]').click()">Chọn</button>
    </div>
    <div class="form-group">
        <label>MaNganh:</label>
        <select name="MaNganh">
            <?php while($nganh = $nganh_result->fetch_assoc()): ?>
                <option value="<?php echo $nganh['MaNganh']; ?>">
                    <?php echo $nganh['TenNganh']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group">
        <label></label>
        <input type="submit" value="Create">
    </div>
</form>
<a href="index.php?controller=sinhvien&action=index">Back to List</a>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 