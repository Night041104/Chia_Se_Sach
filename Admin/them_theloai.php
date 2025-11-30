<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm thể loại</title>
</head>
<style>
    /* CSS giữ nguyên từ file mẫu */
    th{
        font-size: 30px;
        background-color: #ae1c55;
        color: white;
    }
    table {
        background-color: #fddedc;
    }
    td{
        padding: 10px; /* Tăng khoảng cách cho dễ nhìn */
    }
    input[type="text"] {
        width: 300px; 
        padding: 5px;
    }
</style>
<body>
    
    <?php
        $msg = ""; 
        if(isset($_POST["them"]))
        {
            $matl = mysqli_real_escape_string($conn, $_POST["matl"]);
            $tentl = mysqli_real_escape_string($conn, $_POST["tentl"]);

            if(empty($matl) || empty($tentl)) {
                $msg = "<p style='color:red;' align='center'>⚠️ Vui lòng nhập đầy đủ thông tin!</p>";
            }
            else 
            {
                // Kiểm tra trùng
                $check = "SELECT MaTheLoai FROM theloai WHERE MaTheLoai = '$matl'";
                $result = mysqli_query($conn, $check);
                
                if(mysqli_num_rows($result) > 0) {
                    $msg = "<p style='color:red;' align='center'>❌ Mã thể loại '$matl' đã tồn tại.</p>";
                }
                else 
                {
                    $insert = "INSERT INTO theloai(MaTheLoai, TenTheLoai) VALUES ('$matl','$tentl')";
                    if(mysqli_query($conn, $insert)) {
                        $msg = "<p style='color:green;' align='center'>✅ Thêm thể loại thành công.</p>";
                        $_POST = array(); 
                    } else {
                        $msg = "<p style='color:red;' align='center'>Lỗi SQL: " . mysqli_error($conn) . "</p>";
                    }
                }
            }
        }
    ?>

    <form method="post" action="">
        <table align="center" width="600" border="0"> <tr>
                <th colspan="2">THÊM THỂ LOẠI MỚI</th>
            </tr>
            
            <?php if(!empty($msg)) echo "<tr><td colspan='2'>$msg</td></tr>"; ?>

            <tr>
                <td width="30%" align="right">
                    <label style="font-weight:bold;">Mã thể loại:</label>
                </td>
                <td width="70%">
                    <input type="text" name="matl" value="<?php if(isset($_POST['matl'])) echo $_POST['matl']; ?>">
                </td>
            </tr>
            <tr>
                <td align="right">
                    <label style="font-weight:bold;">Tên thể loại:</label>
                </td>
                <td>
                    <input type="text" name="tentl" value="<?php if(isset($_POST['tentl'])) echo $_POST['tentl']; ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="them" value="Thêm mới" style="padding:5px 20px; font-weight:bold; cursor:pointer;">
                </td>
            </tr>
        </table>
    </form>
    
    <div style="text-align:center; margin-top:20px;">
        <a href="admin_index_theloai.php" style="text-decoration:none; font-weight:bold; color:#555;">&laquo; Quay Về Danh Sách</a>
    </div>
</body>
</html>