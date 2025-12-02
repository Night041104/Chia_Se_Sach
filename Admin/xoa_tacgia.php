<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Thông Tin Tác Giả</title>
    
<style>
    th { background:#ae1c55; color:#fff; font-size:14px; }
table { background:#fddedc; width:600px; }
input, textarea { background:#fff; padding:5px; border:1px solid #bbb; width:300px; }

</style>
</head>
<body>
<?php

    // Lấy mã tác giả
    $matg = $_REQUEST['matg'];

    // Lấy dữ liệu tác giả
    $sql = "SELECT * FROM tacgia WHERE MaTG = '$matg'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) <> 0) {
        $row = mysqli_fetch_array($result);
        $tenTG = $row['TenTG'];
        $moTaTG = $row['MoTaTG'];
    } else {
        die('Không tìm thấy tác giả trong CSDL!');
    }

    // Xử lý khi người dùng nhấn nút xóa
    if(isset($_POST['sub']))
    {
        // 1. Kiểm tra khóa ngoại (trong bảng sach_tacgia)
        $sql_check = "select * from sach_tacgia where MaTG = '$matg'";
        $result_check = mysqli_query($conn,$sql_check);
        
        if(mysqli_num_rows($result_check) <> 0)
        {
            echo "<p style='color:red;' align='center'>Không thể xóa tác giả này vì đang có sách sử dụng.</p>";
        }else
        {
            // 2. Nếu không, tiến hành xóa
            $sql_delete = "DELETE from tacgia where MaTG = '$matg'";
            $result_delete = mysqli_query($conn,$sql_delete);
            if($result_delete)
            {
                header("Location: admin_index_tacgia.php");
                exit();
            }else
            {
                echo "<p style='color:red;' align='center'>Lỗi khi xóa!</p>";
            }
        }
    }
    mysqli_close($conn);
?>

<form action="xoa_tacgia.php" method="POST">
    <table align="center" border="0" cellpadding="5">
        <tr><th colspan="2"><h2><b>XÓA THÔNG TIN TÁC GIẢ</b></h2></th></tr>

        <tr>
            <td><label>Mã Tác Giả:</label></td>
            <td><input type="text" name="matg" value="<?php echo $matg; ?>" style="width:250px;" readonly></td>
        </tr>

        <tr>
            <td><label>Tên Tác Giả:</label></td>
            <td><input type="text" name="tentg" value="<?php echo $tenTG; ?>" style="width:250px;" disabled></td>
        </tr>
        
        <tr>
            <td><label>Mô Tả:</label></td>
            <td><textarea name="mota" rows="5" style="width:250px;" disabled><?php echo $moTaTG; ?></textarea></td>
        </tr>

        <tr><th colspan="2" align="center"><input type="submit" name="sub" value="Xóa"></th></tr>
    </table>

    <div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="admin_index_tacgia.php" 
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