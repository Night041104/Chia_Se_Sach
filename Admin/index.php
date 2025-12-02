<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang quản trị</title>
    <link rel="stylesheet" href="../includes/style.css" type="text/css" media="screen" />
    
    <style>
    /* --- Container chính --- */
.admin-dashboard {
    width: 95%;
    max-width: 1300px;
    margin: 30px auto;
    font-family: Arial, sans-serif;
}

/* Tiêu đề dashboard */
.admin-title {
    text-align: center;
    color: #ae1c55;   /* Màu đồng bộ với nút */
    margin-bottom: 40px;
    border-bottom: 3px solid #f0f0f0;
    padding-bottom: 15px;
    font-size: 2.2em;
    letter-spacing: 1px;
}


/* Lưới card */
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    justify-items: center;
}

/* Card nâng cấp */
.admin-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    width: 100%;
    max-width: 320px;
    padding: 25px 20px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.admin-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

/* Tiêu đề card */
.card-header {
    font-size: 1.6em;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

/* Số lượng thống kê */
.card-stat {
    font-size: 3em;
    color: #ae1c55;
    font-weight: bold;
    margin-bottom: 20px;
    text-align: center;
}

/* Các nút bấm */
.card-actions {
    width: 100%;
    display: flex;
    gap: 12px;
}
.btn-admin {
    flex: 1;
    padding: 12px;
    text-align: center;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    font-size: 1em;
    transition: all 0.2s ease;
}
.btn-view {
    background-color: #337ab7;
    color: white;
}
.btn-view:hover { 
    background-color: #286090; 
    transform: translateY(-2px);
}

.btn-add {
    background-color: #5cb85c;
    color: white;
}
.btn-add:hover { 
    background-color: #449d44; 
    transform: translateY(-2px);
}

/* Responsive nhẹ */
@media (max-width: 600px) {
    .card-stat {
        font-size: 2.2em;
    }
    .card-header {
        font-size: 1.3em;
    }
    .btn-admin {
        font-size: 0.9em;
        padding: 10px;
    }
}

</style>
</head>
<body>
<?php

$page_title = 'Trang Quản Trị Admin';


// Đếm Sách
$count_sach = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM sach"))[0];
// Đếm Thể loại
$count_tl = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM theloai"))[0];
// Đếm Tác giả
$count_tg = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM tacgia"))[0];
// Đếm User (Nếu bạn muốn)
$count_user = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];

mysqli_close($conn);
?>



<div class="admin-dashboard">
    <h1 class="admin-title">BẢNG ĐIỀU KHIỂN QUẢN TRỊ</h1>

    <div class="admin-grid">

        <div class="admin-card">
            <div class="card-header">📚 Sách</div>
            <div class="card-stat"><?php echo $count_sach; ?></div>
            <div class="card-actions">
                <a href="admin_index_sach.php" class="btn-admin btn-view">Xem DS</a>
                <a href="them_sach.php" class="btn-admin btn-add">Thêm Mới</a>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">🏷️ Thể Loại</div>
            <div class="card-stat"><?php echo $count_tl; ?></div>
            <div class="card-actions">
                <a href="admin_index_theloai.php" class="btn-admin btn-view">Xem DS</a>
                <a href="them_theloai.php" class="btn-admin btn-add">Thêm Mới</a>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">✍️ Tác Giả</div>
            <div class="card-stat"><?php echo $count_tg; ?></div>
            <div class="card-actions">
                <a href="admin_index_tacgia.php" class="btn-admin btn-view">Xem DS</a>
                <a href="them_tacgia.php" class="btn-admin btn-add">Thêm Mới</a>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">👥 Người dùng</div>
            <div class="card-stat"><?php echo $count_user; ?></div>
            <div class="card-actions">
                <a href="admin_index_user.php" class="btn-admin btn-view">Xem DS</a>
                <a href="them_user.php" class="btn-admin btn-add">Thêm Mới</a>
            </div>
        </div>

    </div>
</div>
<div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
   <a href="../index.php" 
      style="
         color: #ae1c55; 
         font-weight: bold; 
         font-size: 20px; 
         border: 2px solid #ae1c55; 
         padding: 8px 18px;
         border-radius: 6px;
         text-decoration: none;
      ">
      Quay Về Trang Người Dùng
   </a>
</div>


<?php
include('../includes/footer.html');
?>    
</body>
</html>
