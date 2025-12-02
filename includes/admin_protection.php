<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../dang_nhap.php'); 
    exit(); 
}

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo "<script>
            alert('CẢNH BÁO: Bạn không có quyền truy cập trang này!');
            window.location.href = '../index.php';
          </script>";
    exit(); 
}

?>