<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sách</title>
</head>
<body>
    <?php 
        $sql = 'select MaSach, TenSach, MoTa, Hinh, TinhTrang from sach ORDER BY TenSach ASC';
        $result = mysqli_query($conn,$sql);

        // 3. Tiêu đề
        echo "<p align='center'><font size='5px' style='font-weight: bold;'>QUẢN LÝ SÁCH</font></p>";
        echo "<table align='center' width='95%' border='1' cellpadding='2'>";
        
        // 4. Tiêu đề bảng (THÊM CỘT 'Thêm chương')
        echo '
            <tr style="color: #ae1c55; font-size:18px;">
                <th>Mã Sách</th>
                <th>Tên Sách</th>
                
                <th>Ảnh bìa</th>
                <th>Tình trạng</th>
                <th>Quản lý Chương</th>
                <th>Xem chi tiết</th>
                <th>Sửa</th>
                <th>Xóa</th>
            </tr>';
        
        // 5. Lặp qua dữ liệu
        if(mysqli_num_rows($result)<>0)
        {
            $cnt = 0;
            $bg;
            while($rows = mysqli_fetch_array($result))
            {
                $cnt++;
                if($cnt <= 1) $bg = "#fee0c1";
                else $bg = "#ffffff";
                
                echo "<tr style='background-color: $bg;'>";
                    
                    // Mã sách
                    echo"<td align='center'>{$rows['MaSach']}</td>";
                    
                    // Tên sách
                    echo"<td align='center'>{$rows['TenSach']}</td>";
                    
                    
                    
                    // Ảnh bìa
                    $hinh_path = "Hinh_sach/" . $rows['Hinh'];
                    echo"<td align='center'><img src='{$hinh_path}' width='50px' alt='{$rows['TenSach']}' /></td>";
                    echo"<td align='center'>{$rows['TinhTrang']}</td>";
                    // --- CỘT THÊM CHƯƠNG (MỚI) ---
                    // Chuyển sang trang them_chuong.php và truyền mã sách
                    echo"<td align='center'>
                            <a href='them_chuong.php?masach={$rows['MaSach']}' style='color:green; font-weight:bold; text-decoration:none;'>
                                Quản lý chương
                            </a>
                        </td>";
                    // -----------------------------
                    echo"<td align='center'><a href='xem_chi_tiet.php?masach={$rows['MaSach']}' >Chi tiết</a></td>";
                    // Cột Sửa
                    echo"<td align='center'><a href='sua_sach.php?masach={$rows['MaSach']}' >Sửa</a></td>";
                    
                    // Cột Xóa
                    echo"<td align='center'><a href='xoa_sach.php?masach={$rows['MaSach']}'>Xóa</a></td>";
                    
                echo"</tr>";
                
                if($cnt==2){ $cnt = 0; }
            }
        }
        echo "</table>";
        
        mysqli_close($conn);
     ?>
     <div style="text-align:center; margin-top:20px;">
        <a href="index.php">Quay Về Dashboard</a>
     </div>
</body>
</html>