<?php # Script danh_gia.php
include ('includes/db_connect.php');
session_start();

// 1. KẾT NỐI VÀ KIỂM TRA
$masach = isset($_GET['masach']) ? trim($_GET['masach']) : '';
if (empty($masach)) { echo "Lỗi: Thiếu mã sách."; exit(); }


//  role_id = 1 là Admin 
$is_admin = (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1);

// --- [MỚI] XỬ LÝ LOGIC XÓA BÌNH LUẬN (Chỉ Admin mới chạy được) ---
if ($is_admin && isset($_GET['xoa_id'])) {
    $xoa_id = intval($_GET['xoa_id']); // Lấy ID bình luận cần xóa
    
    // Thực hiện xóa
    $sql_delete = "DELETE FROM danh_gia WHERE id = '$xoa_id'";
    if (mysqli_query($conn, $sql_delete)) {
        // Xóa xong thì load lại trang để mất tham số xoa_id trên URL
        echo "<script>alert('Đã xóa bình luận thành công!'); window.location.href='danh_gia.php?masach=$masach';</script>";
        exit();
    } else {
        echo "<script>alert('Lỗi khi xóa: " . mysqli_error($conn) . "');</script>";
    }
}
// ------------------------------------------------------------------

// Lấy tên sách để hiển thị tiêu đề
$ten_sach = "Sách không tồn tại";
$res_sach = mysqli_query($conn, "SELECT TenSach FROM sach WHERE MaSach='$masach'");
if(mysqli_num_rows($res_sach) > 0) {
    $row_s = mysqli_fetch_array($res_sach);
    $ten_sach = $row_s['TenSach'];
}

$page_title = 'Đánh giá: ' . $ten_sach;
include ("includes/header.php");

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$msg = "";

// 2. XỬ LÝ FORM (LƯU / SỬA) - Chỉ chạy nếu đã đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_id > 0) {
    $so_sao = intval($_POST['rating']);
    $noi_dung = mysqli_real_escape_string($conn, $_POST['noi_dung']);
    
    $sql_save = "INSERT INTO danh_gia (MaSach, userID, SoSao, NoiDung, NgayTao) 
                 VALUES ('$masach', '$user_id', '$so_sao', '$noi_dung', NOW())
                 ON DUPLICATE KEY UPDATE 
                 SoSao = '$so_sao', NoiDung = '$noi_dung', NgayTao = NOW()";
                 
    if (mysqli_query($conn, $sql_save)) {
        echo "<script>window.location.href='danh_gia.php?masach=$masach';</script>";
    } else {
        $msg = "<p style='color:red;'>Lỗi: " . mysqli_error($conn) . "</p>";
    }
}

// 3. LẤY DỮ LIỆU CỦA CHÍNH USER (ĐỂ ĐIỀN VÀO FORM)
$my_review = null;
if ($user_id > 0) {
    $sql_check = "SELECT * FROM danh_gia WHERE MaSach = '$masach' AND userID = '$user_id'";
    $res_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($res_check) > 0) {
        $my_review = mysqli_fetch_array($res_check);
    }
}

// 4. LẤY TẤT CẢ ĐÁNH GIÁ (CỘNG ĐỒNG)
$sql_all = "SELECT dg.*, u.hoUser, u.tenUser, u.avatar 
            FROM danh_gia dg 
            JOIN users u ON dg.userID = u.userID 
            WHERE dg.MaSach = '$masach' 
            ORDER BY dg.NgayTao DESC";
$res_all = mysqli_query($conn, $sql_all);
?>

<style>
    /* CSS CHUNG */
    .review-page-container { width: 80%; max-width: 1000px; margin: 20px auto; display: flex; gap: 20px; flex-direction: column; }
    
    /* KHUNG VIẾT ĐÁNH GIÁ */
    .my-review-box { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-start; font-size: 2em; }
    .star-rating input { display: none; }
    .star-rating label { color: #ddd; cursor: pointer; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #f5b301; }
    textarea { width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ccc; font-family: inherit; }
    .btn-submit { background: #ae1c55; color: white; padding: 8px 20px; border: none; cursor: pointer; margin-top: 10px; font-weight: bold; }
    
    /* DANH SÁCH ĐÁNH GIÁ */
    .community-reviews { background: #f9f9f9; padding: 20px; border: 1px solid #eee; border-radius: 5px; }
    .review-item { display: flex; border-bottom: 1px solid #ddd; padding: 15px 0; }
    .review-item:last-child { border-bottom: none; }
    
    .review-avatar { width: 50px; margin-right: 15px; }
    .review-avatar img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid #ccc; }
    
    .review-content { flex-grow: 1; }
    .review-header-line { display: flex; justify-content: space-between; align-items: center; } /* Mới: Để căn nút xóa sang phải */
    
    .review-author { font-weight: bold; color: #333; font-size: 1.1em; }
    .review-time { font-size: 0.8em; color: #777; margin-left: 10px; }
    .review-stars-display { color: #f5b301; margin: 5px 0; font-size: 1.2em; }
    .review-text { color: #444; line-height: 1.5; }

    /* CSS CHO NÚT XÓA ADMIN */
    .btn-delete-admin {
        color: red;
        font-size: 0.85em;
        text-decoration: none;
        border: 1px solid red;
        padding: 2px 8px;
        border-radius: 4px;
        transition: 0.3s;
    }
    .btn-delete-admin:hover {
        background-color: red;
        color: white;
    }
</style>

<div class="review-page-container">
    
    <div class="my-review-box">
        <h2 style="margin-top:0; border-bottom: 1px dashed #ccc; padding-bottom:10px;">
            Đánh giá: <span style="color:#ae1c55;"><?php echo $ten_sach; ?></span>
        </h2>
        
        <?php echo $msg; ?>

        <?php if ($user_id > 0): ?>
            <form action="" method="POST">
                <div class="star-rating">
                    <?php 
                    $current_rating = ($my_review) ? $my_review['SoSao'] : 5;
                    for($i=5; $i>=1; $i--){
                        $checked = ($i == $current_rating) ? "checked" : "";
                        echo "<input type='radio' id='star$i' name='rating' value='$i' $checked />";
                        echo "<label for='star$i' title='$i sao'>&#9733;</label>";
                    }
                    ?>
                </div>
                <p style="margin: 5px 0; font-weight:bold; color:#555;">Cảm nhận của bạn:</p>
                <textarea name="noi_dung" rows="4" placeholder="Nội dung hấp dẫn, nhân vật thú vị..."><?php echo ($my_review) ? $my_review['NoiDung'] : ''; ?></textarea>
                
                <input type="submit" class="btn-submit" value="<?php echo ($my_review) ? 'Cập nhật' : 'Gửi đánh giá'; ?>">
                <a href="chi_tiet_sach.php?masach=<?php echo $masach; ?>" style="float:right; margin-top:15px; color:#777;">&larr; Quay lại sách</a>
            </form>
        <?php else: ?>

            <p align="center">Bạn cần <a href="dang_nhap.php?masach=<?php echo $masach; ?> & from=danh_gia.php " style="color:#ae1c55; font-weight:bold;">Đăng nhập</a> để viết đánh giá.</p>
        <?php endif; ?>
    </div>

    <div class="community-reviews">
        <h3 style="margin-top:0;">Bình luận & Đánh giá (<?php echo mysqli_num_rows($res_all); ?>)</h3>
        
        <?php
        if (mysqli_num_rows($res_all) > 0) {
            while ($rv = mysqli_fetch_array($res_all)) {
                echo "<div class='review-item'>";
                
                // Cột Avatar
                echo "  <div class='review-avatar'>";
                $src= "Admin/Hinh_user/{$rv['avatar']}";
                echo "      <img  src='$src' alt='Avatar'>"; 
                echo "  </div>";
                
                // Cột Nội dung
                echo "  <div class='review-content'>";
                
                // --- [MỚI] Dòng tiêu đề chứa Tên + Nút Xóa (nếu là Admin) ---
                echo "      <div class='review-header-line'>";
                echo "          <div>";
                echo "              <span class='review-author'>" . $rv['hoUser'] . " " . $rv['tenUser'] . "</span>";
                echo "              <span class='review-time'>" . $rv['NgayTao'] . "</span>";
                echo "          </div>";

                // Chỉ hiển thị nút xóa nếu là Admin
                if ($is_admin) {
                    echo "      <a href='danh_gia.php?masach=$masach&xoa_id=" . $rv['id'] . "' 
                                   class='btn-delete-admin' 
                                   onclick='return confirm(\"Bạn chắc chắn muốn xóa bình luận này chứ?\");'>
                                   <i class='fa fa-trash'></i> Xóa
                                </a>";
                }
                echo "      </div>"; 
                // -----------------------------------------------------------

                // Hiển thị sao vàng
                echo "      <div class='review-stars-display'>";
                for($k=0; $k < $rv['SoSao']; $k++) echo "&#9733;";
                echo "      </div>";
                
                echo "      <div class='review-text'>" . nl2br($rv['NoiDung']) . "</div>";
                echo "  </div>";
                
                echo "</div>"; // Đóng review-item
            }
        } else {
            echo "<p style='text-align:center; color:#777;'>Chưa có đánh giá nào. Hãy là người đầu tiên!</p>";
        }
        ?>
    </div>

</div>

<?php 
mysqli_close($conn);
include ("includes/footer.html"); 
?>