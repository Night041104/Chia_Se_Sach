<?php # Script chi_tiet_sach.php
include ('includes/db_connect.php');

// 1. LẤY MÃ SÁCH TỪ URL VÀ KẾT NỐI CSDL
$masach = isset($_GET['masach']) ? trim($_GET['masach']) : ''; 


$book_found = false;
$page_title = 'Không tìm thấy sách'; 
$chapters = []; 

// 2. TRUY VẤN CSDL (ĐÃ TỐI ƯU HÓA)
if (!empty($masach)) {
    
    // --- [QUAN TRỌNG] GỘP 3 TRUY VẤN THÀNH 1 ---
    // Sử dụng kỹ thuật: CONCAT(Ma, '$', Ten) để gói dữ liệu lại
    // Sau đó GROUP_CONCAT để nối các gói lại với nhau
    $sql_sach = "SELECT s.*, 
                        GROUP_CONCAT(DISTINCT CONCAT(tg.MaTG, '$', tg.TenTG) SEPARATOR '|') as ListTacGia,
                        GROUP_CONCAT(DISTINCT CONCAT(tl.MaTheLoai, '$', tl.TenTheLoai) SEPARATOR '|') as ListTheLoai
                 FROM sach s
                 LEFT JOIN sach_tacgia stg ON s.MaSach = stg.MaSach
                 LEFT JOIN tacgia tg ON stg.MaTG = tg.MaTG
                 LEFT JOIN sach_theloai stl ON s.MaSach = stl.MaSach
                 LEFT JOIN theloai tl ON stl.MaTheLoai = tl.MaTheLoai
                 WHERE s.MaSach = '$masach'
                 GROUP BY s.MaSach";
                 
    $result_sach = mysqli_query($conn, $sql_sach);
    
    if (mysqli_num_rows($result_sach) > 0) {
        $row_sach = mysqli_fetch_array($result_sach);
        $page_title = $row_sach['TenSach']; 
        $book_found = true;
        
        // QUERY CHƯƠNG (Vẫn giữ riêng vì nó là danh sách dọc, không gộp được)
        $sql_chuong = "SELECT id, TenChuong, FilePath FROM chuong WHERE MaSach = '$masach' ORDER BY id ASC";
        $result_chuong = mysqli_query($conn, $sql_chuong);
        
        while ($row_c = mysqli_fetch_assoc($result_chuong)) {
            $chapters[] = $row_c;
        }
    }
    
    // Kiểm tra yêu thích
    $check_yeuthich= false;
    session_start();
    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
        $sql_check = "SELECT * FROM yeu_thich_sach where MaSach = '$masach' and userID = '$user_id'";
        $result_yeuthich = mysqli_query($conn, $sql_check);
        if(mysqli_num_rows($result_yeuthich)>0){
            $check_yeuthich=true;
        }
    }
}

// 3. INCLUDE HEADER
include ("includes/header.php"); 

// 4. HIỂN THỊ NỘI DUNG
if ($book_found) {
    
    $hinh_path = "Admin/Hinh_sach/" . $row_sach['Hinh'];
    
    // Logic nút Đọc
    $doc_link = "#"; 
    $alert_script = "onclick=\"alert('Sách này chưa có chương nào để đọc!'); return false;\"";
    
    if (!empty($chapters)) {
        $first_chapter_id = $chapters[0]['id'];
        $doc_link = "doc_sach.php?id=" . $first_chapter_id;
        $alert_script = ""; 
    }

    $danh_gia_link = "danh_gia.php?masach=" . $row_sach['MaSach'];

    // Bảng layout
    echo "<table class='book-detail-table'>";
    echo "<tr>";

    // CỘT 1: ẢNH BÌA
    echo "<td class='book-detail-image-cell'>";
    echo "<img src='{$hinh_path}' alt='" . $row_sach['TenSach'] . "' />";
    echo "</td>";

    // CỘT 2: THÔNG TIN CHI TIẾT
    echo "<td class='book-detail-info-cell'>";
    
    // Tên sách
    echo "<h1>" . $row_sach['TenSach'] . "</h1>";
    
    // --- [XỬ LÝ HIỂN THỊ TÁC GIẢ] ---
    echo "<div><b>Tác giả:</b> ";
    if (!empty($row_sach['ListTacGia'])) {
        // Tách chuỗi "TG01$Nam Cao|TG02$Tô Hoài" thành mảng
        $arr_tg = explode('|', $row_sach['ListTacGia']);
        $links = [];
        
        foreach($arr_tg as $tg_item) {
            // Tách tiếp "TG01$Nam Cao" -> [0]=>TG01, [1]=>Nam Cao
            $parts = explode('$', $tg_item);
            if(count($parts) == 2) {
                $links[] = "<a href='danh_muc_tac_gia.php?matg={$parts[0]}' class='book-tacgia-link'>{$parts[1]}</a>";
            }
        }
        echo implode(' ', $links); 
    } else {
        echo "Chưa cập nhật";
    }
    echo "</div>";

    // --- [XỬ LÝ HIỂN THỊ THỂ LOẠI] ---
    echo "<div><b>Thể loại:</b> ";
    if (!empty($row_sach['ListTheLoai'])) {
        $arr_tl = explode('|', $row_sach['ListTheLoai']);
        foreach($arr_tl as $tl_item) {
            $parts = explode('$', $tl_item);
            if(count($parts) == 2) {
                echo "<a href='danh_muc_the_loai.php?matheloai={$parts[0]}' class='book-theloai-link'>{$parts[1]}</a> ";
            }
        }
    } else {
        echo "Chưa cập nhật";
    }
    echo "</div>";

    // Hiển thị tình trạng
    $tinh_trang = !empty($row_sach['TinhTrang']) ? $row_sach['TinhTrang'] : "Đang tiến hành";
    echo "<div style='margin-top: 5px;'><b>Tình trạng:</b> <span style='color: #28a745; font-weight: bold;'>$tinh_trang</span></div>";

    // Thống kê
    echo "<div class='book-stats'>";
    echo "<span>👁️ Lượt đọc: " . $row_sach['LuotDoc'] . "</span> | ";
    echo "<span>📥 Lượt tải: " . $row_sach['LuotTai'] . "</span>";
    echo "</div>";
    
    // Khối nút bấm
    echo "<br>";
    echo "<a href='{$doc_link}' $alert_script ><i class='fa fa-book'></i> ĐỌC NGAY</a>";
    echo "<a href='{$danh_gia_link}' ><i class='fa fa-star'></i> ĐÁNH GIÁ</a>";

    if (isset($_SESSION['username'])) {
        $heart_color = $check_yeuthich ? '#e91e63' : '#555';
        $heart_icon  = $check_yeuthich ? 'fas' : 'far';
        $text_status = $check_yeuthich ? 'Đã thích' : 'Yêu thích';
        
        echo "<a href='yeu_thich_sach.php?masach=$masach' class='action-item heart' style='text-decoration:none; color:$heart_color'>
                <i class='$heart_icon fa-heart' style='color:$heart_color'></i>
                <span>$text_status</span>
              </a>";
    } else {
        // [Đã sửa lỗi khoảng trắng]
        echo "<a href='dang_nhap.php?masach=$masach&from=yeu_thich_sach.php' class='action-item heart' style='text-decoration:none;'>
                <i class='far fa-heart'></i>
                <span>Yêu thích</span>
              </a>";
    }
    
    echo "<hr style='margin-top: 15px;'>"; 

    // Mô tả
    echo "<h3 class='book-description-title'>Mô tả chi tiết:</h3>";
    echo "<p class='book-description-text'>" . nl2br($row_sach['MoTa']) . "</p>";

    // Danh sách chương (Giữ nguyên)
    echo "<div class='chapter-section'>";
    echo "<h3 class='book-description-title'>Danh sách chương:</h3>";
    
    if (!empty($chapters)) {
        echo "<ul class='chapter-list-style' style='list-style: none; padding: 0;'>";
        foreach ($chapters as $chap) {
            $link_doc_chuong = "doc_sach.php?id=" . $chap['id'];
            $link_tai_chuong = "tai_sach.php?id=" . $chap['id']; 

            echo "<li style='border-bottom: 1px solid #eee; padding: 8px 0; display: flex; justify-content: space-between; align_items: center;'>";
            echo "<a href='{$link_doc_chuong}' style='text-decoration: none; color: #333; font-weight: 500;'>
                    <i class='fa fa-file-text-o'></i> " . $chap['TenChuong'] . "
                  </a>";
            echo "<a href='{$link_tai_chuong}' style='font-size: 0.9em; color: #007bff; margin-left: 10px;'>
                    <i class='fa fa-download'></i> Tải về
                  </a>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p><i>Chưa có chương nào được cập nhật cho sách này.</i></p>";
    }
    echo "</div>";
    
    echo "</td>";
    echo "</tr>";   
    echo "</table>";

    // ... (Code Bình luận ở đây) ...

} else {
    echo '<h1 align="center">Lỗi</h1>';
    echo '<p align="center">Không tìm thấy cuốn sách bạn yêu cầu.</p>';
}

mysqli_close($conn);
include ('includes/footer.html');
?>