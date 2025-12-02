<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Thể Loại</title>
    <style>
        th{
    background:#ae1c55;
    color:#fff;
    font-size:18px;
}

table{
    background:#fddedc;
}

td{
    padding:5px;
}

input,
textarea{
    background:#fff;
    width:300px;
    padding:5px;
    border:1px solid #bbb;
}

    </style>
</head>
<body>
<?php
    // Lấy mã thể loại từ GET hoặc POST
    if (isset($_GET['matheloai'])) {
        $matheloai = $_GET['matheloai'];
    } elseif (isset($_POST['matheloai'])) {
        $matheloai = $_POST['matheloai'];
    } else {
        echo "Lỗi: Không tìm thấy Mã Thể Loại!";
        exit(); // Dừng nếu không có ID
    }

    // Lấy dữ liệu thể loại cũ
    $sql = "SELECT * FROM theloai WHERE MaTheLoai = '$matheloai'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) <> 0) {
        $row = mysqli_fetch_array($result);
        $tenTL = $row['TenTheLoai'];
    } else {
        die('Không tìm thấy thể loại trong CSDL!');
    }

    // Xử lý khi người dùng nhấn nút cập nhật
   if (isset($_POST['sub'])) {
        if (empty($_POST['tentl'])) {
            echo "<p style='color:red;' align='center'> Bạn chưa điền tên thể loại!</p>";
        } else {
            // [ĐÃ SỬA] Thêm mysqli_real_escape_string
            $tentl = mysqli_real_escape_string($conn, $_POST['tentl']);

            $sql_capnhat = "UPDATE theloai SET TenTheLoai='$tentl' WHERE MaTheLoai='$matheloai'";

            if (mysqli_query($conn, $sql_capnhat)) {
                echo "<p style='color:green;' align='center'> Đã cập nhật thành công!</p>";
                // Cập nhật lại biến $tenTL để hiển thị trong form
                $tenTL = $tentl; 
            } else {
                echo "Lỗi khi cập nhật: " . mysqli_error($conn);
            }
        }
    }
    mysqli_close($conn); // Đóng kết nối
?>

<form action="sua_theloai.php" method="POST">
    <table align="center" border="0" cellpadding="5">
        <tr><th colspan="2"><h2><b>CẬP NHẬT THÔNG TIN THỂ LOẠI</b></h2></th></tr>

        <tr>
            <td><label>Mã Thể Loại:</label></td>
            <td><input type="text" name="matheloai" value="<?php echo $matheloai; ?>" style="width:250px;" readonly></td>
        </tr>

        <tr>
            <td><label>Tên Thể Loại:</label></td>
            <td><input type="text" name="tentl" value="<?php echo $tenTL; ?>" style="width:250px;"></td>
        </tr>

        <tr><th colspan="2"><input type="submit" name="sub" value="Cập Nhật"></th></tr>
    </table>
</form>
<div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="admin_index_theloai.php" 
        style="
            color: #ae1c55; 
            font-weight: bold; 
            font-size: 14px; 
            border: 2px solid #ae1c55; 
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
        ">
        &laquo; Quay Về Danh Sách
    </a>
    </div>
</body>
</html>