<!DOCTYPE html>
<html>
<head>
    <title><?php echo isset($title) ? $title : 'Test1'; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Test1</a>
        <a href="index.php?controller=sinhvien&action=index">Sinh Viên</a>
        <a href="index.php?controller=hocphan&action=index">Học Phần</a>
        <a href="index.php?controller=dangkyhocphan&action=index">Đăng Kí ()</a>
        <a href="index.php?controller=auth&action=login">Đăng Nhập</a>
    </div>
    <div class="content">
        <?php echo $content; ?>
    </div>
</body>
</html> 