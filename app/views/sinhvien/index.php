<?php
$title = 'Test1 - Sinh Viên';
ob_start();
?>

<h1>TRANG SINH VIÊN</h1>
<a href="index.php?controller=sinhvien&action=create">Add Student</a>
<table>
    <tr>
        <th>MaSV</th>
        <th>HoTen</th>
        <th>GioiTinh</th>
        <th>NgaySinh</th>
        <th>Hinh</th>
        <th>MaNganh</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['MaSV']; ?></td>
        <td><?php echo $row['HoTen']; ?></td>
        <td><?php echo $row['GioiTinh']; ?></td>
        <td><?php echo date('d/m/Y', strtotime($row['NgaySinh'])); ?></td>
        <td><img src="<?php echo $row['Hinh']; ?>" class="student-image" width="50" height="50"></td>
        <td><?php echo $row['MaNganh']; ?></td>
        <td>
            <a href="index.php?controller=sinhvien&action=edit&id=<?php echo $row['MaSV']; ?>" class="btn">Edit</a> |
            <a href="index.php?controller=sinhvien&action=details&id=<?php echo $row['MaSV']; ?>" class="btn">Details</a> |
            <a href="index.php?controller=sinhvien&action=delete&id=<?php echo $row['MaSV']; ?>" class="btn">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?> 