<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem chi tiết sách</title>
</head>
<body>
    <?php
        $masach = isset($_GET['masach'])?$_GET['masach']:'';

        $sql = "SELECT s.*,
                    GROUP_CONCAT(DISTINCT tg.TenTG SEPARATOR ', ') as TacGia,
                    GROUP_CONCAT(DISTINCT tl.TenTheLoai SEPARATOR ', ') as TheLoai
                FROM sach s
                JOIN sach_tacgia stg ON s.MaSach = stg.MaSach
                JOIN tacgia tg ON stg.MaTG = tg.MaTG
                JOIN sach_theloai stl ON s.MaSach = stl.MaSach
                JOIN theloai tl ON tl.MaTheLoai = stl.MaTheLoai
                WHERE s.MaSach = '$masach'
                GROUP BY s.MaSach";            
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result)>0)
        {
            $row= mysqli_fetch_array($result);
            echo "<table width='600px' align='center' border='1' cellpadding='2'>";
                echo "<tr><th colspan='2' style='background-color:#fee0c1; padding:4px'><p align='center' style='font-size:30px; font-weight:bold; color:#f86500; margin:0;'><i>{$row['TenSach']}</i></p></th></tr>";
                echo "<tr>";
                echo "<td width='30%'><div align='center'><img src='Hinh_sach/{$row['Hinh']}' width='100px'></div></td>";
                echo "<td width='70%' style='padding:8px'>
                        <p><i><b>Tác giả:</b> {$row['TacGia']}</p>
                        <p><i><b>Thể loại:</b> {$row['TheLoai']}</p>
                        <p><i><b>Tình trạng:</b> {$row['TinhTrang']}</p>
                        <p><i><b>Lượt đọc:</b> {$row['LuotDoc']} <b>Lượt tải:</b> {$row['LuotTai']}</p>
                        <p><i><b>Mô tả:</b></i></p>
                        <p style='text-align: justify;'>{$row['MoTa']}</p>
                    </td>";
                echo "</tr>";
            echo "</table>";
        }

    ?>
    <div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="admin_index_sach.php" 
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