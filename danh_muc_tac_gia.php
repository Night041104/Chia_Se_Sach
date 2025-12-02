<?php # Script danh_muc_tac_gia.php

// --- 1. LẤY MÃ TÁC GIẢ VÀ THÔNG TIN ---
$matg = '';
if (isset($_GET['matg'])) { // Sửa: Lấy 'matg'
    $matg = $_GET['matg'];
}

// Kết nối CSDL
$conn = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL");
mysqli_set_charset($conn, 'UTF8');

// Lấy Tên tác giả để làm tiêu đề
$author_name = "Không rõ"; // Sửa: Tên biến
if (!empty($matg)) {
    // Sửa: Truy vấn bảng 'tacgia'
    $sql_title = "SELECT TenTG FROM tacgia WHERE MaTG = '$matg'";
    $result_title = mysqli_query($conn, $sql_title);
    if (mysqli_num_rows($result_title) > 0) {
        $row_title = mysqli_fetch_array($result_title);
        $author_name = $row_title['TenTG']; // Sửa: Lấy 'TenTG'
    }
}

// 2. INCLUDE HEADER (SAU KHI ĐÃ CÓ TIÊU ĐỀ)
$page_title = 'Tác giả: ' . $author_name; // Sửa: Tiêu đề trang
include ("includes/header.php"); 

?>

<!-- GIỮ NGUYÊN CSS TỪ FILE MẪU -->


<!-- SỬA TIÊU ĐỀ H1 -->
<h1 class="tentheloatacgia" align="center">Sách của tác giả: <?php echo $author_name; ?></h1>

<?php
// 5. TRUY VẤN VÀ HIỂN THỊ SÁCH
if (empty($matg)) { // Sửa: Kiểm tra 'matg'
    echo "<p align='center'>Vui lòng chọn một tác giả.</p>"; // Sửa: Thông báo
} else {
    $sql_motaTG = "SELECT MoTaTG from tacgia where MaTG = '$matg'";
    $result_mota = mysqli_query($conn, $sql_motaTG);
    $row = mysqli_fetch_array($result_mota);
    echo "<h4 class='motatacgia' style='text-align:justify;'>{$row['MoTaTG']}</h4>";
    // Sửa câu SQL: Dùng INNER JOIN với 'sach_tacgia'
    $sql_books = "SELECT s.MaSach, s.TenSach, s.Hinh 
                  FROM sach s
                  INNER JOIN sach_tacgia stg ON s.MaSach = stg.MaSach
                  WHERE stg.MaTG = '$matg'
                  ORDER BY s.TenSach ASC";
                  
    $result_books = mysqli_query($conn, $sql_books);

    // 6. Kiểm tra xem có sách không
    if (mysqli_num_rows($result_books) > 0) {
        
        // Bắt đầu vùng chứa lưới
        echo "<div class='book-grid' align='center'>";

        // 7. Lặp qua từng cuốn sách (Giữ nguyên)
        while ($row = mysqli_fetch_array($result_books)) {
            
            // Lấy đường dẫn ảnh
            $hinh_path = "Admin/Hinh_sach/" . $row['Hinh'];
            
            // Link để qua trang chi tiết
            $chi_tiet_sach = "chi_tiet_sach.php?masach=" . $row['MaSach'];
            
            // 8. Echo ra từng "ô" sách
            echo "<a href='{$chi_tiet_sach}'>";
            echo "<div class='book-cell'>";
            
            // Hiển thị Ảnh bìa
            echo "<img src='{$hinh_path}' alt='" . $row['TenSach'] . "' />";
            
            // Hiển thị Tên sách
            echo "<div class='book-title'>" . $row['TenSach'] . "</div>";
            
            echo "</div>"; // Đóng book-cell
            echo "</a>"; // Đóng link
        }
        
        echo "</div>"; // Đóng book-grid

    } else {
        echo "<p align='center'>Không tìm thấy sách nào của tác giả này.</p>"; // Sửa: Thông báo
    }
} // Kết thúc else

// 9. Đóng kết nối
mysqli_close($conn);

?>

<?php
// Nhớ lời bạn dặn: Dùng footer.html
include ("includes/footer.html"); 
?>