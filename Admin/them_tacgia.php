<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm tác giả</title>
</head>
<style>
    /* Bê nguyên style từ them_sach.php */
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
    input[type="text"], textarea {
        width: 300px; /* Độ rộng chuẩn */
        padding: 5px;
    }
</style>
<body>
    
    <?php
        $msg = ""; 
        if(isset($_POST["them"]))
        {
            // Bảo mật dữ liệu đầu vào (Bước 3)
            $matg = mysqli_real_escape_string($conn, $_POST["matg"]);
            $tentg = mysqli_real_escape_string($conn, $_POST["tentg"]);
            $mota = mysqli_real_escape_string($conn, $_POST["mota"]);

            if(empty($matg) || empty($tentg) || empty($mota)) {
                $msg = "<p style='color:red;' align='center'>⚠️ Vui lòng nhập đầy đủ thông tin!</p>";
            }
            else 
            {
                // Kiểm tra trùng
                $check = "SELECT MaTG FROM tacgia WHERE MaTG = '$matg'";
                $result = mysqli_query($conn, $check);
                
                if(mysqli_num_rows($result) > 0) {
                    $msg = "<p style='color:red;' align='center'>❌ Mã tác giả '$matg' đã tồn tại.</p>";
                }
                else 
                {
                    $insert = "INSERT INTO tacgia(MaTG, TenTG, MoTaTG) VALUES ('$matg','$tentg','$mota')";
                    if(mysqli_query($conn, $insert)) {
                        $msg = "<p style='color:green;' align='center'>✅ Thêm tác giả thành công.</p>";
                        // Xóa dữ liệu trong POST để form trống lại (nếu muốn nhập tiếp)
                        $_POST = array(); 
                    } else {
                        $msg = "<p style='color:red;' align='center'>Lỗi SQL: " . mysqli_error($conn) . "</p>";
                    }
                }
            }
        }
    ?>

    <form method="post" action="">
        <table align="center">
            <tr>
                <th colspan="2">THÊM TÁC GIẢ MỚI</th>
            </tr>
            
            <?php if(!empty($msg)) echo "<tr><td colspan='2'>$msg</td></tr>"; ?>

            <tr>
                <td><label>Mã tác giả:</label></td>
                <td>
                    <input type="text" name="matg" value="<?php if(isset($_POST['matg'])) echo $_POST['matg']; ?>">
                </td>
            </tr>
            <tr>
                <td><label>Tên tác giả:</label></td>
                <td>
                    <input type="text" name="tentg" value="<?php if(isset($_POST['tentg'])) echo $_POST['tentg']; ?>">
                </td>
            </tr>
            <tr>
                <td><label>Mô tả tác giả:</label></td>
                <td>
                    <textarea name="mota" rows="4" cols="50"><?php if(isset($_POST['mota'])) echo $_POST['mota']; ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="them" value="Thêm mới" style="padding:5px 20px; font-weight:bold; cursor:pointer;">
                </td>
            </tr>
        </table>
    </form>
    
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
        &laquo; Quay Về Trang Quản Trị
    </a>
    </div>
</body>
</html>