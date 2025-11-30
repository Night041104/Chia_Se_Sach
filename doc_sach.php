<?php # Script doc_sach.php
session_start();

// 1. BẢO VỆ: KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['username'])) {
    header("Location: dang_nhap.php?id=".$_GET['id']."& from=doc_sach.php"); 
    exit();
}
//userID
$user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:0;
// 2. KẾT NỐI CSDL
$conn = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL");
mysqli_set_charset($conn, 'UTF8');

// 3. KHỞI TẠO BIẾN
$id_chuong = isset($_GET['id']) ? intval($_GET['id']) : 0;
$file_path_pdf = "";
$page_title = "Đọc sách"; 

// Biến lưu ID chương trước/sau
$prev_id = 0;
$next_id = 0;

// 4. XỬ LÝ LOGIC
if ($id_chuong > 0) {
    
    // A. Lấy thông tin Chương hiện tại
    $sql = "SELECT c.FilePath, c.TenChuong, c.MaSach, s.TenSach 
            FROM chuong c 
            JOIN sach s ON c.MaSach = s.MaSach 
            WHERE c.id = '$id_chuong'";
            
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        
        $page_title = $row['TenSach'] . " - " . $row['TenChuong'];
        $base_url = "Admin/File_sach/"; 
        $pdf_url = $base_url . $row['FilePath'];
        $file_path_pdf = "pdfjs/web/viewer.html?file=../../" . $pdf_url;
        $masach = $row['MaSach'];
        
        // --- [MỚI] TÌM CHƯƠNG TRƯỚC & CHƯƠNG TIẾP ---
        
        // 1. Tìm chương TRƯỚC (Cùng mã sách, ID nhỏ hơn ID hiện tại, lấy cái lớn nhất trong đám nhỏ hơn)
        $sql_prev = "SELECT id FROM chuong WHERE MaSach = '$masach' AND id < $id_chuong ORDER BY id DESC LIMIT 1";
        $res_prev = mysqli_query($conn, $sql_prev);
        if ($row_prev = mysqli_fetch_assoc($res_prev)) {
            $prev_id = $row_prev['id'];
        }

        // 2. Tìm chương TIẾP (Cùng mã sách, ID lớn hơn ID hiện tại, lấy cái nhỏ nhất trong đám lớn hơn)
        $sql_next = "SELECT id FROM chuong WHERE MaSach = '$masach' AND id > $id_chuong ORDER BY id ASC LIMIT 1";
        $res_next = mysqli_query($conn, $sql_next);
        if ($row_next = mysqli_fetch_assoc($res_next)) {
            $next_id = $row_next['id'];
        }
        // ---------------------------------------------
        
        // B. Tăng lượt đọc
        mysqli_query($conn, "UPDATE sach SET LuotDoc = LuotDoc + 1 WHERE MaSach = '$masach'");
        mysqli_query($conn, "UPDATE chuong SET LuotDoc = LuotDoc + 1 WHERE id = '$id_chuong'");
        
        // C. Ghi lịch sử đọc
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $sql_his = "INSERT INTO lich_su_doc (userID, MaSach, chuong_id, NgayXem) 
                        VALUES ('$user_id', '$masach', '$id_chuong', NOW())
                        ON DUPLICATE KEY UPDATE NgayXem = NOW()";
            mysqli_query($conn, $sql_his);
        }
    }
}

include ("includes/header.php"); 
?>

<style>
    .reader-container {
        /* 1. Thiết lập chiều rộng tối đa (ví dụ 900px hoặc 80%) */
        width: 100%;       /* Trên điện thoại vẫn full màn hình */
        max-width: 900px;  /* Trên máy tính chỉ rộng tối đa 900px thôi */
        
        /* 2. Căn giữa khung đọc */
        margin: 5px auto; 
        
        /* 3. Tạo bóng đổ nhẹ để nổi bật trang giấy (Tùy chọn cho đẹp) */
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 10px;
        height: 100vh; /* Giữ nguyên chiều cao */
        border: 1px solid #ccc;
        background: #fff;
    }
    #pdf-reader-frame {
        width: 100%;
        height: 100%;
        border: none;
        display: block;
    }
    .reader-header {
        width: 100%;       /* Trên điện thoại vẫn full màn hình */
        max-width: 900px;
        margin: 5px auto;
        border-radius: 10px;
        background: #333;
        color: white;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Cho phép xuống dòng trên mobile */
    }
    .reader-info {
        font-size: 1.1em;
    }
    
    /* STYLE CHO NÚT ĐIỀU HƯỚNG */
    .nav-group {
        display: flex;
        gap: 10px;
    }
    .nav-btn {
        background-color: #555;
        color: white;
        padding: 5px 15px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.9em;
        border: 1px solid #666;
        transition: 0.3s;
    }
    .nav-btn:hover {
        background-color: #ae1c55; /* Màu chủ đạo của bạn */
        border-color: #ae1c55;
        color: white;
    }
    .nav-btn.disabled {
        background-color: #444;
        color: #777;
        cursor: not-allowed;
        border-color: #444;
        pointer-events: none; /* Không cho click */
        opacity: 0.6;
    }
    .back-link {
        color: #ddd; text-decoration: none; margin-left: 20px;
    }
    .back-link:hover { color: white; }
</style>
    <div id="content" style="width:100%; padding:0; margin:0;">
        
        <?php if (!empty($file_path_pdf)): ?>
            
            <div class="reader-header">
                
                <div class="reader-info">
                    <span>📖 <b><?php echo $row['TenChuong']; ?></b></span>
                </div>

                <div class="nav-group">
                    <?php if ($prev_id > 0): ?>
                        <a href="doc_sach.php?id=<?php echo $prev_id; ?>" class="nav-btn">
                            &laquo; Chương trước
                        </a>
                    <?php else: ?>
                        <span class="nav-btn disabled">&laquo; Chương trước</span>
                    <?php endif; ?>

                    <?php if ($next_id > 0): ?>
                        <a href="doc_sach.php?id=<?php echo $next_id; ?>" class="nav-btn">
                            Chương tiếp &raquo;
                        </a>
                    <?php else: ?>
                        <span class="nav-btn disabled">Chương tiếp &raquo;</span>
                    <?php endif; ?>
                </div>

                <div>
                    <a href="chi_tiet_sach.php?masach=<?php echo $masach; ?>" class="back-link">
                        &larr; Chi tiết sách
                    </a>
                </div>

            </div>

            <div class="reader-container">
                <iframe id="pdf-reader-frame" src="<?php echo $file_path_pdf; ?>"></iframe>
            </div>

        <?php else: ?>
            
            <div style="padding: 50px; text-align: center;">
                <h2 style="color:red;">Lỗi: Không tìm thấy nội dung chương.</h2>
                <p>Vui lòng quay lại và chọn chương khác.</p>
                <a href="index.php">Về trang chủ</a>
            </div>

        <?php endif; ?>

    </div>
<?php
mysqli_close($conn);
include ("includes/footer.html");
?>