-- Tạo bảng NganhHoc nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `nganhhoc` (
    `MaNganh` varchar(10) NOT NULL,
    `TenNganh` varchar(100) NOT NULL,
    PRIMARY KEY (`MaNganh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng SinhVien nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `sinhvien` (
    `MaSV` varchar(10) NOT NULL,
    `HoTen` varchar(50) NOT NULL,
    `GioiTinh` varchar(5),
    `NgaySinh` date,
    `Hinh` varchar(255),
    `MaNganh` varchar(10),
    PRIMARY KEY (`MaSV`),
    FOREIGN KEY (`MaNganh`) REFERENCES `nganhhoc`(`MaNganh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng HocPhan nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `hocphan` (
    `MaHP` varchar(10) NOT NULL,
    `TenHP` varchar(100) NOT NULL,
    `SoTinChi` int NOT NULL,
    PRIMARY KEY (`MaHP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng DangKy nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `dangky` (
    `MaDK` int AUTO_INCREMENT,
    `MaSV` varchar(10) NOT NULL,
    `NgayDK` date NOT NULL,
    PRIMARY KEY (`MaDK`),
    FOREIGN KEY (`MaSV`) REFERENCES `sinhvien`(`MaSV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng ChiTietDangKy nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `chitietdangky` (
    `MaDK` int NOT NULL,
    `MaHP` varchar(10) NOT NULL,
    PRIMARY KEY (`MaDK`, `MaHP`),
    FOREIGN KEY (`MaDK`) REFERENCES `dangky`(`MaDK`),
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
INSERT INTO `hocphan` (`MaHP`, `TenHP`, `SoTinChi`) VALUES
('CNTT01', 'Lập trình C', 3),
('CNTT02', 'Cơ sở dữ liệu', 2),
('QTDK02', 'Xác suất thống kê 1', 3),
('QTKD01', 'Kinh tế vi mô', 2); 