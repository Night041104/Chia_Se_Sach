<?php
session_start(); 

// 1. Bảo vệ
if (!isset($_SESSION['username'])) {
    header("Location: dang_nhap.php"); 
    exit();
}

// 2. Nhận ID Chương
// --- [SỬA] Đổi $_GET['machuong'] thành $_GET['id'] ---
if (isset($_GET['id'])) {
    $id_chuong = intval($_GET['id']);
    
    $conn = mysqli_connect("localhost","root","","chiasesach") or die("Lỗi kết nối");
    mysqli_set_charset($conn, 'UTF8');
    
    // 3. Lấy thông tin File từ bảng CHUONG
    // --- [SỬA] WHERE id = '$id_chuong' ---                                                                                  
    $sql_file = "SELECT FilePath, MaSach FROM chuong WHERE id = '$id_chuong'";
    $result_file = mysqli_query($conn, $sql_file);
    
    if (mysqli_num_rows($result_file) > 0) {
        $row = mysqli_fetch_array($result_file);
        $file_name = $row['FilePath'];
        $masach = $row['MaSach'];
        
        // Đường dẫn file trên Server
        $file_path_local = "Admin/File_sach/" . $file_name;
        
        if (file_exists($file_path_local)) {
            
            // 4. GHI LỊCH SỬ & TĂNG LƯỢT TẢI
            if (isset($_SESSION['user_id'])) {
                $uid = $_SESSION['user_id'];
                
                // Tăng lượt tải Sách & Chương
                mysqli_query($conn, "UPDATE sach SET LuotTai = LuotTai + 1 WHERE MaSach = '$masach'");
                mysqli_query($conn, "UPDATE chuong SET LuotTai = LuotTai + 1 WHERE id = '$id_chuong'");
                
                // Ghi lịch sử tải
                $sql_his = "INSERT INTO lich_su_tai (userID, MaSach, chuong_id, NgayTai) 
                            VALUES ('$uid', '$masach', '$id_chuong', NOW())
                            ON DUPLICATE KEY UPDATE NgayTai = NOW()";
                mysqli_query($conn, $sql_his);
            }

            // 5. Tải file về
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path_local) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path_local));

            readfile($file_path_local);
            
            mysqli_close($conn);
            exit();
        }
    }
}

// Xử lý lỗi
$page_title = 'Lỗi Tải File';
include ("includes/header.php");
echo '<h1 align="center">Lỗi</h1>';
echo '<p align="center">Không tìm thấy file chương này hoặc file không tồn tại trên hệ thống.</p>';
include ("includes/footer.html");
?>