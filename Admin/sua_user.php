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
    /* Style giữ nguyên như cũ */
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
    input[type="text"], input[type="password"] { 
        width: 300px; 
        padding: 5px;
    }
    select {
        padding: 5px;
    }
</style>
<body>
    <?php
        // 1. LẤY ID USER CẦN SỬA
        if(isset($_GET['userID'])) $uid = $_GET['userID']; 
        elseif(isset($_POST['uid'])) $uid = $_POST['uid'];
        else { echo "Không tìm thấy ID người dùng"; exit(); }

        // 2. LẤY THÔNG TIN CŨ
        $sql_info = "SELECT * FROM users WHERE userID = '$uid'";
        $res_info = mysqli_query($conn, $sql_info);
        if(mysqli_num_rows($res_info) > 0) {
            $row = mysqli_fetch_array($res_info);
            // Gán biến để hiển thị
            $hoUser = $row['hoUser'];
            $tenUser = $row['tenUser'];
            $email = $row['email'];
            $password_hash = $row['password']; // Lấy chuỗi hash
            $roleID = $row['roleID'];
            $old_avatar = $row['avatar'];
        } else {
            die('User không tồn tại.');
        }

        $msg='';

        // 3. XỬ LÝ KHI BẤM NÚT CẬP NHẬT
        if(isset($_POST['capnhat']))
        {
            // Lấy dữ liệu từ form
            $hoUser_new = mysqli_real_escape_string($conn, $_POST['hoUser']);
            $tenUser_new = mysqli_real_escape_string($conn, $_POST['tenUser']);
            $role = $_POST['role']; 
            $roleID_new = ($role == 'Admin') ? 1 : 2;
            
            $avatar_name = $_FILES['avatar']['name'];

            // Validation
            if(empty($hoUser_new) || empty($tenUser_new)) {
                $msg = "<p style='color:red;' align='center'>⚠️ Họ và Tên không được để trống!</p>";
            } else {
                
                // Xử lý Upload Avatar Mới (nếu có)
                $avatar_update = $old_avatar; 
                $upload_ok = true;

                if(!empty($avatar_name)) {
                    $file_tmp = $_FILES['avatar']['tmp_name'];
                    $file_ext = strtolower(pathinfo($avatar_name, PATHINFO_EXTENSION));
                    $allowed = array('jpg', 'jpeg', 'png', 'gif');
                    
                    if(in_array($file_ext, $allowed)) {
                        $new_avatar_name = time() . "_" . $avatar_name;
                        $upload_path = "Hinh_user/" . $new_avatar_name;
                        
                        if(move_uploaded_file($file_tmp, $upload_path)) {
                            $avatar_update = $new_avatar_name;
                            if($old_avatar != 'default_avatar.png' && file_exists("Hinh_user/".$old_avatar)){
                                unlink("Hinh_user/".$old_avatar);
                            }
                        } else {
                            $msg = "<p style='color:red;' align='center'>❌ Lỗi upload ảnh.</p>";
                            $upload_ok = false;
                        }
                    } else {
                        $msg = "<p style='color:red;' align='center'>❌ Chỉ chấp nhận file ảnh.</p>";
                        $upload_ok = false;
                    }
                }

                if($upload_ok) {
                    // [ĐÃ SỬA] Chỉ cập nhật thông tin, KHÔNG đụng vào mật khẩu
                    $sql = "UPDATE users SET 
                            hoUser = '$hoUser_new', 
                            tenUser = '$tenUser_new', 
                            roleID = '$roleID_new', 
                            avatar = '$avatar_update' 
                            WHERE userID = '$uid'";

                    if(mysqli_query($conn, $sql)) {
                        $msg = "<p style='color:green;' align='center'>✅ Cập nhật thành công!</p>";
                        // Cập nhật lại biến hiển thị
                        $hoUser = $hoUser_new;
                        $tenUser = $tenUser_new;
                        $roleID = $roleID_new;
                        $old_avatar = $avatar_update;
                    } else {
                        $msg = "<p style='color:red;' align='center'>Lỗi SQL: ".mysqli_error($conn)."</p>";
                    }
                }
            }
        }
    ?>

    <form method="post" name="suaUser" action="" enctype="multipart/form-data">
        <input type="hidden" name="uid" value="<?php echo $uid; ?>">

        <table align="center" >
            <tr>
                <th colspan="2">CẬP NHẬT THÔNG TIN USER</th>
            </tr>
            <?php if($msg) echo "<tr><td colspan='2'>$msg</td></tr>";?>
            
            <tr>
                <td><label>Email (Tài khoản): </label></td>
                <td>
                    <input type="text" value="<?php echo $email; ?>" disabled style="background-color:#eee;">
                </td>
            </tr>

            <tr>
                <td><label>Họ user: </label></td>
                <td>
                    <input type="text" name="hoUser" 
                           value="<?php echo isset($_POST['hoUser']) ? $_POST['hoUser'] : $hoUser; ?>">
                </td>
            </tr>
            <tr>
                <td><label>Tên user: </label></td>
                <td>
                    <input type="text" name="tenUser" 
                           value="<?php echo isset($_POST['tenUser']) ? $_POST['tenUser'] : $tenUser; ?>">
                </td>
            </tr>
            
            <tr>
                <td><label>Mật khẩu (Hash): </label></td>
                <td>
                    <input type="text" value="<?php echo $password_hash; ?>" disabled style="background-color:#eee; color:#666; font-size:12px;">
                </td>
            </tr>
            
            <tr>
                <td><label>Quyền hạn: </label></td>
                <td>
                    <select name="role">
                        <option value="User" <?php if($roleID == 2) echo 'selected'; ?>>User</option>
                        <option value="Admin" <?php if($roleID == 1) echo 'selected'; ?>>Admin</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td><label>Ảnh đại diện:</label></td>
                <td>
                    <?php if($old_avatar) echo "<img src='Hinh_user/$old_avatar' width='50' style='vertical-align:middle; margin-right:10px;'>"; ?>
                    <input type="file" name="avatar">
                </td>
            </tr>
            
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="capnhat" value="Cập Nhật" style="padding:5px 20px; font-weight:bold; cursor:pointer;">
                </td>
            </tr>
        </table>
    </form>
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
</body>
</html>