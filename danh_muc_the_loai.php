<?php # Script danh_muc_the_loai.php

// --- 1. LẤY MÃ THỂ LOẠI VÀ THÔNG TIN ---
$matheloai = '';
if (isset($_GET['matheloai'])) {
    $matheloai = $_GET['matheloai'];
}

// Kết nối CSDL
$conn = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL");
mysqli_set_charset($conn, 'UTF8');

// Lấy Tên thể loại để làm tiêu đề
$category_name = "Không rõ";
if (!empty($matheloai)) {
    // Dùng style code của bạn
    $sql_title = "SELECT TenTheLoai FROM theloai WHERE MaTheLoai = '$matheloai'";
    $result_title = mysqli_query($conn, $sql_title);
    if (mysqli_num_rows($result_title) > 0) {
        $row_title = mysqli_fetch_array($result_title);
        $category_name = $row_title['TenTheLoai'];
    }
}

// 2. INCLUDE HEADER (SAU KHI ĐÃ CÓ TIÊU ĐỀ)
$page_title = 'Thể loại: ' . $category_name;
include ("includes/header.php"); // Giả sử bạn đã đổi tên file thành .php

?>



<h1 align="center">Thể loại: <?php echo $category_name; ?></h1>

<?php
// 5. TRUY VẤN VÀ HIỂN THỊ SÁCH
if (empty($matheloai)) {
    echo "<p align='center'>Vui lòng chọn một thể loại từ menu.</p>";
} else {
    // Sửa câu SQL: Dùng INNER JOIN để lọc sách theo thể loại
    $sql_books = "SELECT s.MaSach, s.TenSach, s.Hinh 
                  FROM sach s
                  INNER JOIN sach_theloai stl ON s.MaSach = stl.MaSach
                  WHERE stl.MaTheLoai = '$matheloai'
                  ORDER BY s.TenSach ASC";
                  
    $result_books = mysqli_query($conn, $sql_books);

    // 6. Kiểm tra xem có sách không
    if (mysqli_num_rows($result_books) > 0) {
        
        // Bắt đầu vùng chứa lưới
        echo "<div class='book-grid' align='center'>";

        // 7. Lặp qua từng cuốn sách
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
        echo "<p align='center'>Không tìm thấy sách nào thuộc thể loại này.</p>";
    }
} // Kết thúc else

// 9. Đóng kết nối
mysqli_close($conn);

?>
 <div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="index.php" 
        style="
            color: #ae1c55; 
            font-weight: bold; 
            font-size: 14px; 
            border: 2px solid #ae1c55; 
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
        ">
        &laquo; Quay Về Trang Chủ
    </a>
    </div>

<?php
include ("includes/footer.html"); 
?>