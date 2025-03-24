-- Xóa các bảng nếu tồn tại
DROP TABLE IF EXISTS `chitietdangky`;
DROP TABLE IF EXISTS `dangky`;
DROP TABLE IF EXISTS `hocphan`;
DROP TABLE IF EXISTS `sinhvien`;
DROP TABLE IF EXISTS `nganhhoc`;

-- Tạo bảng NganhHoc
CREATE TABLE `nganhhoc` (
    `MaNganh` varchar(10) NOT NULL,
    `TenNganh` varchar(100) NOT NULL,
    PRIMARY KEY (`MaNganh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng SinhVien
CREATE TABLE `sinhvien` (
    `MaSV` varchar(10) NOT NULL,
    `HoTen` varchar(50) NOT NULL,
    `GioiTinh` varchar(5),
    `NgaySinh` date,
    `Hinh` varchar(255),
    `MaNganh` varchar(10),
    PRIMARY KEY (`MaSV`),
    FOREIGN KEY (`MaNganh`) REFERENCES `nganhhoc`(`MaNganh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng HocPhan
CREATE TABLE `hocphan` (
    `MaHP` varchar(10) NOT NULL,
    `TenHP` varchar(100) NOT NULL,
    `SoTinChi` int NOT NULL,
    `SoLuong` int NOT NULL DEFAULT 100,
    PRIMARY KEY (`MaHP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng DangKy
CREATE TABLE `dangky` (
    `MaDK` int AUTO_INCREMENT,
    `MaSV` varchar(10) NOT NULL,
    `NgayDK` date NOT NULL,
    PRIMARY KEY (`MaDK`),
    FOREIGN KEY (`MaSV`) REFERENCES `sinhvien`(`MaSV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng ChiTietDangKy
CREATE TABLE `chitietdangky` (
    `MaDK` int NOT NULL,
    `MaHP` varchar(10) NOT NULL,
    PRIMARY KEY (`MaDK`, `MaHP`),
    FOREIGN KEY (`MaDK`) REFERENCES `dangky`(`MaDK`) ON DELETE CASCADE,
    FOREIGN KEY (`MaHP`) REFERENCES `hocphan`(`MaHP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm dữ liệu mẫu cho bảng NganhHoc
INSERT INTO `nganhhoc` (`MaNganh`, `TenNganh`) VALUES
('CNTT', 'Công nghệ thông tin'),
('QTKD', 'Quản trị kinh doanh');

-- Thêm dữ liệu mẫu cho bảng SinhVien
INSERT INTO `sinhvien` (`MaSV`, `HoTen`, `GioiTinh`, `NgaySinh`, `MaNganh`) VALUES
('2180607094', 'Ngô Văn Tiến Đạt', 'Nam', '2003-10-26', 'CNTT');

-- Thêm dữ liệu mẫu cho bảng HocPhan
INSERT INTO `hocphan` (`MaHP`, `TenHP`, `SoTinChi`, `SoLuong`) VALUES
('CNTT01', 'Lập trình C', 3, 99),
('CNTT02', 'Cơ sở dữ liệu', 2, 99),
('QTKD01', 'Kinh tế vi mô', 2, 100),
('QTKD02', 'Xác suất thống kê 1', 3, 99); 