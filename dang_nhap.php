<?php # Script dang_nhap.php

// BƯỚC 1: Bắt đầu session VÀ xử lý logic chuyển hướng
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1.1: Nếu người dùng đã đăng nhập, chuyển về trang chủ
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$errors = array(); 
$masach = isset($_GET['masach']) ? trim($_GET['masach']) : '';
$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$from = isset($_GET['from']) ? $_GET['from'] : '';

// 1.2: Xử lý POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $conn = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL");
    mysqli_set_charset($conn, 'UTF8');

    if (empty($_POST['email'])) {
        $errors[] = 'Bạn quên nhập Email.';
    } else {
        $e = mysqli_real_escape_string($conn, trim($_POST['email']));
    }
    
    if (empty($_POST['pass'])) {
        $errors[] = 'Bạn quên nhập Mật khẩu.';
    } else {
        $p = mysqli_real_escape_string($conn, trim($_POST['pass']));
    }
    
    if (empty($errors)) {
        // Lấy mật khẩu hash VÀ tên từ CSDL
        $q = "SELECT userID, hoUser, tenUser, roleID, avatar, password FROM users WHERE email='$e'";
        $r = @mysqli_query($conn, $q);
        
        if (mysqli_num_rows($r) == 1) {
            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
            $hash_from_db = $row['password']; 
            
            if (password_verify($p, $hash_from_db)) {
                // ĐĂNG NHẬP THÀNH CÔNG
                $_SESSION['username'] = $row['hoUser'] . ' ' . $row['tenUser']; 
                $_SESSION['email'] = $e; 
                $_SESSION['user_id'] = $row['userID'];
                $_SESSION['role_id'] = $row['roleID'];
                $_SESSION['avt'] = $row['avatar'];
                
                if(isset($_GET['from']) && !empty($_GET['from'])){
                    $ma = (!empty($id)) ? "?id=".$id : ((!empty($masach)) ? "?masach=".$masach : "");
                    header("Location: ".$from.$ma);
                }
                else{
                    header("Location: index.php");
                }
                exit(); 
            } else {
                $errors[] = 'Email hoặc mật khẩu không đúng.';
            }
        } else {
            $errors[] = 'Email hoặc mật khẩu không đúng.';
        }
    }
    mysqli_close($conn);
} 

// BƯỚC 2: BẮT ĐẦU HIỂN THỊ HTML
$page_title = 'Đăng nhập';
include ('includes/header.php');

// In lỗi (nếu có)
if (!empty($errors)) {
    echo '<div style="width: 450px; margin: 20px auto; color: red;">';
    echo '<h3>Lỗi!</h3>';
    foreach ($errors as $msg) {
        echo " - $msg<br />\n";
    }
    echo '</div>';
}
?>

<div class="login-form-container">
    <h1>Đăng nhập</h1>
    
    <!-- Giữ nguyên các tham số trên URL khi submit form (như masach, id, from) -->
    <form method="post" action="">
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" maxlength="100" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" />
        </div>
        
        <div class="form-group">
            <label for="pass">Mật khẩu:</label>
            <input type="password" id="pass" name="pass" maxlength="20" />
        </div>
        
        <div class="form-group">
            <input type="submit" name="submit" value="Đăng nhập" />
        </div>
        
        <!-- [MỚI] THÊM LINK QUÊN MẬT KHẨU -->
        <div style="text-align: right; margin-top: 10px;">
            <a href="quen_mat_khau.php" style="color: #666; font-size: 0.9em; text-decoration: none;">Quên mật khẩu?</a>
        </div>
        
    </form>
</div>

<?php include ('includes/footer.html'); ?>