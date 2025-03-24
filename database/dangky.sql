-- Create HocPhan table if it doesn't exist
CREATE TABLE IF NOT EXISTS `hocphan` (
    `MaHP` varchar(10) NOT NULL,
    `TenHP` varchar(100) NOT NULL,
    `SoTC` int NOT NULL,
    PRIMARY KEY (`MaHP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create DangKy table if it doesn't exist
CREATE TABLE IF NOT EXISTS `dangky` (
    `MaSV` varchar(10) NOT NULL,
    `MaHP` varchar(10) NOT NULL,
    `NgayDK` datetime NOT NULL,
    PRIMARY KEY (`MaSV`, `MaHP`),
    FOREIGN KEY (`MaSV`) REFERENCES `sinhvien`(`MaSV`) ON DELETE CASCADE,
    FOREIGN KEY (`MaHP`) REFERENCES `hocphan`(`MaHP`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample courses
INSERT INTO `hocphan` (`MaHP`, `TenHP`, `SoTC`) VALUES
('HP001', 'Lập trình web', 3),
('HP002', 'Cơ sở dữ liệu', 3),
('HP003', 'Lập trình Java', 4),
('HP004', 'Mạng máy tính', 3),
('HP005', 'An toàn thông tin', 3); 