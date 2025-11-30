<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Sách</title>
</head>
<style>
    /* Bê nguyên style từ code mẫu */
    th{
        font-size: 30px;
        background-color: #ae1c55;
        color: white;
    }
    table {
        background-color: #fddedc;
    }
    td{
        padding: 5px;
    }
    .checkbox-list {
        max-height: 100px;
        overflow-y: auto;
        border: 1px solid #ccc;
        padding: 5px;
        background-color: white;
    }
    .file-info { font-size: 12px; font-style: italic; color: #555; }
</style>
<body>
<?php
    // 1. LẤY MÃ SÁCH TỪ URL HOẶC FORM
    if (isset($_GET['masach'])) {
        $masach = $_GET['masach'];
    } elseif (isset($_POST['masach'])) {
        $masach = $_POST['masach'];
    } else {
        echo "Lỗi: Không tìm thấy Mã Sách!";
        exit();
    }

    // 2. LẤY THÔNG TIN CƠ BẢN CỦA SÁCH
    $sql = "SELECT * FROM sach WHERE MaSach = '$masach'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) <> 0) {
        $row = mysqli_fetch_array($result);
        $tenSach = $row['TenSach'];
        $moTa = $row['MoTa'];
        $old_hinh = $row['Hinh'];
        $tinhTrang = $row['TinhTrang'];
    } else {
        die('Không tìm thấy sách trong CSDL!');
    }

    // 3. LẤY MẢNG TÁC GIẢ & THỂ LOẠI HIỆN TẠI (Để tí nữa check vào ô)
    // Tác giả
    $arr_tacgia_cu = array();
    $rs_tg = mysqli_query($conn, "SELECT MaTG FROM sach_tacgia WHERE MaSach = '$masach'");
    while ($r = mysqli_fetch_array($rs_tg)) $arr_tacgia_cu[] = $r['MaTG'];

    // Thể loại
    $arr_theloai_cu = array();
    $rs_tl = mysqli_query($conn, "SELECT MaTheLoai FROM sach_theloai WHERE MaSach = '$masach'");
    while ($r = mysqli_fetch_array($rs_tl)) $arr_theloai_cu[] = $r['MaTheLoai'];


    // --- XỬ LÝ KHI BẤM CẬP NHẬT ---
    if(isset($_POST["capnhat"]))
    {
        $tensach_new = mysqli_real_escape_string($conn, $_POST["tensach"]);
        $mota_new = mysqli_real_escape_string($conn, $_POST["mota"]);
        $tinhtrang_new = $_POST["tinhtrang"];
        
        $tacgia_arr = $_POST["tacgia"] ?? []; 
        $theloai_arr = $_POST["theloai"] ?? []; 

        $hinh_name = $old_hinh;
        $hinh_da_upload = false;

        // Xử lý upload ảnh (nếu có chọn ảnh mới)
        if (isset($_FILES['hinh']) && $_FILES['hinh']['error'] == 0 && !empty($_FILES['hinh']['name'])) {
            $hinh_name_new = $_FILES['hinh']['name'];
            $hinh_tmp = $_FILES['hinh']['tmp_name'];
            $hinh_ext = strtolower(pathinfo($hinh_name_new, PATHINFO_EXTENSION));
            
            if(in_array($hinh_ext, ['jpg', 'jpeg', 'png'])) {
                $uploadPath_hinh = "Hinh_sach/" . $hinh_name_new; 
                if (move_uploaded_file($hinh_tmp, $uploadPath_hinh)) {
                    $hinh_name = $hinh_name_new;
                    $hinh_da_upload = true;
                }
            } else {
                echo "<p style='color:red;' align='center'>❌ Lỗi: Ảnh phải là JPG hoặc PNG.</p>";
            }
        }

        // CẬP NHẬT BẢNG SÁCH
        $sql_update = "UPDATE sach 
                       SET TenSach='$tensach_new', MoTa='$mota_new', Hinh='$hinh_name', TinhTrang='$tinhtrang_new'
                       WHERE MaSach='$masach'";
                       
        if(mysqli_query($conn, $sql_update)) {
            
            // Xóa ảnh cũ nếu có ảnh mới
            if ($hinh_da_upload && $old_hinh != $hinh_name && file_exists("Hinh_sach/" . $old_hinh)) {
                unlink("Hinh_sach/" . $old_hinh);
            }

            // --- CẬP NHẬT TÁC GIẢ (XÓA HẾT -> THÊM LẠI) ---
            mysqli_query($conn, "DELETE FROM sach_tacgia WHERE MaSach='$masach'");
            foreach($tacgia_arr as $matg) {
                mysqli_query($conn, "INSERT INTO sach_tacgia(MaSach, MaTG) VALUES ('$masach', '$matg')");
            }

            // --- CẬP NHẬT THỂ LOẠI (XÓA HẾT -> THÊM LẠI) ---
            mysqli_query($conn, "DELETE FROM sach_theloai WHERE MaSach='$masach'");
            foreach($theloai_arr as $matl) {
                mysqli_query($conn, "INSERT INTO sach_theloai(MaSach, MaTheLoai) VALUES ('$masach', '$matl')");
            }

            echo "<script>alert('Cập nhật sách thành công!'); window.location.href='sua_sach.php?masach=$masach';</script>";
            
        } else {
            echo "<p style='color:red;' align='center'>❌ Lỗi CSDL: ".mysqli_error($conn)."</p>";
        }
    }
?>
    
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="masach" value="<?php echo $masach; ?>">

        <table align="center">
            <tr>
                <th colspan="2">CẬP NHẬT THÔNG TIN SÁCH</th>
            </tr>
            <tr>
                <td><label>Mã sách:</label></td>
                <td><input type="text" value="<?php echo $masach; ?>" disabled style="background:#eee;"></td>
            </tr>
            <tr>
                <td><label>Tên sách:</label></td>
                <td><input type="text" name="tensach" value="<?php echo $tenSach; ?>" style="width:300px;" required></td>
            </tr>

            <tr>
                <td><label>Tác giả:</label></td>
                <td>
                    <div class="checkbox-list">
                        <?php
                            $sql_all_tg = "SELECT * FROM tacgia ORDER BY TenTG ASC";
                            $res_all_tg = mysqli_query($conn, $sql_all_tg);
                            while ($row_tg = mysqli_fetch_array($res_all_tg)) {
                                // Kiểm tra nếu có trong mảng cũ thì check
                                $is_checked = in_array($row_tg['MaTG'], $arr_tacgia_cu) ? "checked" : "";
                                echo "<label><input type='checkbox' name='tacgia[]' value='{$row_tg['MaTG']}' $is_checked> {$row_tg['TenTG']}</label><br>";
                            }
                        ?>
                    </div>
                </td>
            </tr>
            
            <tr>
                <td><label>Thể loại:</label></td>
                <td>
                    <div class="checkbox-list">
                        <?php
                            $sql_all_tl = "SELECT * FROM theloai ORDER BY TenTheLoai ASC";
                            $res_all_tl = mysqli_query($conn, $sql_all_tl);
                            while ($row_tl = mysqli_fetch_array($res_all_tl)) {
                                $is_checked = in_array($row_tl['MaTheLoai'], $arr_theloai_cu) ? "checked" : "";
                                echo "<label><input type='checkbox' name='theloai[]' value='{$row_tl['MaTheLoai']}' $is_checked> {$row_tl['TenTheLoai']}</label><br>";
                            }
                        ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td><label>Mô tả:</label></td>
                <td><textarea name="mota" rows="4" cols="55"><?php echo $moTa; ?></textarea></td>
            </tr>
            
            <tr>
                <td><label>Tình trạng:</label></td>
                <td>
                    <select name="tinhtrang">
                        <option value="Đang tiến hành" <?php if($tinhTrang == 'Đang tiến hành') echo 'selected'; ?>>Đang tiến hành</option>
                        <option value="Đã hoàn thành" <?php if($tinhTrang == 'Đã hoàn thành') echo 'selected'; ?>>Đã hoàn thành</option>
                        <option value="Tạm ngưng" <?php if($tinhTrang == 'Tạm ngưng') echo 'selected'; ?>>Tạm ngưng</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><label>Ảnh bìa:</label></td>
                <td>
                    <input type="file" name="hinh">
                    <br><span class="file-info">Hiện tại: <?php echo $old_hinh; ?></span>
                </td>
            </tr>
            
            <tr>
                <td><label>Nội dung:</label></td>
                <td>
                    <a href="them_chuong.php?masach=<?php echo $masach; ?>" style="color:blue; font-weight:bold;">
                        [📂 Quản lý chương & PDF]
                    </a>
                </td>
            </tr>

            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="capnhat" value="Cập Nhật">
                </td>
            </tr>
        </table>
    </form>

    <div style="text-align:center; margin-top:20px;">
        <a href="admin_index_sach.php">Quay Về Danh Sách</a>
    </div>

<?php
// Đóng kết nối
if(isset($conn)) mysqli_close($conn);
?>
</body>
</html>