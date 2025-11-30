<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Thông Tin Sách</title>
    <style>
        th { background-color: orange; color: red; }
        table { background-color: #fee0c1; }
        input, textarea { background-color: #fff2cc; }
    </style>
</head>
<body>
<?php
    $masach = $_REQUEST['masach'];

    // Lấy dữ liệu sách
    $sql = "SELECT * FROM sach WHERE MaSach = '$masach'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) <> 0) {
        $row = mysqli_fetch_array($result);
        $tenSach = $row['TenSach'];
        $moTa = $row['MoTa'];
        $old_hinh = $row['Hinh'];
    } else {
        die('Không tìm thấy sách trong CSDL!');
    }

    if(isset($_POST['sub']))
    {
        // 1. XÓA ẢNH BÌA SÁCH
        $hinh_path = "Hinh_sach/" . $old_hinh;
        if (file_exists($hinh_path)) {
            unlink($hinh_path);
        }
        
        // 2. XÓA TẤT CẢ FILE PDF CỦA CÁC CHƯƠNG
        $sql_get_files = "SELECT FilePath FROM chuong WHERE MaSach = '$masach'";
        $res_files = mysqli_query($conn, $sql_get_files);
        
        if (mysqli_num_rows($res_files) > 0) {
            while ($row_file = mysqli_fetch_array($res_files)) {
                $pdf_path = "File_sach/" . $row_file['FilePath'];
                if (file_exists($pdf_path)) {
                    unlink($pdf_path); // Xóa từng file PDF
                }
            }
        }
        
        // 3. XÓA SÁCH TRONG CSDL
        // (Do đã cài ON DELETE CASCADE, các bảng con như chuong, sach_tacgia... sẽ tự động bị xóa theo)
        $sql_delete = "DELETE from sach where MaSach = '$masach'";
        $result_delete = mysqli_query($conn, $sql_delete);
        
        if($result_delete)
        {
            echo "<p style='color:green;' align='center'>Đã xóa sách và toàn bộ chương/file liên quan.</p>";
            $masach = $tenSach = $moTa = "";
        }else
        {
            echo "<p style='color:red;' align='center'>Lỗi khi xóa sách: " . mysqli_error($conn) . "</p>";
        }
    }
    mysqli_close($conn);
?>

<form action="xoa_sach.php" method="POST">
    <table align="center" border="0" cellpadding="5">
        <tr><th colspan="2"><h2><b>XÓA THÔNG TIN SÁCH</b></h2></th></tr>
        <tr><td colspan="2" align="center" style="color:red; font-weight:bold;">CẢNH BÁO: Hành động này sẽ xóa Sách, Ảnh bìa và TOÀN BỘ CÁC CHƯƠNG (File PDF) của sách này!</td></tr>

        <tr>
            <td><label>Mã Sách:</label></td>
            <td><input type="text" name="masach" value="<?php echo $masach; ?>" style="width:250px;" readonly></td>
        </tr>

        <tr>
            <td><label>Tên Sách:</label></td>
            <td><input type="text" name="tensach" value="<?php echo $tenSach; ?>" style="width:250px;" disabled></td>
        </tr>
        
        <tr>
            <td><label>Mô Tả:</label></td>
            <td><textarea name="mota" rows="5" style="width:250px;" disabled><?php echo $moTa; ?></textarea></td>
        </tr>

        <tr><th colspan="2" align="center"><input type="submit" name="sub" value="XÁC NHẬN XÓA VĨNH VIỄN"></th></tr>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <a href="admin_index_sach.php"> Quay Về Trang Danh Sách</a>
    </div>
</form>
</body>
</html>