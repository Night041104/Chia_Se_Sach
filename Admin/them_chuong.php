<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Chương Sách</title>
</head>
<style>
    /* Bê nguyên style từ code mẫu them_sach.php */
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
</style>
<body>  
    <?php     
        $masach = isset($_GET['masach'])? $_GET['masach']:'';
        
        $ten_sach_hien_thi = "Chưa chọn sách";
        if (!empty($masach)) {
            $res = mysqli_query($conn, "SELECT TenSach FROM sach WHERE MaSach='$masach'");
            if(mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_array($res);
                $ten_sach_hien_thi = $row['TenSach'];
            }
        }
        $msg = "";
        if(isset($_POST["them"]))
        {
            $tenchuong = mysqli_real_escape_string($conn, $_POST["tenchuong"]);
            $filename = $_FILES["file_chuong"]["name"];
            
            if(empty($masach) || empty($tenchuong) || empty($filename))
                $msg = "<p style='color:red;'>⚠️ Vui lòng nhập đầy đủ thông tin</p>";
            else
            {             
                // Xử lý upload file
                $file_tmp = $_FILES['file_chuong']['tmp_name'];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if($file_ext != "pdf") {
                    $msg = "<p style='color:red;'>❌ Lỗi: Chỉ chấp nhận file PDF.</p>";
                } else {
                    // Đổi tên file tránh trùng
                    $new_filename = $masach . "_" . time() . "_" . $filename;
                    $uploadPath = "File_sach/" . $new_filename; 

                    if(move_uploaded_file($file_tmp, $uploadPath)) {
                        
                        // INSERT vào bảng CHUONG (Không có LuotDoc, LuotTai vì mặc định 0)
                        $sqlInsert = "INSERT INTO chuong(MaSach, TenChuong, FilePath, NgayDang)
                                      VALUES ('$masach','$tenchuong','$new_filename', NOW())";
                        
                        if(mysqli_query($conn, $sqlInsert)) {
                           $msg = "<p style='color:green;'>✅ Thêm chương thành công.</p>";
                            // Xóa POST để không hiện lại tên chương vừa thêm
                            unset($_POST['tenchuong']);
                        } else {
                            $msg = "<p style='color:red;'>❌ Lỗi SQL: " . mysqli_error($conn) . "</p>";
                            unlink($uploadPath);
                        }
                    } else {
                       $msg = "<p style='color:red;'>❌ Không thể upload file (kiểm tra quyền thư mục).</p>";
                    }
                }
            }
        }
        
        // --- XỬ LÝ XÓA CHƯƠNG ---
        if(isset($_GET['xoa_id'])) {
            $id_xoa = intval($_GET['xoa_id']);//ép về kiểu số nguyên
            $res_f = mysqli_query($conn, "SELECT FilePath FROM chuong WHERE id='$id_xoa'");
            $row_f = mysqli_fetch_array($res_f);
            if ($row_f) {
                if(file_exists("File_sach/" . $row_f['FilePath'])) unlink("File_sach/" . $row_f['FilePath']);
                mysqli_query($conn, "DELETE FROM chuong WHERE id='$id_xoa'");
                echo "<script>window.location.href='them_chuong.php?masach=$masach';</script>";
            }
        }
    ?>
     <form method="post" name="themChuong" action="" enctype="multipart/form-data">
        <input type="hidden" name="masach" value="<?php echo $masach; ?>">
        
        <table align="center">
            <tr>
                <th colspan="2">THÊM CHƯƠNG MỚI</th>
            </tr>
            <?php if(!empty($msg)) echo "<tr><td colspan='2'>$msg</td></tr>"; ?>
            <tr>
                <td colspan="2" align="center" style="font-weight:bold; color:#ae1c55;">
                    Sách: <?php echo $ten_sach_hien_thi; ?> (Mã: <?php echo $masach; ?>)
                </td>
            </tr>
            
            <tr>
                <td>
                    <label>Tên chương:</label>
                </td>
                <td>
                    <input type="text" name="tenchuong" 
                           value="<?php if(isset($_POST['tenchuong'])) echo $_POST['tenchuong']; ?>" required>
                </td>
            </tr>

            <tr>
                <td>
                    <label>File chương (PDF):</label>
                </td>
                <td>
                    <input type="file" name="file_chuong" accept=".pdf" required>
                </td>
            </tr>
            
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="them" value="Thêm Chương" style="font-weight:bold; cursor:pointer;">
                </td>
            </tr>
        </table>
    </form>

    <p align='center'><font size='5px' style='font-weight: bold;'>DANH SÁCH CHƯƠNG</font></p>
    <table align='center' width='900' border='1' cellpadding='2'>
        <tr style="color: #ae1c55; font-size:18px;">
            <th style="font-size:20px">Tên Chương</th>
            <th style="font-size:20px">Tên File</th>
            <th style="font-size:20px">Ngày Đăng</th>
            <th style="font-size:20px">Lượt Đọc</th>
            <th style="font-size:20px">Lượt Tải</th>
            <th style="font-size:20px">Xóa</th>
        </tr>
        
        <?php
        if ($masach) {
            $sql_list = "SELECT * FROM chuong WHERE MaSach='$masach' ORDER BY id ASC";
            $result_list = mysqli_query($conn, $sql_list);
            
            if(mysqli_num_rows($result_list) <> 0)
            {
                $cnt = 0;
                $bg = "";
                while($rows = mysqli_fetch_array($result_list))
                {
                    $cnt++;
                    if($cnt <= 1) $bg = "#fee0c1"; else $bg = "#ffffff";
                    
                    echo "<tr style='background-color: $bg;'>";
                        echo "<td align='center'>{$rows['TenChuong']}</td>";
                        echo "<td>{$rows['FilePath']}</td>";
                        echo "<td align='center'>{$rows['NgayDang']}</td>";
                        echo "<td align='center'>{$rows['LuotDoc']}</td>";
                        echo "<td align='center'>{$rows['LuotTai']}</td>";
                        
                        // Nút Xóa
                        echo "<td align='center'>
                                <a href='them_chuong.php?masach=$masach&xoa_id={$rows['id']}' 
                                   onclick=\"return confirm('Xóa chương này?');\" 
                                   style='color:red;'>Xóa</a>
                              </td>";
                    echo "</tr>";
                    
                    if($cnt==2) $cnt = 0;
                }
            } else {
                echo "<tr><td colspan='5' align='center'>Chưa có chương nào.</td></tr>";
            }
        }
        
        if(isset($conn)) mysqli_close($conn);
        ?>
    </table>
    
   <div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="admin_index_sach.php" 
        style="
            color: #ae1c55; 
            font-weight: bold; 
            font-size: 14px; 
            border: 2px solid #ae1c55; 
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
        ">
        &laquo; Quay Về Danh Sách
    </a>
    </div>

</body>
</html>