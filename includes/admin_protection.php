<?php
// includes/admin_protection.php

// 1. Đảm bảo session đã bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kiểm tra: Đã đăng nhập chưa?
if (!isset($_SESSION['user_id'])) {
    // Chưa đăng nhập -> Đá về trang login
    header('Location: ../dang_nhap.php'); 
    exit(); // Quan trọng: Dừng code ngay lập tức
}

// 3. Kiểm tra: Có phải Admin (role_id = 1) không?
// (Giả sử trong login.php bạn đã lưu $_SESSION['role_id'])
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Đã đăng nhập nhưng KHÔNG phải Admin -> Đá về trang chủ
    echo "<script>
            alert('CẢNH BÁO: Bạn không có quyền truy cập trang này!');
            window.location.href = '../index.php';
          </script>";
    exit(); // Quan trọng: Dừng code ngay lập tức
}

// Nếu vượt qua được cả 2 cửa trên, nghĩa là Admin xịn -> Cho phép chạy tiếp
?>