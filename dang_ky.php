<?php
$page_title = 'Đăng ký';
include ('includes/header.php'); 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $dbc = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL");
    mysqli_set_charset($dbc, 'UTF8');
    
    $errors = array(); 
    
    // Sửa: Check 'hoUser'
    if (empty($_POST['hoUser'])) {
        $errors[] = 'Bạn quên nhập Họ.';
    } else {
        $ho = mysqli_real_escape_string($dbc, trim($_POST['hoUser']));
    }
    
    // Sửa: Check 'tenUser'
    if (empty($_POST['tenUser'])) {
        $errors[] = 'Bạn quên nhập Tên.';
    } else {
        $ten = mysqli_real_escape_string($dbc, trim($_POST['tenUser']));
    }
    
    // Check email (có check trùng)
    if (empty($_POST['email'])) {
         $errors[] = 'Bạn quên nhập Email.';
    } elseif (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
         // [MỚI] Dòng này sẽ kiểm tra cấu trúc email (phải có @, dấu chấm, tên miền hợp lệ)
         $errors[] = 'Email không đúng định dạng (Ví dụ: tenban@gmail.com).';
    } else {
        $e = mysqli_real_escape_string($dbc, trim($_POST['email']));
        // Sửa: Check trùng trong bảng 'users' (Giả sử bảng là 'users' (số nhiều))
        $q_check = "SELECT userID FROM users WHERE email='$e'"; 
        $r_check = @mysqli_query($dbc, $q_check);
        if (mysqli_num_rows($r_check) != 0) {
            $errors[] = 'Địa chỉ email này đã được đăng ký.';
        }
    }
    
    // Check password
    if (!empty($_POST['pass1'])) {
        if ($_POST['pass1'] != $_POST['pass2']) {
            $errors[] = 'Mật khẩu không trùng khớp.';
        } else {
            $p = mysqli_real_escape_string($dbc, trim($_POST['pass1']));
        }
    } else {
        $errors[] = 'Bạn quên nhập Mật khẩu.';
    }
    
    if (empty($errors)) { 
    
        // Mã hóa mật khẩu (Cách an toàn)
        $hashed_password = password_hash($p, PASSWORD_DEFAULT);
        
        // Sửa: Dùng tên cột từ CSDL của bạn
        $q = "INSERT INTO users (hoUser, tenUser, email, password, ngayTao, roleID) 
              VALUES ('$ho', '$ten', '$e', '$hashed_password', NOW(), 2)";
              
        $r = @mysqli_query ($dbc, $q); 
        
        if ($r) { 
            echo '<h1>Cảm ơn bạn!</h1>
            <p>Bạn đã đăng ký thành công. Bây giờ bạn có thể đăng nhập.</p><p><br /></p>'; 
        } else { 
            echo '<h1>Lỗi hệ thống</h1>
            <p class="error">Bạn không thể đăng ký do lỗi hệ thống.</p>'; 
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
        }
        
        mysqli_close($dbc); 
        include ('includes/footer.html'); 
        exit();
        
    } else { // Báo lỗi
        echo '<h1>Lỗi!</h1>
        <p class="error">Các lỗi sau đã xảy ra:<br />';
        foreach ($errors as $msg) {
            echo " - $msg<br />\n";
        }
        echo '</p><p>Vui lòng thử lại.</p><p><br /></p>';
    }
    mysqli_close($dbc);
}
?>

<div class="login-form-container"> <h1>Đăng ký</h1>
    <form action="dang_ky.php" method="post">
        
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