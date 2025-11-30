<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thông Tin Tác Giả</title>
    <style>
        th{
            background-color: orange;
            color: red;
        }
        table{
            background-color: #fee0c1;
        }
        input, textarea { /* Thêm textarea vào style */
            background-color: #fff2cc;
        }
    </style>
</head>
<body>
<?php

    // Lấy mã tác giả từ GET hoặc POST
    if (isset($_GET['matg'])) {
        $matg = $_GET['matg'];
    } elseif (isset($_POST['matg'])) {
        $matg = $_POST['matg'];
    } else {
        echo "Lỗi: Không tìm thấy Mã Tác Giả!";
        exit();
    }

    // Lấy dữ liệu tác giả cũ
    $sql = "SELECT * FROM tacgia WHERE MaTG = '$matg'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) <> 0) {
        $row = mysqli_fetch_array($result);
        $tenTG = $row['TenTG'];
        $moTaTG = $row['MoTaTG'];
    } else {
        die('Không tìm thấy tác giả trong CSDL!');
    }

    // Xử lý khi người dùng nhấn nút cập nhật
    if (isset($_POST['sub'])) {
        if (empty($_POST['tentg']) || empty($_POST['mota'])) {
            echo "<p style='color:red;' align='center'> Bạn chưa điền đầy đủ thông tin!</p>";
        } else {
            // [ĐÃ SỬA] Thêm mysqli_real_escape_string
            $tentg = mysqli_real_escape_string($conn, $_POST['tentg']);
            $mota = mysqli_real_escape_string($conn, $_POST['mota']);

            $sql_capnhat = "UPDATE tacgia SET TenTG='$tentg', MoTaTG='$mota' WHERE MaTG='$matg'";

            if (mysqli_query($conn, $sql_capnhat)) {
                echo "<p style='color:green;' align='center'> Đã cập nhật thành công!</p>";
                // Cập nhật biến để hiển thị
                $tenTG = $tentg;
                $moTaTG = $mota;
            } else {
                echo "Lỗi khi cập nhật: " . mysqli_error($conn);
            }
        }
    }
    mysqli_close($conn);
?>

<form action="sua_tacgia.php" method="POST">
    <table align="center" border="0" cellpadding="5">
        <tr><th colspan="2"><h2><b>CẬP NHẬT THÔNG TIN TÁC GIẢ</b></h2></th></tr>

        <tr>
            <td><label>Mã Tác Giả:</label></td>
            <td><input type="text" name="matg" value="<?php echo $matg; ?>" style="width:250px;" readonly></td>
        </tr>

        <tr>
            <td><label>Tên Tác Giả:</label></td>
            <td><input type="text" name="tentg" value="<?php echo $tenTG; ?>" style="width:250px;"></td>
        </tr>
        
        <tr>
            <td><label>Mô Tả:</label></td>
            <td><textarea name="mota" rows="5" style="width:250px;"><?php echo $moTaTG; ?></textarea></td>
        </tr>

        <tr><th colspan="2"><input type="submit" name="sub" value="Cập Nhật"></th></tr>
    </table>
</form>
</body>
</html>