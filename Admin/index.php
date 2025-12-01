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
    /* Container chính */
    .admin-dashboard {
        width: 90%;
        max-width: 1200px;
        margin: 30px auto;
        font-family: Arial, sans-serif;
    }

    .admin-title {
        text-align: center;
        color: #ae1c55;
        margin-bottom: 30px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    /* Lưới các thẻ (Cards) */
    .admin-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    /* Style cho từng thẻ */
    .admin-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        width: 300px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }

    /* Tiêu đề thẻ */
    .card-header {
        font-size: 1.5em;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    
    /* Số lượng thống kê */
    .card-stat {
        font-size: 2.5em;
        color: #ae1c55;
        font-weight: bold;
        margin-bottom: 20px;
    }

    /* Các nút bấm */
    .card-actions {
        width: 100%;
        display: flex;
        gap: 10px;
    }
    .btn-admin {
        flex: 1;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.9em;
    }
    .btn-view {
        background-color: #337ab7;
        color: white;
    }
    .btn-view:hover { background-color: #286090; }
    
    .btn-add {
        background-color: #5cb85c;
        color: white;
    }
    .btn-add:hover { background-color: #449d44; }

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

<?php
include('../includes/footer.html');
?>    
</body>
</html>
