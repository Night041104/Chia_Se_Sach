<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật user</title>
</head>
<style>
        /* Style giữ nguyên như bạn yêu cầu */
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
    input[type="text"], input[type="password"] { /* Thêm password vào đây */
        width: 300px; 
        padding: 5px;
    }
    select {
        
        padding: 5px;
    }
</style>
<body>
    <?php
        $msg='';
        if(isset($_POST['them']))
        {
            // 1. Lấy dữ liệu từ form (Sửa $_GET thành $_POST)
            $hoUser = mysqli_real_escape_string($conn, $_POST['hoUser']);
            $tenUser = mysqli_real_escape_string($conn, $_POST['tenUser']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = $_POST['password'];
            $role = $_POST['role']; // Admin hoặc User

            // Chuyển đổi role thành số (Giả định: 1=Admin, 2=User)
            $roleID = ($role == 'Admin') ? 1 : 2;
            
            // Lấy tên file ảnh
            $avatar_name = $_FILES['avatar']['name'];

            // 2. Validation (Kiểm tra rỗng)
            if(empty($hoUser) || empty($tenUser) || empty($email) || empty($password)) {
                $msg = "<p style='color:red;' align='center'>⚠️ Vui lòng nhập đầy đủ thông tin (Họ, Tên, Email, Mật khẩu)!</p>";
            } else {
                
                // 3. Kiểm tra trùng Email
                $check = mysqli_query($conn, "SELECT userID FROM users WHERE email='$email'");
                if(mysqli_num_rows($check) > 0) {
                    $msg = "<p style='color:red;' align='center'>❌ Email này đã tồn tại.</p>";
                } else {
                    
                    // 4. Xử lý Upload Avatar (Nếu có chọn)
                    $new_avatar_name = 'default_avatar.png'; // Mặc định
                    
                    if(!empty($avatar_name)) {
                        $file_tmp = $_FILES['avatar']['tmp_name'];
                        $file_ext = strtolower(pathinfo($avatar_name, PATHINFO_EXTENSION));
                        $allowed = array('jpg', 'jpeg', 'png', 'gif');
                        
                        if(in_array($file_ext, $allowed)) {
                            // Đổi tên file để tránh trùng: email_time.jpg
                            $new_avatar_name = time() . "_" . $email;
                            $upload_path = "Hinh_user/" . $new_avatar_name;
                            
                            if(!move_uploaded_file($file_tmp, $upload_path)) {
                                $msg = "<p style='color:red;' align='center'>❌ Không thể upload ảnh.</p>";
                                $new_avatar_name = 'default_avatar.png'; // Quay về mặc định nếu lỗi
                            }
                        } else {
                            $msg = "<p style='color:red;' align='center'>❌ Chỉ chấp nhận file ảnh (jpg, png, gif).</p>";
                        }
                    }

                    // 5. Mã hóa mật khẩu
                    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

                    // 6. Thêm vào CSDL (Nếu không có lỗi upload nghiêm trọng)
                    if(strpos($msg, '❌') === false) {
                        $sql = "INSERT INTO users (hoUser, tenUser, email, password, roleID, avatar, ngayTao) 
                                VALUES ('$hoUser', '$tenUser', '$email', '$pass_hash', '$roleID', '$new_avatar_name', NOW())";
                        
                        if(mysqli_query($conn, $sql)) {
                            $msg = "<p style='color:green;' align='center'>✅ Thêm thành viên thành công.</p>";
                            // Xóa dữ liệu POST để reset form
                            $_POST = array();
                        } else {
                            $msg = "<p style='color:red;' align='center'>Lỗi SQL: ".mysqli_error($conn)."</p>";
                        }
                    }
                }
            }
        }
    ?>
    <form method="post" name="suaUser" action="" enctype="multipart/form-data">
        <table align="center" >
            <tr>
                <th colspan="2">CẬP NHẬT THÔNG TIN USER</th>
            </tr>
            <?php if($msg) echo "<tr><td colspan='2'>$msg</td></tr>";?>
            <tr>
                <td><label>Họ user: </label></td>
                <td><input type="text" name="hoUser" value="<?php if(isset($_POST['hoUser'])) echo $_POST['hoUser'];?>"></td>
            </tr>
            <tr>
                <td><label>Tên user: </label></td>
                <td><input type="text" name="tenUser" value="<?php if(isset($_POST['tenUser'])) echo $_POST['tenUser'];?>"></td>
            </tr>
            <tr>
                <td><label>Email: </label></td>
                <td><input type="text" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>"></td>
            </tr>
            <tr>
                <td><label>Mật khẩu: </label></td>
                <td><input readonly type="password" name="password" style="width: 300px; padding: 5px;"></td>
            </tr>
            <tr>
                <td><label>Quyền hạn: </label></td>
                <td>
                    <select name="role">
                        <option value="User" <?php if(isset($_POST['role']) && $_POST['role']=='User') echo 'selected'; ?>>User</option>
                        <option value="Admin" <?php if(isset($_POST['role']) && $_POST['role']=='Admin') echo 'selected'; ?>>Admin</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Ảnh đại diện:</label></td>
                <td><input type="file" name="avatar"></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="them" value="Thêm mới" style="padding:5px 20px; font-weight:bold; cursor:pointer;">
                </td>
            </tr>
        </table>
    </form>
    <div style="text-align:center; margin-top:20px;">
        <a href="admin_index_user.php" style="text-decoration:none; font-weight:bold; color:#555;">&laquo; Quay Về Danh Sách</a>
    </div>
</body>
</html>