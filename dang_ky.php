<?php
$page_title = 'Đăng ký';
include ('includes/db_connect.php');
include ('includes/header.php'); 

// Khởi tạo biến thông báo
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $errors = array(); 
    
    // Sửa: Check 'hoUser'
    if (empty($_POST['hoUser'])) {
        $errors[] = 'Bạn quên nhập Họ.';
    } else {
        $ho = mysqli_real_escape_string($conn, trim($_POST['hoUser']));
    }
    
    // Sửa: Check 'tenUser'
    if (empty($_POST['tenUser'])) {
        $errors[] = 'Bạn quên nhập Tên.';
    } else {
        $ten = mysqli_real_escape_string($conn, trim($_POST['tenUser']));
    }
    
    // Check email (có check trùng)
    if (empty($_POST['email'])) {
        $errors[] = 'Bạn quên nhập Email.';
    } elseif (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        // [MỚI] Dòng này sẽ kiểm tra cấu trúc email (phải có @, dấu chấm, tên miền hợp lệ)
        $errors[] = 'Email không đúng định dạng (Ví dụ: tenban@gmail.com).';
    } else {
        $e = mysqli_real_escape_string($conn, trim($_POST['email']));
        // Sửa: Check trùng trong bảng 'users' (Giả sử bảng là 'users' (số nhiều))
        $q_check = "SELECT userID FROM users WHERE email='$e'"; 
        $r_check = @mysqli_query($conn, $q_check);
        if (mysqli_num_rows($r_check) != 0) {
            $errors[] = 'Địa chỉ email này đã được đăng ký.';
        }
    }
    
    // Check password
    if (!empty($_POST['pass1'])) {
        if ($_POST['pass1'] != $_POST['pass2']) {
            $errors[] = 'Mật khẩu không trùng khớp.';
        } else {
            if (strlen(trim($_POST['pass1'])) < 6) { 
                $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            } else {
                $p = mysqli_real_escape_string($conn, trim($_POST['pass1']));
            }
        }
    } else {
        $errors[] = 'Bạn quên nhập Mật khẩu.';
    }
    
    if (empty($errors)) { 
    
        // Mã hóa mật khẩu (Cách an toàn)
        $hashed_password = password_hash($p, PASSWORD_DEFAULT);
        
        // Chèn dữ liệu
        $q = "INSERT INTO users (hoUser, tenUser, email, password, ngayTao, roleID) 
              VALUES ('$ho', '$ten', '$e', '$hashed_password', NOW(), 2)";
              
        $r = @mysqli_query ($conn, $q); 
        
        if ($r) { 
            // THÔNG BÁO THÀNH CÔNG ĐƠN GIẢN
            $msg = "<p style='color:green; font-weight:bold; text-align:center;'>✅ Đăng ký thành công. Bạn có thể đăng nhập ngay bây giờ.</p>";
            // Xóa dữ liệu POST để làm trống form
            $_POST = array(); 
            
        } else { 
            // LỖI HỆ THỐNG
            $msg = "<p style='color:red; font-weight:bold; text-align:center;'>❌ Lỗi hệ thống: Bạn không thể đăng ký do lỗi hệ thống. Vui lòng thử lại.</p>";
        }
        
    } else { 
        // Báo lỗi Input
        $msg = '<p style="color:red; font-weight:bold; text-align:center;">❌ Lỗi! Các lỗi sau đã xảy ra:<br />';
        foreach ($errors as $error_msg) {
            $msg .= " - $error_msg<br />\n";
        }
        $msg .= 'Vui lòng thử lại.</p>';
    }
}
?>



<div class="login-form-container"> <h1>Đăng ký</h1>
    <form action="dang_ky.php" method="post">
        <?php if(!empty($msg)) echo $msg; ?>
        
        <div class="form-group">
            <label for="hoUser">Họ:</label>
            <input type="text" id="hoUser" name="hoUser" maxlength="50" value="<?php if (isset($_POST['hoUser'])) echo htmlspecialchars($_POST['hoUser']); ?>" />
        </div>

        <div class="form-group">
            <label for="tenUser">Tên:</label>
            <input type="text" id="tenUser" name="tenUser" maxlength="30" value="<?php if (isset($_POST['tenUser'])) echo htmlspecialchars($_POST['tenUser']); ?>" />
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" maxlength="100" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" />
        </div>
        
        <div class="form-group">
            <label for="pass1">Mật khẩu:</label>
            <input type="password" id="pass1" name="pass1" maxlength="20" />
        </div>

        <div class="form-group">
            <label for="pass2">Xác nhận mật khẩu:</label>
            <input type="password" id="pass2" name="pass2" maxlength="20" />
        </div>
        
        <div class="form-group">
            <input type="submit" name="submit" value="Đăng ký" />
        </div>
        
    </form>
</div>

<?php include ('includes/footer.html'); ?>