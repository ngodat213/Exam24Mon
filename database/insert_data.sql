-- Xóa dữ liệu cũ (nếu có)
DELETE FROM `chitietdangky`;
DELETE FROM `dangky`;
DELETE FROM `sinhvien`;
DELETE FROM `hocphan`;
DELETE FROM `nganhhoc`;

-- Thêm dữ liệu cho bảng NganhHoc
INSERT INTO `nganhhoc` (`MaNganh`, `TenNganh`) VALUES
('CNTT', 'Công nghệ thông tin'),
('QTKD', 'Quản trị kinh doanh'),
('KTPM', 'Kỹ thuật phần mềm'),
('HTTT', 'Hệ thống thông tin');

-- Thêm dữ liệu cho bảng SinhVien
INSERT INTO `sinhvien` (`MaSV`, `HoTen`, `GioiTinh`, `NgaySinh`, `MaNganh`) VALUES
('2180607094', 'Ngô Văn Tiến Đạt', 'Nam', '2003-10-26', 'CNTT'),
('2180607095', 'Nguyễn Văn A', 'Nam', '2003-05-15', 'KTPM'),
('2180607096', 'Trần Thị B', 'Nữ', '2003-08-20', 'HTTT'),
('2180607097', 'Lê Văn C', 'Nam', '2003-12-10', 'QTKD');

-- Thêm dữ liệu cho bảng HocPhan
INSERT INTO `hocphan` (`MaHP`, `TenHP`, `SoTinChi`) VALUES
('CNTT01', 'Lập trình C', 3),
('CNTT02', 'Cơ sở dữ liệu', 2),
('CNTT03', 'Lập trình Java', 3),
('CNTT04', 'Lập trình Web', 3),
('KTPM01', 'Công nghệ phần mềm', 3),
('KTPM02', 'Kiểm thử phần mềm', 2),
('HTTT01', 'Phân tích thiết kế hệ thống', 3),
('HTTT02', 'Cơ sở dữ liệu phân tán', 2),
('QTKD01', 'Kinh tế vi mô', 2),
('QTKD02', 'Marketing căn bản', 3),
('QTDK02', 'Xác suất thống kê 1', 3);

-- Thêm một số dữ liệu mẫu cho bảng DangKy
INSERT INTO `dangky` (`MaSV`, `NgayDK`) VALUES
('2180607094', '2024-03-24');

-- Lấy MaDK vừa thêm
SET @last_madk = LAST_INSERT_ID();

-- Thêm dữ liệu cho bảng ChiTietDangKy
INSERT INTO `chitietdangky` (`MaDK`, `MaHP`) VALUES
(@last_madk, 'CNTT01'),
(@last_madk, 'CNTT02'),
(@last_madk, 'CNTT03'); 