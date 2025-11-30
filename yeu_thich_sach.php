<?php # Script yeu_thich_sach.php
session_start();

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dang_nhap.php');
    exit();
}

// 2. Kết nối CSDL
$conn = mysqli_connect("localhost","root","","chiasesach") or die("Lỗi kết nối");
mysqli_set_charset($conn, 'UTF8');

$user_id = $_SESSION['user_id'];
$masach = isset($_GET['masach']) ? trim($_GET['masach']) : '';
$from = isset($_GET['from']) ? $_GET['from'] : ''; // Biến để biết đang ở trang nào

if (!empty($masach)) {
    
    // 3. KIỂM TRA XEM ĐÃ THÍCH CHƯA
    $check = "SELECT id FROM yeu_thich_sach WHERE userID='$user_id' AND MaSach='$masach'";
    $rs = mysqli_query($conn, $check);

    if (mysqli_num_rows($rs) > 0) {
        // A. NẾU ĐÃ CÓ -> XÓA (BỎ THÍCH)
        $sql = "DELETE FROM yeu_thich_sach WHERE userID='$user_id' AND MaSach='$masach'";
        mysqli_query($conn, $sql);
    } else {
        // B. NẾU CHƯA CÓ -> THÊM (THÍCH)
        // Chỉ thêm nếu không phải đang gọi từ trang cá nhân (vì trang cá nhân chỉ có nút Bỏ thích)
        if ($from != 'profile') {
            $sql = "INSERT INTO yeu_thich_sach (userID, MaSach, ngayThich) VALUES ('$user_id', '$masach', NOW())";
            mysqli_query($conn, $sql);
        }
    }
}

// 4. ĐIỀU HƯỚNG VỀ LẠI TRANG CŨ
if ($from == 'profile') {
    // Nếu đến từ trang cá nhân -> Về lại trang cá nhân (tab yêu thích)
    header("Location: trang_ca_nhan.php?open_tab=danh_sach_yeu_thich");
} else {
    // Mặc định -> Về lại trang chi tiết sách
    header("Location: chi_tiet_sach.php?masach=$masach");
}

exit();
?>