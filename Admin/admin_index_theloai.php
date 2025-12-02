<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
include ('../includes/phan_trang.php')
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông tin Thể Loại</title>
</head>
<body>
    <?php 
        $p = new Phan_trang(4);
        $sql = 'select MaTheLoai, TenTheLoai from theloai ORDER BY TenTheLoai ASC LIMIT '.$p->getLimitPage();
        $result = mysqli_query($conn,$sql);

        echo "<p align='center'><font size='5px' style='font-weight: bold;'>THÔNG TIN THỂ LOẠI</font></p>";
        echo "<table align='center' width='1200' border='1' cellpadding='10' style='font-size:20px;'>";
        echo '
            <tr style="color: #ae1c55; font-size:18px;">
                <th width="20%">
                    Mã Thể Loại
                </th>
                <th width="60%">
                    Tên Thể Loại
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
                    echo"<td align='center'>{$rows['MaTheLoai']}</td>";
                    echo"<td align='center'>{$rows['TenTheLoai']}</td>";
                    
                    // Cột Sửa
                    echo"<td align='center'><a href='sua_theloai.php?matheloai={$rows['MaTheLoai']}' >Sửa</a></td>";
                    
                    // Cột Xóa
                    echo"<td align='center'><a href='xoa_theloai.php?matheloai={$rows['MaTheLoai']}' >Xóa</a></td>";
                    
                echo"</tr>";
                
                if($cnt==2){
                    $cnt = 0;
                }
            }
        }
        echo "</table>";
        $p->paging(mysqli_query($conn,"SELECT * FROM theloai"));
        mysqli_close($conn);
     ?>
    <div style="text-align:center; margin-top:20px; margin-bottom: 20px;">
        <a href="index.php" 
        style="
            color: #ae1c55; 
            font-weight: bold; 
            font-size: 14px; 
            border: 2px solid #ae1c55; 
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
        ">
        &laquo; Quay Về Dashboard
    </a>
    </div>
</body>
</html>