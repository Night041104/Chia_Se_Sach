<?php # Script dang_xuat.php

// 1. Bắt đầu session để có thể truy cập nó
session_start(); 

// 2. Hủy tất cả các biến session (như $_SESSION['username'])
session_unset();

// 3. Hủy hoàn toàn session trên server
session_destroy();

// 4. Chuyển người dùng về trang chủ
// (Đảm bảo không có code HTML hoặc lệnh 'echo' nào
// được chạy trước lệnh header() này)
header('Location: index.php');
exit(); // Dừng script ngay lập tức
?>