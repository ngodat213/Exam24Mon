CREATE DATABASE Test1;
USE Test1;

CREATE TABLE NganhHoc (
    MaNganh CHAR(4) PRIMARY KEY,
    TenNganh VARCHAR(30)
);

CREATE TABLE SinhVien (
    MaSV CHAR(10) PRIMARY KEY,
    HoTen VARCHAR(50) NOT NULL,
    GioiTinh VARCHAR(5),
    NgaySinh DATE,
    Hinh VARCHAR(50),
    MaNganh CHAR(4),
    FOREIGN KEY (MaNganh) REFERENCES NganhHoc(MaNganh)
);

CREATE TABLE HocPhan (
    MaHP CHAR(6) PRIMARY KEY,
    TenHP VARCHAR(30) NOT NULL,
    SoTinChi INT
);

CREATE TABLE DangKy (
    MaDK INT AUTO_INCREMENT PRIMARY KEY,
    NgayDK DATE,
    MaSV CHAR(10),
    FOREIGN KEY (MaSV) REFERENCES SinhVien(MaSV)
);

CREATE TABLE ChiTietDangKy (
    MaDK INT,
    MaHP CHAR(6),
    PRIMARY KEY (MaDK, MaHP),
    FOREIGN KEY (MaDK) REFERENCES DangKy(MaDK),
    FOREIGN KEY (MaHP) REFERENCES HocPhan(MaHP)
);

INSERT INTO NganhHoc(MaNganh, TenNganh) VALUES 
('CNTT', 'Công nghệ thông tin'),
('QTKD', 'Quản trị kinh doanh');

INSERT INTO SinhVien(MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) VALUES
('0123456789', 'Nguyễn Văn A', 'Nam', '2000-02-12', 'Content/images/sv1.jpg', 'CNTT'),
('9876543210', 'Nguyễn Thị B', 'Nữ', '2000-07-03', 'Content/images/sv2.jpg', 'QTKD');

INSERT INTO HocPhan(MaHP, TenHP, SoTinChi) VALUES
('CNTT01', 'Lập trình C', 3),
('CNTT02', 'Cơ sở dữ liệu', 2),
('QTKD01', 'Kinh tế vi mô', 2),
('QTDK02', 'Xác suất thống kê 1', 3); 