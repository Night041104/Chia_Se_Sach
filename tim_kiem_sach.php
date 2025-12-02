<?php
// 1. LẤY TỪ KHÓA TỪ URL (Đã đổi thành tensach)
$search_query = "";
if (isset($_GET['tensach'])) {
    $search_query = trim($_GET['tensach']);
}

$page_title = 'Tìm kiếm: ' . htmlspecialchars($search_query);
include ("includes/header.php");
include ('includes/db_connect.php');
?>



<div class="search-container">

    <?php
    if ($search_query != "") {
        $safe_query = mysqli_real_escape_string($conn, $search_query);

        $sql = "SELECT s.*, 
                       GROUP_CONCAT(DISTINCT tg.TenTG SEPARATOR ', ') as DanhSachTacGia,
                       GROUP_CONCAT(DISTINCT tl.TenTheLoai SEPARATOR ',') as DanhSachTheLoai
                FROM sach s
                LEFT JOIN sach_tacgia stg ON s.MaSach = stg.MaSach
                LEFT JOIN tacgia tg ON stg.MaTG = tg.MaTG
                LEFT JOIN sach_theloai stl ON s.MaSach = stl.MaSach
                LEFT JOIN theloai tl ON stl.MaTheLoai = tl.MaTheLoai
                WHERE s.TenSach LIKE '%$safe_query%'
                GROUP BY s.MaSach";
        
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            
            echo "<h2 class='search-heading'>Kết quả tìm kiếm: \"$search_query\" (" . mysqli_num_rows($result) . ")</h2>";
            
            while ($row = mysqli_fetch_array($result)) {
                $hinh_path = "Admin/Hinh_sach/" . $row['Hinh'];
                $link_sach = "chi_tiet_sach.php?masach=" . $row['MaSach'];
                
                $mota_ngan = strip_tags($row['MoTa']);
                if (strlen($mota_ngan) > 300) $mota_ngan = mb_substr($mota_ngan, 0, 300, 'UTF-8') . "...";

                $status_class = "";
                if($row['TinhTrang'] == 'Đã hoàn thành') $status_class = "hoan-thanh";
                elseif($row['TinhTrang'] == 'Tạm ngưng') $status_class = "tam-ngung";

                $theloai_arr = explode(',', $row['DanhSachTheLoai']);

                echo "<div class='search-card'>";
                echo "  <div class='card-left'>";
                echo "      <a href='$link_sach'><img src='$hinh_path' alt='{$row['TenSach']}'></a>";
                echo "      <div class='status-label $status_class'>{$row['TinhTrang']}</div>";
                echo "  </div>";

                echo "  <div class='card-right'>";
                echo "      <h3 class='card-title'><a href='$link_sach'>{$row['TenSach']}</a></h3>";
                
                echo "      <div class='card-tags'>";
                if(!empty($row['DanhSachTheLoai'])) {
                    foreach($theloai_arr as $tl_ten) {
                        echo "<span class='genre-tag'>$tl_ten</span>";
                    }
                }
                echo "      </div>";

                echo "      <div class='card-meta'>
                                <i class='fa fa-pen-nib'></i> Tác giả: <b>" . ($row['DanhSachTacGia'] ? $row['DanhSachTacGia'] : 'Đang cập nhật') . "</b>
                            </div>";

                echo "      <div class='card-desc'>$mota_ngan</div>";
                echo "      <a href='$link_sach' class='btn-detail'>Đọc tiếp</a>";
                echo "  </div>";
                echo "</div>";
            }

        } else {
            echo "<div style='text-align:center; padding: 50px; background:#fff; border-radius:8px;'>
                    <i class='fa fa-search' style='font-size: 3em; margin-bottom: 15px; color:#ddd;'></i>
                    <p style='color:#777;'>Rất tiếc, không tìm thấy cuốn sách nào có tên: <b>'$search_query'</b></p>
                    <a href='index.php' style='color: #ae1c55; font-weight: bold;'>Quay về trang chủ</a>
                  </div>";
        }

    } else {
        echo "<p class='error' align='center'>Vui lòng nhập từ khóa để tìm kiếm.</p>";
    }

    mysqli_close($conn);
    ?>

</div>

<?php include ("includes/footer.html"); ?>