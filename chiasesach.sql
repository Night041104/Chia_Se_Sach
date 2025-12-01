-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 30, 2025 lúc 04:45 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12
CREATE database if not exists chiasesach;
use chiasesach;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `chiasesach`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuong`
--

CREATE TABLE `chuong` (
  `id` int(11) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `TenChuong` varchar(255) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `NgayDang` datetime DEFAULT current_timestamp(),
  `LuotDoc` int(11) DEFAULT 0,
  `LuotTai` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chuong`
--

INSERT INTO `chuong` (`id`, `MaSach`, `TenChuong`, `FilePath`, `NgayDang`, `LuotDoc`, `LuotTai`) VALUES
(2, 'A01', 'Toàn tập', 'A01_1764090032_[Kindlesaigon.vn] Ten Cua Tro Choi La Bat Coc - Higashino Keigo.pdf', '2025-11-26 00:00:32', 1, 0),
(3, 'A03', 'Chương 1: Hai anh em song sinh', 'A03_1764091198_2_Chng 1 Hai anh em song sinh.pdf', '2025-11-26 00:19:58', 72, 1),
(4, 'A03', 'Chương 2: Diễn viên trăm khuôn mặt', 'A03_1764091219_3_Chng 2 Din vin trm gng mt.pdf', '2025-11-26 00:20:19', 4, 2),
(5, 'A03', 'Chương 3: Giấc mộng giữa ban ngày', 'A03_1764091243_4_Chng 3 Gic mng gia ban ngy.pdf', '2025-11-26 00:20:43', 2, 1),
(6, 'A03', 'Chương 4: Gã tí hon nhảy múa', 'A03_1764091268_5_Chng 4 G t hon nhy ma.pdf', '2025-11-26 00:21:08', 2, 3),
(7, 'A02', 'Chuyện thứ nhất: Hoa anh đào', 'A02_1764164430_1_Chuyn th nht Hoa anh o.pdf', '2025-11-26 20:40:30', 3, 0),
(8, 'A02', 'Chuyện thứ hai: Phi hành gia', 'A02_1764164444_2_Chuyn th hai Phi hnh gia.pdf', '2025-11-26 20:40:44', 0, 0),
(9, 'A02', 'Chuyện thứ ba: Vận tốc năm centimet trên giây', 'A02_1764164476_3_Chuyn th ba Vn tc nm centimet trn giy.pdf', '2025-11-26 20:41:16', 1, 0),
(10, 'A02', 'Lời tác giả', 'A02_1764164492_4_Li tc gi.pdf', '2025-11-26 20:41:32', 0, 0),
(11, 'A03', 'Chương 5: Vách đá', 'A03_1764508225_6_Chng 5 Vch .pdf', '2025-11-30 20:10:25', 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id` int(11) NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `SoSao` tinyint(1) NOT NULL,
  `NoiDung` text DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`id`, `MaSach`, `userID`, `SoSao`, `NoiDung`, `NgayTao`) VALUES
(11, 'A03', 1, 5, 'hay', '2025-11-26 00:51:48'),
(12, 'A03', 2, 2, 'tệ', '2025-11-29 23:33:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_doc`
--

CREATE TABLE `lich_su_doc` (
  `id` int(11) NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `chuong_id` int(11) NOT NULL,
  `NgayXem` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su_doc`
--

INSERT INTO `lich_su_doc` (`id`, `userID`, `MaSach`, `chuong_id`, `NgayXem`) VALUES
(7, 1, 'A01', 2, '2025-11-26 00:00:45'),
(8, 1, 'A03', 3, '2025-11-29 20:54:29'),
(9, 1, 'A03', 4, '2025-11-26 00:48:25'),
(11, 1, 'A03', 6, '2025-11-26 00:29:16'),
(15, 1, 'A03', 5, '2025-11-26 00:29:18'),
(80, 1, 'A02', 7, '2025-11-28 12:53:49'),
(81, 1, 'A02', 9, '2025-11-28 12:43:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lich_su_tai`
--

CREATE TABLE `lich_su_tai` (
  `id` int(11) NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `chuong_id` int(11) NOT NULL,
  `NgayTai` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lich_su_tai`
--

INSERT INTO `lich_su_tai` (`id`, `userID`, `MaSach`, `chuong_id`, `NgayTai`) VALUES
(2, 1, 'A03', 6, '2025-11-28 13:01:42'),
(3, 1, 'A03', 3, '2025-11-26 00:48:50'),
(4, 1, 'A03', 5, '2025-11-28 13:00:24'),
(5, 1, 'A03', 4, '2025-11-28 13:03:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `roleID` int(10) UNSIGNED NOT NULL,
  `tenRole` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`roleID`, `tenRole`) VALUES
(1, 'Admin'),
(2, 'User');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach`
--

CREATE TABLE `sach` (
  `MaSach` varchar(10) NOT NULL,
  `TenSach` varchar(50) NOT NULL,
  `MoTa` text NOT NULL,
  `Hinh` varchar(200) NOT NULL,
  `LuotDoc` int(11) NOT NULL,
  `LuotTai` int(11) NOT NULL,
  `TinhTrang` varchar(50) DEFAULT 'Đang tiến hành'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sach`
--

INSERT INTO `sach` (`MaSach`, `TenSach`, `MoTa`, `Hinh`, `LuotDoc`, `LuotTai`, `TinhTrang`) VALUES
('A01', 'Tên của trò chơi là bắt cóc', 'Sakuma là một nhân viên lập kế hoạch tài năng trong mảng quảng cáo. Dự án của anh bỗng chốc tan thành mây khói vì một vị khách hàng quan trọng – Katsuragi. Tìm đến dinh thự của Katsuragi trong cơn quẫn bách, anh tình cờ bắt gặp con gái của Katsuragi vừa trốn ra khỏi nhà. Cả hai bèn cùng nhau chơi một trò chơi điên rồ với Katsuragi – người tự gọi mình là “bậc thầy trò chơi”. Một vụ bắt cóc giả, con gái ông ta là con tin. Khéo léo tận dụng điện thoại di động và Internet, họ đánh cắp số tiền chuộc 300 triệu yên.\r\n\r\nHai bộ não thông minh cùng toan tính một kế hoạch tưởng chừng không có kẽ hở. Và khi trò chơi hạ màn, chiến thắng sẽ thuộc về ai?', 'tencuatrochoilabatcoc.jpg', 12, 1, 'Đang tiến hành'),
('A02', '5 Centimet Trên Giây', '5 Centimet Trên Giây của Shinkai Makoto không chỉ là chút vị đắng của tách cà phê mà ai cũng phải một lần nhâm nhi trong đời, mà còn là tác phẩm có dấu ấn đậm nét của văn học Nhật Bản. 5 Centimet Trên Giây lột tả một bi kịch tuổi trẻ đầy day dứt, ám ảnh, nhưng cũng chan chứa nghị lực, tươi sáng và cực kì dứt khoát.\\r\\n\\r\\nCâu chuyện bắt đầu bằng một mảnh quá khứ của tuổi thơ, nơi những tâm hồn và tình cảm trong sáng nhất được nuôi dưỡng và lớn dần. Nhưng đột ngột, những kí ức tươi đẹp ấy chợt tan biến, chia ly ập đến như một cơn mưa rào không báo trước. Cánh hoa anh đào chầm chậm rơi với vận tốc 5 cm/s rồi cũng đến ngày phải chạm đất. Như một vết thương khoét sâu vào hai tâm hồn vốn đã rất đỗi cô đơn, họ khao khát tìm đến nhau, khao khát ở bên nhau nhưng bắt buộc phải chịu đựng cảnh chia ly. Cuối cùng, họ dần dần quên nhau rồi rơi vào một bi kịch tuổi trẻ đầy nhớ nhung, cô đơn và lạc lõng.', '5cm_tren_s.jpg', 26, 5, 'Đã hoàn thành'),
('A03', 'Những Tội Ác Bất Dung Thứ', 'Những Tội Ác Bất Dung Thứ của tác giả Edogawa Ranpo là một tác phẩm trinh thám kinh điển, đưa độc giả vào thế giới tội ác tinh vi và những tâm lý bất thường của con người. Edogawa Ranpo, được xem là “cha đẻ của truyện trinh thám Nhật Bản”, khéo léo kết hợp yếu tố trinh thám cổ điển với phân tích tâm lý, tạo nên những câu chuyện vừa ly kỳ, vừa ám ảnh.\r\n\r\nCuốn sách gồm nhiều vụ án khác nhau, mỗi vụ án là một thử thách đối với trí tưởng tượng và khả năng suy luận của người đọc. Ranpo xây dựng những tội ác tinh vi, thường vượt ra ngoài giới hạn thông thường, khiến cảnh sát và độc giả phải đau đầu tìm kiếm manh mối. Nhưng điểm đặc sắc nhất là cách tác giả đi sâu vào tâm lý tội phạm, khám phá động cơ, ám ảnh và sở thích bệnh lý khiến con người thực hiện những hành vi đáng sợ.', 'nhung-toi-ac-bat-dung-thu.jpg', 80, 7, 'Tạm ngưng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach_tacgia`
--

CREATE TABLE `sach_tacgia` (
  `MaSach` varchar(10) NOT NULL,
  `MaTG` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sach_tacgia`
--

INSERT INTO `sach_tacgia` (`MaSach`, `MaTG`) VALUES
('A01', 'KH01'),
('A02', 'SK02'),
('A03', 'ER01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach_theloai`
--

CREATE TABLE `sach_theloai` (
  `MaSach` varchar(10) NOT NULL,
  `MaTheLoai` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sach_theloai`
--

INSERT INTO `sach_theloai` (`MaSach`, `MaTheLoai`) VALUES
('A01', 'tl01'),
('A01', 'tl02'),
('A02', 'tl03'),
('A02', 'tl04'),
('A03', 'tl01'),
('A03', 'tl02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tacgia`
--

CREATE TABLE `tacgia` (
  `MaTG` varchar(10) NOT NULL,
  `TenTG` varchar(50) NOT NULL,
  `MoTaTG` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tacgia`
--

INSERT INTO `tacgia` (`MaTG`, `TenTG`, `MoTaTG`) VALUES
('ER01', 'Edogawa Ranpo.', 'EDOGAWA RANPO – HUYỀN THOẠI MỞ ĐƯỜNG CHO TRUYỆN TRINH THÁM NHẬT\r\nEdogawa Ranpo (1894–1965), tên thật là Hirai Tarou, được xem là người tiên phong trong việc khai sinh dòng tiểu thuyết trinh thám hiện đại tại Nhật Bản. Ông sinh ra tại tỉnh Mie, từng trải qua nhiều nghề khác nhau trước khi bước vào sự nghiệp văn chương. Năm 1923, Ranpo công bố tác phẩm đầu tay mang tên \"Đồng xu hai sen\" trên tạp chí Shin Seinen, đánh dấu bước ngoặt lớn trong hành trình sáng tác của mình.'),
('KH01', 'Keigo Higashino', 'Sinh tại Osaka vào năm 1958, tốt nghiệp trường Đại học Osaka. Trong khi vẫn còn làm công việc kỹ sư, ông đã giành được Giải thưởng Edogawa Rampo lần thứ 31 với tác phẩm “Sau giờ học”. Năm 1999, ông giành Giải thưởng của Hiệp hội Nhà văn trinh thám Nhật Bản lần thứ 52 với tác phẩm “Bí mật của Naoko”. Năm 2006, ông giành Giải thưởng Naoki lần thứ 134 và Giải thưởng dành cho tác phẩm xuất sắc dòng trinh thám Honkaku với tác phẩm “Phía sau Nghi can X”. Nhờ phóng bút rộng và đầy tham vọng, ông thu hút được rất nhiều độc giả ở nhiều lứa tuổi.'),
('SK02', 'Shinkai Makoto', 'Makoto Shinkai là đạo diễn và tác giả người Nhật, nổi tiếng với việc viết tiểu thuyết chuyển thể từ chính các bộ phim anime bom tấn của ông. Các tác phẩm của ông, như Your Name hay 5 Centimeters Per Second, luôn thấm đẫm chất trữ tình và nỗi buồn man mác. Văn phong của Shinkai tập trung sâu vào nội tâm nhân vật, khám phá các chủ đề về khoảng cách, sự chia ly, và những nuối tiếc của tuổi thanh xuân.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theloai`
--

CREATE TABLE `theloai` (
  `MaTheLoai` varchar(10) NOT NULL,
  `TenTheLoai` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `theloai`
--

INSERT INTO `theloai` (`MaTheLoai`, `TenTheLoai`) VALUES
('tl01', 'Trinh thám'),
('tl02', 'Kinh dị'),
('tl03', 'Học đường'),
('tl04', 'Lãng mạn'),
('tl05', 'Drama');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `userID` int(10) UNSIGNED NOT NULL,
  `hoUser` varchar(50) NOT NULL,
  `tenUser` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ngayTao` date NOT NULL,
  `roleID` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `avatar` varchar(50) NOT NULL DEFAULT 'default_avt.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`userID`, `hoUser`, `tenUser`, `email`, `password`, `ngayTao`, `roleID`, `avatar`) VALUES
(1, 'Nguyễn', 'Dũng', 'dung.nm.64cntt@ntu.edu.vn', '$2y$10$uwAxEVe577Wml3JF4Msrx.XfjP/KT8gOuXi2RNLzwbVbEzg5DUEIC', '2025-11-14', 1, 'dung.nm.64cntt@ntu.edu.vn_1764432066.jpg'),
(2, 'Nguyễn', 'Dũng', 'dungnguyen.041104@gmail.com', '$2y$10$ojB.2uWGCoCA3q0HJcB.pO1odIXO6eN7PaFyYaMJOszfKZmmNTAom', '2025-11-20', 2, 'default_avt.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `yeu_thich_sach`
--

CREATE TABLE `yeu_thich_sach` (
  `id` int(11) NOT NULL,
  `userID` int(10) UNSIGNED NOT NULL,
  `MaSach` varchar(10) NOT NULL,
  `ngayThich` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `yeu_thich_sach`
--

INSERT INTO `yeu_thich_sach` (`id`, `userID`, `MaSach`, `ngayThich`) VALUES
(15, 2, 'A01', '2025-11-26'),
(28, 1, 'A03', '2025-11-30');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chuong`
--
ALTER TABLE `chuong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `MaSach` (`MaSach`);

--
-- Chỉ mục cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`userID`,`MaSach`),
  ADD KEY `MaSach` (`MaSach`);

--
-- Chỉ mục cho bảng `lich_su_doc`
--
ALTER TABLE `lich_su_doc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_chapter_read` (`userID`,`chuong_id`),
  ADD KEY `MaSach` (`MaSach`),
  ADD KEY `chuong_id` (`chuong_id`);

--
-- Chỉ mục cho bảng `lich_su_tai`
--
ALTER TABLE `lich_su_tai`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_chapter_down` (`userID`,`chuong_id`),
  ADD KEY `MaSach` (`MaSach`),
  ADD KEY `chuong_id` (`chuong_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleID`);

--
-- Chỉ mục cho bảng `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`MaSach`);

--
-- Chỉ mục cho bảng `sach_tacgia`
--
ALTER TABLE `sach_tacgia`
  ADD PRIMARY KEY (`MaSach`,`MaTG`),
  ADD KEY `MaTG` (`MaTG`);

--
-- Chỉ mục cho bảng `sach_theloai`
--
ALTER TABLE `sach_theloai`
  ADD PRIMARY KEY (`MaSach`,`MaTheLoai`),
  ADD KEY `MaTheLoai` (`MaTheLoai`);

--
-- Chỉ mục cho bảng `tacgia`
--
ALTER TABLE `tacgia`
  ADD PRIMARY KEY (`MaTG`);

--
-- Chỉ mục cho bảng `theloai`
--
ALTER TABLE `theloai`
  ADD PRIMARY KEY (`MaTheLoai`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `fk_users_roles` (`roleID`);

--
-- Chỉ mục cho bảng `yeu_thich_sach`
--
ALTER TABLE `yeu_thich_sach`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userID_2` (`userID`,`MaSach`),
  ADD KEY `userID` (`userID`,`MaSach`),
  ADD KEY `MaSach` (`MaSach`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chuong`
--
ALTER TABLE `chuong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `lich_su_doc`
--
ALTER TABLE `lich_su_doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT cho bảng `lich_su_tai`
--
ALTER TABLE `lich_su_tai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `roleID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `yeu_thich_sach`
--
ALTER TABLE `yeu_thich_sach`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chuong`
--
ALTER TABLE `chuong`
  ADD CONSTRAINT `chuong_ibfk_1` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD CONSTRAINT `danh_gia_ibfk_1` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`) ON DELETE CASCADE,
  ADD CONSTRAINT `danh_gia_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lich_su_doc`
--
ALTER TABLE `lich_su_doc`
  ADD CONSTRAINT `lich_su_doc_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_su_doc_ibfk_2` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_su_doc_ibfk_3` FOREIGN KEY (`chuong_id`) REFERENCES `chuong` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lich_su_tai`
--
ALTER TABLE `lich_su_tai`
  ADD CONSTRAINT `lich_su_tai_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_su_tai_ibfk_2` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`) ON DELETE CASCADE,
  ADD CONSTRAINT `lich_su_tai_ibfk_3` FOREIGN KEY (`chuong_id`) REFERENCES `chuong` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `sach_tacgia`
--
ALTER TABLE `sach_tacgia`
  ADD CONSTRAINT `sach_tacgia_ibfk_1` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`),
  ADD CONSTRAINT `sach_tacgia_ibfk_2` FOREIGN KEY (`MaTG`) REFERENCES `tacgia` (`MaTG`);

--
-- Các ràng buộc cho bảng `sach_theloai`
--
ALTER TABLE `sach_theloai`
  ADD CONSTRAINT `sach_theloai_ibfk_1` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`),
  ADD CONSTRAINT `sach_theloai_ibfk_2` FOREIGN KEY (`MaTheLoai`) REFERENCES `theloai` (`MaTheLoai`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`roleID`) REFERENCES `roles` (`roleID`);

--
-- Các ràng buộc cho bảng `yeu_thich_sach`
--
ALTER TABLE `yeu_thich_sach`
  ADD CONSTRAINT `yeu_thich_sach_ibfk_1` FOREIGN KEY (`MaSach`) REFERENCES `sach` (`MaSach`),
  ADD CONSTRAINT `yeu_thich_sach_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
