<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông tin Tác Giả</title>
</head>
<body>
    <?php 

        $sql = 'select MaTG, TenTG, MoTaTG from tacgia ORDER BY TenTG ASC';
        $result = mysqli_query($conn,$sql);

        echo "<p align='center'><font size='5px' style='font-weight: bold;'>THÔNG TIN TÁC GIẢ</font></p>";
        echo "<table align='center' width='900' border='1' cellpadding='2'>";
        
        echo '
            <tr style="color: #ae1c55; font-size:18px;">
                <th width="15%">
                    Mã Tác Giả
                </th>
                <th width="25%">
                    Tên Tác Giả
                </th>
                <th width="40%">
                    Mô tả
                </th>
                <th>
                    Sửa
                </th>
                <th>
                    Xóa
                </th>
            </tr>';
        
        if(mysqli_num_rows($result)<>0)
        {
            $cnt = 0;
            $bg;
            while($rows = mysqli_fetch_array($result))
            {
                $cnt++;
                if($cnt <= 1)
                    $bg = "#fee0c1";
                else 
                    $bg = "#ffffff";
                echo "<tr style='background-color: $bg;'>";
                    
                    // $rows[0] = MaTG
                    echo"<td align='center'>{$rows['MaTG']}</td>";
                    
                    // $rows[1] = TenTG (THÊM ALIGN='CENTER')
                    echo"<td align='center'>{$rows['TenTG']}</td>";
                    
                    // $rows[2] = MoTaTG (Áp dụng logic cắt ngắn)
                    $mota = $rows[2];
                    $mota_hienthi = $mota;
                    $gioi_han_ky_tu = 100; 

                    if(mb_strlen($mota, 'UTF-8') > $gioi_han_ky_tu)
                    {
                        $mota_hienthi = mb_substr($mota, 0, $gioi_han_ky_tu, 'UTF-8') . "...";
                    }
                    // (THÊM ALIGN='CENTER')
                    echo"<td align='center'>$mota_hienthi</td>";
                    
                    // Cột Sửa
                    echo"<td align='center'><a href='sua_tacgia.php?matg={$rows['MaTG']}' >Sửa</a></td>";
                    
                    // Cột Xóa
                    echo"<td align='center'><a href='xoa_tacgia.php?matg={$rows['MaTG']}' >Xóa</a></td>";
                    
                echo"</tr>";
                
                if($cnt==2){
                    $cnt = 0;
                }
            }
        }
        echo "</table>";
        
        mysqli_close($conn);
     ?>
     
</body>
</html>