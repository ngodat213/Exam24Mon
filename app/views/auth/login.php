<?php
$title = 'Test1 - Đăng Nhập';
ob_start();
?>

<div class="login-container">
    <h1>ĐĂNG NHẬP</h1>
    <form method="post" class="login-form">
        <div class="form-group">
            <label>MaSV:</label>
            <input type="text" name="MaSV" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Đăng Nhập" class="btn-login">
        </div>
    </form>
    <a href="index.php" class="back-link">Back to List</a>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 