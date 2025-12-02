<?php
// Include header (chứa session_start, CSS, Navbar...)
$page_title = 'Tìm kiếm nâng cao';
include ('includes/header.php'); 
include ('includes/phan_trang.php');
include ('includes/db_connect.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm nâng cao</title>

</head>
<body>
    
    <form method="get" action="">
        <table align="center" width="60%" class="search-form-table">
            <tr>
                <th colspan="2">TÌM KIẾM SÁCH NÂNG CAO</th>
            </tr>
            <tr>
                <td>
                    <b>Tên sách:</b> 
                    <input type="text" name="tensach" class="search-input" size="30" value="<?php if(isset($_GET['tensach'])) echo $_GET['tensach'];?>">
                </td>
            </tr>
            <tr>
                <td>
                    <b>Thể loại:</b>
                    <select name="theloai" class="search-input">
                        <option value="">-- Tất cả --</option>
                        <?php    
                            
                            $sql_tl = "select MaTheLoai, TenTheLoai from theloai ORDER BY TenTheLoai ASC";
                            $result_tl = mysqli_query($conn, $sql_tl);
                            if(mysqli_num_rows($result_tl) > 0) {
                                while($row = mysqli_fetch_array($result_tl)) {
                                    $selected = (isset($_GET['theloai']) && $_GET['theloai'] == $row['MaTheLoai']) ? 'selected' : '';
                                    echo "<option value='{$row['MaTheLoai']}' $selected>{$row['TenTheLoai']}</option>";
                                }
                            }
                        ?>
                    </select>

                    &nbsp;&nbsp;&nbsp;

                    <b>Tác giả:</b>
                    <select name="tacgia" class="search-input">
                        <option value="">-- Tất cả --</option>
                        <?php
                            $sql_tg = "select MaTG, TenTG from tacgia ORDER BY TenTG ASC";
                            $result_tg = mysqli_query($conn, $sql_tg);
                            if(mysqli_num_rows($result_tg) > 0) {
                                while($row = mysqli_fetch_array($result_tg)) {
                                    $selected = (isset($_GET['tacgia']) && $_GET['tacgia'] == $row['MaTG']) ? 'selected' : '';
                                    echo "<option value='{$row['MaTG']}' $selected>{$row['TenTG']}</option>";
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="tim" value="Tìm Kiếm" class="btn-search-submit">
                </td>
            </tr>
        </table>
    </form>

    <div class="search-container">
    <?php
        if (isset($_GET['tim'])) 
        {
            $tensach = isset($_GET['tensach']) ? trim($_GET['tensach']) : '';
            $ma_theloai = isset($_GET['theloai']) ? $_GET['theloai'] : '';
            $ma_tacgia = isset($_GET['tacgia']) ? $_GET['tacgia'] : '';

            // Xây dựng câu truy vấn động (Dynamic SQL)
            // Vì người dùng có thể bỏ trống ô nhập liệu
            $sql = "SELECT s.*, 
                           GROUP_CONCAT(DISTINCT tg.TenTG SEPARATOR ', ') as DanhSachTacGia,
                           GROUP_CONCAT(DISTINCT tl.TenTheLoai SEPARATOR ',') as DanhSachTheLoai
                    FROM sach s
                    LEFT JOIN sach_tacgia stg ON s.MaSach = stg.MaSach
                    LEFT JOIN tacgia tg ON stg.MaTG = tg.MaTG
                    LEFT JOIN sach_theloai stl ON s.MaSach = stl.MaSach
                    LEFT JOIN theloai tl ON stl.MaTheLoai = tl.MaTheLoai
                    WHERE 1=1"; // Mẹo: 1=1 để dễ nối chuỗi AND phía sau

            if (!empty($tensach)) {
                $safe_ten = mysqli_real_escape_string($conn, $tensach);
                $sql .= " AND s.TenSach LIKE '%$safe_ten%'";
            }
            if (!empty($ma_theloai)) {
                // Phải tìm trong bảng trung gian vì sách có nhiều thể loại
                // Dùng sub-query để chính xác: Tìm sách nào CÓ chứa mã thể loại này
                $sql .= " AND s.MaSach IN (SELECT MaSach FROM sach_theloai WHERE MaTheLoai = '$ma_theloai')";
            }
            if (!empty($ma_tacgia)) {
                $sql .= " AND s.MaSach IN (SELECT MaSach FROM sach_tacgia WHERE MaTG = '$ma_tacgia')";
            }

            $sql .= " GROUP BY s.MaSach"; // Gom nhóm để GROUP_CONCAT hoạt động

            $result = mysqli_query($conn, $sql);

            if(mysqli_num_rows($result) <> 0) 
            {   
                $count = mysqli_num_rows($result);
                echo "<p align='center' style='margin-bottom:20px;'><b>Có $count sách được tìm thấy</b></p>";
                
                while($row = mysqli_fetch_array($result))
                {
                    // --- BẮT ĐẦU IN GIAO DIỆN ĐẸP (CARD NGANG) ---
                    $hinh_path = "Admin/Hinh_sach/" . $row['Hinh'];
                    $link_sach = "chi_tiet_sach.php?masach=" . $row['MaSach'];
                    
                    // Xử lý mô tả ngắn
                    $mota_ngan = strip_tags($row['MoTa']);
                    if (strlen($mota_ngan) > 300) $mota_ngan = mb_substr($mota_ngan, 0, 300, 'UTF-8') . "...";

                    // Xử lý tình trạng
                    $status_class = "";
                    if($row['TinhTrang'] == 'Đã hoàn thành') $status_class = "hoan-thanh";
                    elseif($row['TinhTrang'] == 'Tạm ngưng') $status_class = "tam-ngung";

                    // Tách thể loại
                    $theloai_arr = explode(',', $row['DanhSachTheLoai']);

                    echo "<div class='search-card'>";
                    // Cột trái
                    echo "  <div class='card-left'>";
                    echo "      <a href='$link_sach'><img src='$hinh_path' alt='{$row['TenSach']}'></a>";
                    echo "      <div class='status-label $status_class'>{$row['TinhTrang']}</div>";
                    echo "  </div>";

                    // Cột phải
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
                    // --- KẾT THÚC CARD ---
                }
            }
            else 
            {
                echo "<div align='center' style='padding:30px;'>
                        <i class='fa fa-search' style='font-size:30px; color:#ccc;'></i><br>
                        <b>Không tìm thấy sách nào phù hợp!</b>
                      </div>";
            }
        }
        
        // Đóng kết nối
        mysqli_close($conn);
    ?>
    </div>

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
        
</body>
</html>

<?php include ("includes/footer.html"); ?>