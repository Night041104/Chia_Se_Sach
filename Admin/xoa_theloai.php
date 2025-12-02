<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Thông Tin Thể Loại</title>
<style>
   th { background:#ae1c55; color:#fff; font-size:16px; }
table { background:#fddedc; width:600px; }
input, textarea { background:#fff; padding:5px; border:1px solid #bbb; width:300px; }

</style>
</head>
<body>
<?php
    // Lấy mã thể loại
    $matheloai = $_REQUEST['matheloai'];

    // Lấy dữ liệu thể loại
    $sql = "SELECT * FROM theloai WHERE MaTheLoai = '$matheloai'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) <> 0) {
        $row = mysqli_fetch_array($result);
        $tenTL = $row['TenTheLoai'];
    } else {
        die('Không tìm thấy thể loại trong CSDL!');
    }

    // Xử lý khi người dùng nhấn nút xóa
    if(isset($_POST['sub']))
    {
        // 1. Kiểm tra khóa ngoại (trong bảng sach_theloai)
        $sql_check = "select * from sach_theloai where MaTheLoai = '$matheloai'";
        $result_check = mysqli_query($conn,$sql_check);
        
        if(mysqli_num_rows($result_check) <> 0)
        {
            echo "<p style='color:red;' align='center'>Không thể xóa thể loại này vì đang có sách sử dụng.</p>";
        }else
        {
            // 2. Nếu không, tiến hành xóa
            $sql_delete = "DELETE from theloai where MaTheLoai = '$matheloai'";
            $result_delete = mysqli_query($conn,$sql_delete);
            if($result_delete)
            {
                header("Location: admin_index_theloai.php");
                exit();
            }else
            {
                echo "<p style='color:red;' align='center'>Lỗi khi xóa!</p>";
            }
        }
        
    }
    mysqli_close($conn);
?>

<form action="xoa_theloai.php" method="POST">
    <table align="center" border="0" cellpadding="5">
        <tr><th colspan="2"><h2><b>XÓA THÔNG TIN THỂ LOẠI</b></h2></th></tr>

        <tr>
            <td><label>Mã Thể Loại:</label></td>
            <td><input type="text" name="matheloai" value="<?php echo $matheloai; ?>" style="width:250px;" readonly></td>
        </tr>

        <tr>
            <td><label>Tên Thể Loại:</label></td>
            <td><input type="text" name="tentl" value="<?php echo $tenTL; ?>" style="width:250px;" disabled></td>
        </tr>

        <tr><th colspan="2" align="center"><input type="submit" name="sub" value="Xóa"></th></tr>
    </table>

    <div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="admin_index_theloai.php" 
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
</form>
</body>
</html>