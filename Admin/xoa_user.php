<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!-- conmemaydungoccho -->
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xoá tài khoản User</title>
<style>
    /* Style y hệt file mẫu */
th { background: #ae1c55; color: #fff; font-size:16px; }
table { background: #fddedc; width:600px; }
input, textarea { background: #fff; padding:5px; border:1px solid #bbb; width:300px; }

</style>
 </head>
 <body>
<?php
    // lấy mã user
    $mauser = $_REQUEST['userID'];  
    //lấy dữ liệy user
    $sql = "SELECT * FROM users WHERE userID = '$mauser'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) <> 0)
    {
        $row = mysqli_fetch_array($result);
        $hoUser = $row['hoUser'];
        $tenUser = $row['tenUser'];
        $email = $row['email'];
        $quyen = $row['roleID'];
        $hinh_path = "Hinh_user/" . $row['avatar'];
    }else
    {
        die('Không tìm thấy user trong CSDL');
    }
    // Xử lí khi người dùng nhấn nút xoá
    if(isset($_POST['sub']))
    {
         // 1. XÓA ẢNH USEER
        if (file_exists($hinh_path)) {
            unlink($hinh_path);
        }
        $sql_delete = "DELETE from users where userID = '$mauser'";
        $result_delete = mysqli_query($conn,$sql_delete);
        if($result_delete)
        {
            echo "<p style='color:green;' align='center'>Đã xóa user thành công.</p>";
            // Xóa dữ liệu trên form
            $mauser = $hoUser = $tenUser = $email = $quyen = $hinh_path = "";
        }else
        {
            echo "<p style='color:red;' align='center'>Lỗi khi xóa!</p>";
        }
        header("Location: admin_index_user.php");
        exit();
        
    }
    mysqli_close($conn);

?>
<form action="xoa_user.php" method="POST">
    <table align="center" border="0" cellpadding="5">
        <tr><th colspan="2"><h2><b>XÓA THÔNG TIN USER</b></h2></th></tr>

        <tr>
            <td><label>UserID:</label></td>
            <td><input type="text" name="userID" value="<?php echo $mauser; ?>" style="width:250px;" readonly></td>
        </tr>

        <tr>
            <td><label>Họ User:</label></td>
            <td><input type="text" name="hoUser" value="<?php echo $hoUser; ?>" style="width:250px;" disabled></td>
        </tr>
        <tr>
            <td><label>Tên User:</label></td>
            <td><input type="text" name="tenUser" value="<?php echo $tenUser; ?>" style="width:250px;" disabled></td>
        </tr>
        <tr>
            <td><label>Email:</label></td>
            <td><input type="text" name="email" value="<?php echo $email  ?>" style="width:250px;" disabled></td>
        </tr>
        <tr>
            <td><label>Quyền:</label></td>
            <td><input type="text" name="roleID" value="<?php 
            if ($quyen === "") 
            {
                echo "";
            }elseif ($quyen == 1) 
            {
                echo "Admin";
            } else 
            {
                echo "User";
            }

            ?>" style="width:250px;" disabled></td>
        </tr>
        <tr>
            <td><label>Avatar:</label></td>
            <td><img src="<?php echo $hinh_path ?>" width="50px" alt="Lỗi ảnhhhh"></td>
        </tr>

        <tr><th colspan="2" align="center"><input type="submit" name="sub" value="Xóa"></th></tr>
    </table>

    <div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="admin_index_user.php" 
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