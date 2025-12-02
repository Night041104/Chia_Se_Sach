<?php # Script quen_mat_khau.php
$page_title = 'Quên mật khẩu';
include ('includes/header.php');
include ('includes/db_connect.php');

$errors = array();
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    // 1. Kiểm tra Email
    if (empty($_POST['email'])) {
        $errors[] = 'Vui lòng nhập Email.';
    } else {
        $e = mysqli_real_escape_string($conn, trim($_POST['email']));
    }

    // 2. Kiểm tra mật khẩu mới
    if (!empty($_POST['pass1'])) {
        if ($_POST['pass1'] != $_POST['pass2']) {
            $errors[] = 'Mật khẩu xác nhận không trùng khớp.';
        } else {
            $np = mysqli_real_escape_string($conn, trim($_POST['pass1']));
        }
    } else {
        $errors[] = 'Vui lòng nhập mật khẩu mới.';
    }

    if (empty($errors)) {
        // Kiểm tra xem Email có tồn tại không
        $q_check = "SELECT userID FROM users WHERE email='$e'";
        $r_check = @mysqli_query($conn, $q_check);

        if (mysqli_num_rows($r_check) == 1) {
            // Email tồn tại -> Cập nhật mật khẩu mới
            // Dùng password_hash để khớp với file dang_nhap.php
            $hashed_pass = password_hash($np, PASSWORD_DEFAULT); 
            
            $q_update = "UPDATE users SET password='$hashed_pass' WHERE email='$e'";
            $r_update = @mysqli_query($conn, $q_update);

            if (mysqli_affected_rows($conn) == 1) {
                $success = true;
            } else {
                $errors[] = 'Lỗi hệ thống: Không thể đổi mật khẩu (có thể mật khẩu mới giống mật khẩu cũ).';
            }
        } else {
            $errors[] = 'Địa chỉ Email này không tồn tại trong hệ thống.';
        }
    }
    mysqli_close($conn);
}
?>

<div class="login-form-container">
    <h1>Quên mật khẩu</h1>
    
    <?php
    // Hiển thị thông báo thành công
    if ($success) {
        echo '<div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                Đã đổi mật khẩu thành công! <a href="dang_nhap.php" style="font-weight:bold;">Đăng nhập ngay</a>.
              </div>';
    }
    
    // Hiển thị lỗi
    if (!empty($errors)) {
        echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">';
        foreach ($errors as $msg) {
            echo " - $msg<br />\n";
        }
        echo '</div>';
    }
    ?>

    <form action="quen_mat_khau.php" method="post">
        
        <div class="form-group">
            <label for="email">Nhập Email đã đăng ký:</label>
            <input type="text" id="email" name="email" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" />
        </div>
        
        <div class="form-group">
            <label for="pass1">Mật khẩu mới:</label>
            <input type="password" id="pass1" name="pass1" maxlength="20" />
        </div>

        <div class="form-group">
            <label for="pass2">Nhập lại mật khẩu mới:</label>
            <input type="password" id="pass2" name="pass2" maxlength="20" />
        </div>
        
        <div class="form-group">
            <input type="submit" name="submit" value="Đổi mật khẩu" />
        </div>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="dang_nhap.php" style="color: #337ab7; text-decoration: none;">Quay lại Đăng nhập</a>
        </div>

    </form>
</div>

<?php include ('includes/footer.html'); ?>