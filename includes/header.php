<?php
// BẮT BUỘC đặt session_start() ở dòng ĐẦU TIÊN

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $page_title; ?></title> 
    	<!-- Sử dụng thêm thư viện font_awesome để lấy 1 số icon như icon trái tim bên mục yêu thích sách -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="includes/style.css" type="text/css" media="screen" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    
    <script type="text/javascript">
        // Hàm để Bật/Tắt dropdown
        function toggleUserDropdown() {
            document.getElementById("userDropdown").classList.toggle("dropdown-show");
        }
        
        // Tắt dropdown nếu nhấp ra ngoài
        window.onclick = function(event) {
            if (!event.target.matches('.user-trigger') && !event.target.matches('.user-trigger *')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('dropdown-show')) {
                        openDropdown.classList.remove('dropdown-show');
                    }
                }
            }
        }
    </script>
</head>
<body>
    <div id="header">
        <div class="container"> 
            
            <a href="index.php" style="text-decoration:none; float: left;">
                <img src="images/logo_test.gif">
                <h1>MyReadingBooks</h1>
            </a>
            
            <form id="header-search" action="tim_kiem_sach.php" method="GET">
                <div class="search-wrapper">
                    <input type="search" name="tensach" placeholder="Tìm kiếm theo tên sách..."/>
                    <button type="submit">&#128269;</button> 
                </div>
            </form>
            
            <div class="header-user-area">
                <?php if (isset($_SESSION['username'])) : ?>
                    <div class="user-dropdown-container" onclick="toggleUserDropdown()">
                        <div class="user-trigger">
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <?php
                                $src= "Admin/Hinh_user/{$_SESSION['avt']}";
                                echo "<img id='avatar' src='$src' alt='Avatar'> ";
                            ?>
                            
                        </div>
                        <div id="userDropdown" class="dropdown-content">
                            <a href="trang_ca_nhan.php">Trang cá nhân</a>
                            <a href="dang_xuat.php">Đăng xuất</a>
                        </div>
                    </div>
                <?php else : ?>
                    <a href="dang_nhap.php" class="btn-header btn-login">Đăng nhập</a>
                    <a href="dang_ky.php" class="btn-header btn-register">Đăng ký</a>
                <?php endif; ?>
            </div>
            
        </div> </div>
    
    <div id="navigation">
        <div class="container">
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                
                <li>
                    <?php
                        $conn1 = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL ".mysqli_connect_error());
                        mysqli_set_charset($conn1, 'UTF8');
                        $sql1 = "select MaTheLoai, TenTheLoai from theloai ORDER BY TenTheLoai ASC";
                        $result1 = mysqli_query($conn1, $sql1);
                    ?>
                    <select name="theloai_menu" 
                            onchange="if (this.value) window.location.href='danh_muc_the_loai.php?matheloai=' + this.value;">
                        
                        <option value="">— Thể loại —</option>
                        
                        <?php
                        if(mysqli_num_rows($result1) > 0) 
                        {
                            while($row1 = mysqli_fetch_array($result1))
                            {
                                echo "<option value='" . $row1['MaTheLoai'] . "'>" . $row1['TenTheLoai'] . "</option>";
                            }
                        }
                        mysqli_close($conn1);
                        ?>
                    </select>
                    
                </li>
                <li><a href="tim_kiem_nang_cao.php">Tim kiếm nâng cao</a></li>
                    <?php
                        if(isset($_SESSION['role_id']) and $_SESSION['role_id']==1)
                        {
                           echo"<li><a href='Admin/index.php'>Trang quản trị</a></li>" ;
                        }
                    ?>
                </ul>
        </div> 
    </div>         
    <div id="content">