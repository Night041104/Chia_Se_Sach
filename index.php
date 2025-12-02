<?php # Script 3.4 - index.php
$page_title = 'Chào mừng đến với Chia Sẻ Sách!';
include ("includes/header.php");
include ("includes/phan_trang.php");

// 1. Kết nối CSDL
$p = new Phan_trang(8);
$conn = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL");
mysqli_set_charset($conn, 'UTF8');

// 2. QUERY 1: LẤY TẤT CẢ SÁCH + ĐIỂM ĐÁNH GIÁ TRUNG BÌNH
// Sử dụng LEFT JOIN để lấy điểm từ bảng danh_gia và hàm AVG để tính trung bình
$sql_all_books = "SELECT s.MaSach, s.TenSach, s.Hinh, s.TinhTrang, 
                         AVG(dg.SoSao) as DiemTrungBinh 
                  FROM sach s
                  LEFT JOIN danh_gia dg ON s.MaSach = dg.MaSach
                  GROUP BY s.MaSach
                  ORDER BY s.TenSach ASC LIMIT ".$p->getLimitPage();

$result_all_books = mysqli_query($conn, $sql_all_books);

// 3. QUERY 2: LẤY 10 SÁCH XEM NHIỀU NHẤT (cho Cột Phải)
$sql_top_books = "SELECT MaSach, TenSach, Hinh, LuotDoc FROM sach ORDER BY LuotDoc DESC LIMIT 3";
$result_top_books = mysqli_query($conn, $sql_top_books);
?>

<style>
    /* === CSS CHO CÁC THÀNH PHẦN HIỂN THỊ TRÊN ẢNH === */
    
    /* 1. Định vị thẻ cha để các thẻ con (badge) bám vào */
    .book-cell {
        position: relative; 
        overflow: hidden;   
    }
    
    /* 2. Badge ĐIỂM SỐ (Góc Trái Trên) */
    .rating-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: rgba(0, 0, 0, 0.7); /* Nền đen mờ */
        color: #ffc107; /* Màu vàng ngôi sao */
        padding: 4px 10px;
        border-radius: 20px; /* Bo tròn kiểu viên thuốc */
        font-size: 0.85em;
        font-weight: bold;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 5px; /* Khoảng cách giữa sao và số */
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }
    
    /* 3. Thanh TÌNH TRẠNG (Góc Dưới) */
    .status-badge {
        position: absolute;
        bottom: 35px; /* Cách đáy 35px để không che tên sách */
        left: 0;
        width: 100%;
        text-align: center;
        color: white;
        font-size: 0.75em;
        padding: 4px 0;
        font-weight: bold;
        text-transform: uppercase;
        background-color: rgba(40, 167, 69, 0.9); /* Mặc định: Xanh lá */
        z-index: 10;
    }
    /* Các màu trạng thái khác nhau */
    .status-badge.tam-ngung { background-color: rgba(108, 117, 125, 0.9); } /* Xám */
    .status-badge.hoan-thanh { background-color: rgba(0, 123, 255, 0.9); } /* Xanh dương */
</style>

<div class="content-container-2col">

    <div class="main-content">

        <?php
        if (mysqli_num_rows($result_all_books) > 0) {
            
            echo "<div class='book-grid'>"; 

            while ($row = mysqli_fetch_array($result_all_books)) {
                
                $hinh_path = "Admin/Hinh_sach/" . $row['Hinh'];
                $chi_tiet_sach = "chi_tiet_sach.php?masach=" . $row['MaSach'];
                
                // --- XỬ LÝ LOGIC HIỂN THỊ ---
                
                // 1. Xử lý điểm số (Làm tròn 1 số lẻ, ví dụ 4.5)
                $diem_tb = $row['DiemTrungBinh'] ? number_format($row['DiemTrungBinh'], 1) : 0;
                
                // 2. Xử lý class màu sắc cho tình trạng
                $status_class = "";
                if($row['TinhTrang'] == 'Đã hoàn thành') $status_class = "hoan-thanh";
                elseif($row['TinhTrang'] == 'Tạm ngưng') $status_class = "tam-ngung";
                
                // --- BẮT ĐẦU IN HTML ---
                echo "<a href='{$chi_tiet_sach}'>";
                echo "<div class='book-cell'>";
                
                // [A] HIỂN THỊ ĐIỂM ĐÁNH GIÁ (Nếu có điểm)
                if ($diem_tb > 0) {
                    echo "<div class='rating-badge'>
                            <i class='fas fa-star'></i> $diem_tb
                          </div>";
                }
                
                // [B] HIỂN THỊ ẢNH BÌA
                echo "<img src='{$hinh_path}' alt='" . $row['TenSach'] . "' />";
                
                // [C] HIỂN THỊ THANH TÌNH TRẠNG
                if (!empty($row['TinhTrang'])) {
                    echo "<div class='status-badge $status_class'>" . $row['TinhTrang'] . "</div>";
                }
                
                // [D] HIỂN THỊ TÊN SÁCH
                echo "<div class='book-title'>" . $row['TenSach'] . "</div>";
                
                echo "</div>"; // Đóng book-cell
                echo "</a>"; // Đóng link
            }
            
            echo "</div>"; // Đóng book-grid

        } else {
            echo "<p align='center'>Chưa có sách nào trong thư viện.</p>";
        }
        $p->paging(mysqli_query($conn,"SELECT * FROM sach"));
        ?>
    </div> 
    
    <div class="sidebar-content">
        <div class="sidebar-widget">
            <h3 class="widget-title">SÁCH XEM NHIỀU</h3>
            
            <ul class="top-books-list">
                <?php
                if (mysqli_num_rows($result_top_books) > 0) {
                    while ($row_top = mysqli_fetch_array($result_top_books)) {
                        $hinh_path_top = "Admin/Hinh_sach/" . $row_top['Hinh'];
                        $link_top = "chi_tiet_sach.php?masach=" . $row_top['MaSach'];
                        
                        echo "<li>";
                        echo "  <a href='{$link_top}'>";
                        echo "    <img src='{$hinh_path_top}' alt='{$row_top['TenSach']}' />";
                        echo "    <div class='top-book-info'>";
                        echo "      <span>" . $row_top['TenSach'] . "</span>";
                        echo "      <small>👁️ " . $row_top['LuotDoc'] . " lượt xem</small>";
                        echo "    </div>";
                        echo "  </a>";
                        echo "</li>";
                    }
                } else {
                    echo "<li>Chưa có dữ liệu.</li>";
                }
                ?>
            </ul>
        </div>
    </div> 

</div> 

<?php
// Đóng kết nối
mysqli_close($conn);
include ("includes/footer.html");
?>