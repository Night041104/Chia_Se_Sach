<?php # Script trang_ca_nhan.php
session_start();

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['username'])) {
    header('Location: dang_nhap.php');
    exit();
}

$page_title = 'Trang cá nhân';
include ('includes/header.php');
include ('includes/db_connect.php');


$user_id = $_SESSION['user_id'];
$user_mail = $_SESSION['email'];
$message = ""; 

// --- XỬ LÝ 1: CẬP NHẬT THÔNG TIN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $ho = trim($_POST['ho']);
    $ten = trim($_POST['ten']);
    if (!empty($ho) && !empty($ten)) {
        $ho = mysqli_real_escape_string($conn, $ho);
        $ten = mysqli_real_escape_string($conn, $ten);
        $sql_update = "UPDATE users SET hoUser='$ho', tenUser='$ten' WHERE userID='$user_id'";
        if (mysqli_query($conn, $sql_update)) {
            $message = "<div class='alert success'>Cập nhật thông tin thành công!</div>";
            $_SESSION['username'] = $ho . ' ' . $ten;
        } else {
            $message = "<div class='alert error'>Lỗi hệ thống: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div class='alert error'>Vui lòng không để trống Họ hoặc Tên.</div>";
    }
}

// --- XỬ LÝ 2: ĐỔI MẬT KHẨU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_pass'])) {
    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_confirm = $_POST['pass_confirm'];
    $q = "SELECT password FROM users WHERE userID='$user_id'";
    $r = mysqli_query($conn, $q);
    $row = mysqli_fetch_array($r);
    
    if (password_verify($pass_old, $row['password'])) {
        if ($pass_new == $pass_confirm) {
            if (strlen($pass_new) >= 6) { 
                $hash_new = password_hash($pass_new, PASSWORD_DEFAULT);
                $q_update = "UPDATE users SET password='$hash_new' WHERE userID='$user_id'";
                if (mysqli_query($conn, $q_update)) {
                    $message = "<div class='alert success'>Đổi mật khẩu thành công!</div>";
                }
            } else {
                $message = "<div class='alert error'>Mật khẩu mới phải từ 6 ký tự trở lên.</div>";
            }
        } else {
            $message = "<div class='alert error'>Mật khẩu xác nhận không trùng khớp.</div>";
        }
    } else {
        $message = "<div class='alert error'>Mật khẩu hiện tại không đúng.</div>";
    }
}

// 3. TRUY VẤN DỮ LIỆU HIỂN THỊ
// A. Thông tin User
$sql_user = "SELECT * FROM users WHERE userID='$user_id'";
$res_user = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_array($res_user);

// B. Lịch sử Đọc
$sql_read = "SELECT s.MaSach, s.TenSach, s.Hinh, c.id as chuong_id, c.TenChuong, lsd.NgayXem 
             FROM lich_su_doc lsd
             JOIN sach s ON lsd.MaSach = s.MaSach
             JOIN chuong c ON lsd.chuong_id = c.id
             WHERE lsd.userID = '$user_id'
             ORDER BY lsd.NgayXem DESC";
$res_read = mysqli_query($conn, $sql_read);

// C. Lịch sử Tải
$sql_down = "SELECT s.MaSach, s.TenSach, s.Hinh, c.id as chuong_id, c.TenChuong, lst.NgayTai 
             FROM lich_su_tai lst
             JOIN sach s ON lst.MaSach = s.MaSach
             JOIN chuong c ON lst.chuong_id = c.id
             WHERE lst.userID = '$user_id'
             ORDER BY lst.NgayTai DESC";
$res_down = mysqli_query($conn, $sql_down);

// D. Danh sách đã thích
$sql_like = "SELECT yts.ngayThich, s.TenSach, s.Hinh, s.MaSach
             FROM yeu_thich_sach yts
             JOIN sach s ON yts.MaSach = s.MaSach
             WHERE yts.userID = '$user_id'
             ORDER BY yts.ngayThich DESC";
$res_like = mysqli_query($conn, $sql_like);

// --- [MỚI] XỬ LÝ 4: ĐỔI ẢNH ĐẠI DIỆN (AVATAR) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_avatar') {
    
    // 1. Kiểm tra có file được gửi lên không
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === 0) {
        
        $file_tmp = $_FILES['avatar_file']['tmp_name'];
        $file_name = $_FILES['avatar_file']['name'];
        $file_size = $_FILES['avatar_file']['size'];
        
        // 2. Kiểm tra đuôi file (Chỉ cho phép ảnh)
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array("jpeg", "jpg", "png", "gif");
        
        if (in_array($file_ext, $allowed_ext)) {
            
            // 3. Kiểm tra dung lượng (Ví dụ giới hạn 5MB)
            if ($file_size < 5000000) {
                
                // 4. Tạo tên file mới: userID + timestamp (để tránh trùng và cache)
                // Ví dụ: 15_1701234567.jpg
                $new_file_name = $user_mail  . '_' . time() . '.' . $file_ext;
                
                // Đường dẫn lưu file (Tính từ thư mục gốc chứa file trang_ca_nhan.php)
                $upload_dir = "Admin/Hinh_user/";
                $upload_path = $upload_dir . $new_file_name;
                
                // 5. Di chuyển file
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    
                    // 6. Cập nhật CSDL
                    // (Lưu ý: Hãy chắc chắn bảng 'users' của bạn đã có cột 'avt' hoặc tên tương tự)
                    // Nếu cột của bạn tên khác (ví dụ: 'Hinh'), hãy sửa lại chỗ `avt` dưới đây
                    $sql_update_avt = "UPDATE users SET avatar = '$new_file_name' WHERE userID = '$user_id'";
                    
                    if (mysqli_query($conn, $sql_update_avt)) {
                                                

                        // [QUAN TRỌNG] Cập nhật lại Session để hiển thị ngay lập tức
                        $_SESSION['avt'] = $new_file_name;
                        
                        $message = "<div class='alert success'>Đổi ảnh đại diện thành công!</div>";
                    } else {
                        $message = "<div class='alert error'>Lỗi CSDL: " . mysqli_error($conn) . "</div>";
                    }
                    
                } else {
                    $message = "<div class='alert error'>Không thể upload file. Kiểm tra quyền thư mục Admin/Hinh_user/</div>";
                }
                
            } else {
                $message = "<div class='alert error'>File quá lớn! Vui lòng chọn ảnh dưới 5MB.</div>";
            }
            
        } else {
            $message = "<div class='alert error'>Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF).</div>";
        }
        
    } else {
        $message = "<div class='alert error'>Vui lòng chọn một file ảnh.</div>";
    }
}
?>
<style>
    /* CSS CŨ GIỮ NGUYÊN */
    .profile-container { display: flex; gap: 20px; margin-top: 20px; }
    .profile-sidebar { flex: 1; max-width: 250px; }
    .profile-card { background: #fff; border: 1px solid #ddd; border-radius: 5px; text-align: center; padding: 20px; margin-bottom: 20px; }
    .profile-avatar { width: 100px; height: 100px; border-radius: 50%; border: 3px solid #ae1c55; margin-bottom: 10px; object-fit: cover;}
    .profile-name { font-weight: bold; font-size: 1.2em; color: #333; margin-bottom: 5px; }
    .profile-role { color: #777; font-size: 0.9em; margin-bottom: 15px; }
    
    .tab-menu { list-style: none; padding: 0; margin: 0; border: 1px solid #ddd; border-radius: 5px; background: #fff; overflow: hidden; }
    .tab-menu li { border-bottom: 1px solid #eee; }
    .tab-menu li:last-child { border-bottom: none; }
    .tab-menu button { width: 100%; text-align: left; padding: 15px; background: none; border: none; cursor: pointer; font-size: 1em; color: #555; transition: 0.3s; }
    .tab-menu button:hover { background: #f9f9f9; color: #ae1c55; }
    .tab-menu button.active { background: #ae1c55; color: white; font-weight: bold; }
    .tab-menu i { margin-right: 10px; width: 20px; text-align: center; }

    .profile-content { flex: 3; background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 25px; min-height: 400px; }
    .tab-content { display: none; animation: fadeIn 0.5s; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    .form-row { margin-bottom: 15px; }
    .form-row label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
    .form-row input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    .btn-save { background: #ae1c55; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    .btn-save:hover { background: #881240; }

    .history-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .history-table th, .history-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    .history-table th { background: #f9f9f9; color: #ae1c55; }
    .history-thumb { width: 50px; height: 70px; object-fit: cover; border-radius: 3px; vertical-align: middle; margin-right: 10px; }
    .history-book-info { display: inline-block; vertical-align: middle; }
    .history-book-name { font-weight: bold; display: block; color: #333; }
    .history-chapter { font-size: 0.9em; color: #666; }
    
    .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
    .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    /* Nút cây bút */
/* --- KHUNG BAO QUANH (QUAN TRỌNG NHẤT) --- */
.avatar-wrapper {
    position: relative; /* Để làm điểm tựa cho nút bút */
    width: 120px;       /* Kích thước khung tròn */
    height: 120px;
    margin: 0 auto 15px; /* Căn giữa và đẩy tên xuống */
}

/* Ảnh avatar sẽ giãn full theo khung */
.avatar-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Cắt ảnh cho tròn đẹp */
    border-radius: 50%;
    border: 3px solid #ae1c55;
    padding: 2px; /* Tạo khoảng hở nhỏ giữa viền và ảnh cho đẹp */
}

/* Nút cây bút */
.edit-avt-btn {
    position: absolute; /* Định vị tuyệt đối theo .avatar-wrapper */
    bottom: 5px;        /* Cách đáy 5px */
    right: 5px;         /* Cách phải 5px */
    
    background-color: #fff;
    color: #333;
    border: 2px solid #ddd;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    
    /* Căn icon ra giữa nút */
    display: flex;
    align-items: center;
    justify-content: center;
    
    cursor: pointer;
    font-size: 14px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: 0.3s;
    z-index: 10; /* Đảm bảo nút nổi lên trên ảnh */
}

.edit-avt-btn:hover {
    background-color: #ae1c55;
    color: white;
    border-color: #ae1c55;
}
</style>

<div class="profile-container container">
    
    <div class="profile-sidebar">
        <div class="profile-card">
            <div class="avatar-wrapper">
            <?php
                $src= "Admin/Hinh_user/{$_SESSION['avt']}";
                // Bỏ class profile-avatar ở đây vì ta sẽ style nó theo wrapper
                echo "<img src='$src' alt='Avatar'>"; 
            ?>
            
            <label for="upload-avt" class="edit-avt-btn" title="Đổi ảnh đại diện">
                <i class="fa fa-pencil-alt"></i>
            </label>
            
            <form action="" method="POST" enctype="multipart/form-data" id="form-avt">
                <input type="file" name="avatar_file" id="upload-avt" style="display: none;" onchange="this.form.submit()">
                <input type="hidden" name="action" value="change_avatar">
            </form>
        </div>
                
            
            <div class="profile-name"><?php echo $user_data['hoUser'] . ' ' . $user_data['tenUser']; ?></div>
            <div class="profile-role">
                <?php
                    if($_SESSION['role_id']!=1)
                        echo"Thành viên";
                    else
                        echo"Quản trị viên";
                ?>
            </div>
        </div>
        
        <ul class="tab-menu">
            <li><button class="tab-link active" onclick="openTab(event, 'thong-tin')" id="btn-thong-tin"><i class="fa fa-user"></i> Thông tin cá nhân</button></li>
            <li><button class="tab-link" onclick="openTab(event, 'lich-su-doc')"><i class="fa fa-book-open"></i> Sách đã đọc</button></li>
            <li><button class="tab-link" onclick="openTab(event, 'lich-su-tai')"><i class="fa fa-download"></i> Sách đã tải</button></li>
            <li><button class="tab-link" onclick="openTab(event, 'danh_sach_yeu_thich')" id="btn-yeu-thich"><i class="fa fa-heart"></i> Sách đã thích</button></li>
        </ul>
    </div>

    <div class="profile-content">
        
        <?php echo $message; ?>

        <div id="thong-tin" class="tab-content active">
            <h2 style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 20px; color:#ae1c55;">Hồ sơ của tôi</h2>
            <form action="" method="POST" style="max-width: 500px;">
                <h3 style="margin-bottom: 15px;">Cập nhật thông tin</h3>
                <div class="form-row">
                    <label>Email (Không thể đổi):</label>
                    <input type="text" value="<?php echo $user_data['email']; ?>" disabled style="background: #eee;">
                </div>
                <div class="form-row" style="display: flex; gap: 10px;">
                    <div style="flex:1;">
                        <label>Họ:</label>
                        <input type="text" name="ho" value="<?php echo $user_data['hoUser']; ?>" required>
                    </div>
                    <div style="flex:1;">
                        <label>Tên:</label>
                        <input type="text" name="ten" value="<?php echo $user_data['tenUser']; ?>" required>
                    </div>
                </div>
                <button type="submit" name="update_info" class="btn-save">Lưu thay đổi</button>
            </form>
            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">
            <form action="" method="POST" style="max-width: 500px;">
                <h3 style="margin-bottom: 15px;">Đổi mật khẩu</h3>
                <div class="form-row"> <label>Mật khẩu hiện tại:</label> <input type="password" name="pass_old" required> </div>
                <div class="form-row"> <label>Mật khẩu mới:</label> <input type="password" name="pass_new" required> </div>
                <div class="form-row"> <label>Xác nhận mật khẩu mới:</label> <input type="password" name="pass_confirm" required> </div>
                <button type="submit" name="change_pass" class="btn-save" style="background: #555;">Đổi mật khẩu</button>
            </form>
        </div>

        <div id="lich-su-doc" class="tab-content">
            <h2 style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 20px; color:#ae1c55;">Sách đã đọc gần đây</h2>
            <?php if(mysqli_num_rows($res_read) > 0): ?>
                <table class="history-table">
                    <thead><tr><th>Sách & Chương</th><th width="150">Thời gian</th><th width="100">Hành động</th></tr></thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_array($res_read)): ?>
                        <tr>
                            <td>
                                <img src="Admin/Hinh_sach/<?php echo $row['Hinh']; ?>" class="history-thumb">
                                <div class="history-book-info">
                                    <a href="chi_tiet_sach.php?masach=<?php echo $row['MaSach']; ?>" class="history-book-name"><?php echo $row['TenSach']; ?></a>
                                    <span class="history-chapter">📖 <?php echo $row['TenChuong']; ?></span>
                                </div>
                            </td>
                            <td><?php echo date("d/m/Y H:i", strtotime($row['NgayXem'])); ?></td>
                            <td><a href="doc_sach.php?id=<?php echo $row['chuong_id']; ?>" style="color: #ae1c55; font-weight:bold;">Đọc tiếp &raquo;</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?> <p>Bạn chưa đọc cuốn sách nào.</p> <?php endif; ?>
        </div>
        
        <div id="lich-su-tai" class="tab-content">
             <h2 style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 20px; color:#ae1c55;">Sách đã tải xuống</h2>
            <?php if(mysqli_num_rows($res_down) > 0): ?>
                <table class="history-table">
                    <thead><tr><th>Sách & Chương</th><th width="150">Thời gian tải</th><th width="100">Hành động</th></tr></thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_array($res_down)): ?>
                        <tr>
                            <td>
                                <img src="Admin/Hinh_sach/<?php echo $row['Hinh']; ?>" class="history-thumb">
                                <div class="history-book-info">
                                    <a href="chi_tiet_sach.php?masach=<?php echo $row['MaSach']; ?>" class="history-book-name"><?php echo $row['TenSach']; ?></a>
                                    <span class="history-chapter">📥 <?php echo $row['TenChuong']; ?></span>
                                </div>
                            </td>
                            <td><?php echo date("d/m/Y H:i", strtotime($row['NgayTai'])); ?></td>
                            <td><a href="tai_sach.php?id=<?php echo $row['chuong_id']; ?>" style="color: #337ab7; font-weight:bold;">Tải lại</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?> <p>Bạn chưa tải cuốn sách nào.</p> <?php endif; ?>
        </div>

        <div id="danh_sach_yeu_thich" class="tab-content">
            <h2 style="border-bottom: 1px dashed #ccc; padding-bottom: 10px; margin-bottom: 20px; color:#ae1c55;">Sách bạn đã thích</h2>
            
            <?php if(mysqli_num_rows($res_like) > 0): ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Sách</th>
                            <th width="150">Ngày Thích</th>
                            <th width="100">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_array($res_like)): ?>
                        <tr>
                            <td>
                                <img src="Admin/Hinh_sach/<?php echo $row['Hinh']; ?>" class="history-thumb">
                                <div class="history-book-info">
                                    <a href="chi_tiet_sach.php?masach=<?php echo $row['MaSach']; ?>" class="history-book-name"><?php echo $row['TenSach']; ?></a>
                                </div>
                            </td>
                            <td><?php echo date("d/m/Y H:i", strtotime($row['ngayThich'])); ?></td>
                            <td>
                                <a href="yeu_thich_sach.php?masach=<?php echo $row['MaSach']; ?>&from=profile" 
                                style="color: #ae1c55; font-weight:bold;"
                                onclick="return confirm('Bạn chắc chắn muốn bỏ thích sách này?');">
                                <i class="fa fa-heart-broken"></i> Bỏ Thích
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Bạn chưa thích cuốn sách nào.</p>
            <?php endif; ?>
        </div> 

    </div>
</div>

<script>
    // Script xử lý chuyển Tab
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }
        
        tablinks = document.getElementsByClassName("tab-link");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        
        // Nếu sự kiện click (evt) tồn tại thì thêm class active, nếu gọi tự động thì bỏ qua
        if(evt) evt.currentTarget.className += " active";
    }

    // Sau khi ta xóa sách đẫ thích, thay vì reset lại trang cá nhna thì nó sẽ trở về giao diện tab yêu thích, phần open_tab này được sử dụng bên yeu_thich_sach
    <?php if(isset($_GET['open_tab'])): ?>
        // Tìm nút nào mở tab này và click nó
        var tabId = "<?php echo $_GET['open_tab']; ?>";
        var btn = document.querySelector("button[onclick*='" + tabId + "']");
        if(btn) btn.click();
    <?php endif; ?>
</script>

<?php
mysqli_close($conn);
include ('includes/footer.html');
?>