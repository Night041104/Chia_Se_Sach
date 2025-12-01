<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông tin Users</title>
</head>
<body>
    <?php 
        $sql = 'select userID, hoUser, tenUser, email, password, ngayTao, roleID, avatar from users ORDER BY userID ASC';
        $result = mysqli_query($conn,$sql);

        echo "<p align='center'><font size='5px' style='font-weight: bold;'>THÔNG TIN USERS</font></p>";
        echo "<table align='center' width='1200' border='1' cellpadding='2'>";
        
        echo '
            <tr style="color: #ae1c55; font-size:18px;">
                <th width="5%">
                    Avatar User
                </th>
                <th width="35%">
                   Họ Tên User
                </th>
                <th width="20%">
                    Email
                </th>
                <th width="20%">
                    Password
                </th>
                <th width="10%">
                    Ngày Tạo
                </th>
                <th width="10%">
                    Quyền
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
                    
                    
                    $hinh_path = "Hinh_user/" . $rows['avatar'];
                    echo"<td align='center'><img src='{$hinh_path}' width='50px' alt='avt bị lỗi hoặc không có avt' /></td>";
                    
                    
                    echo"<td align='center'>{$rows['hoUser']} {$rows['tenUser']}</td>";
                    echo"<td align='center'>{$rows['email']}</td>";
                    echo"<td align='center'>{$rows['password']}</td>";
                    echo"<td align='center'>{$rows['ngayTao']}</td>";
                    echo"<td align='center'>".($rows['roleID']==1?'Admin':'User')."</td>"; 
                    // Cột Sửa
                    echo"<td align='center'><a href='sua_user.php?userID={$rows['userID']}' >Sửa</a></td>";
                    
                    // Cột Xóa
                    echo"<td align='center'><a href='xoa_user.php?userID={$rows['userID']}' >Xóa</a></td>";
                    
                echo"</tr>";
                
                if($cnt==2){
                    $cnt = 0;
                }
            }
        }
        echo "</table>";
        
        mysqli_close($conn);
     ?>
      <div style="text-align:center; margin-top:20px;">
        <a href="index.php" style="text-decoration:none; font-weight:bold; color:#555;">&laquo; Quay Về Dashboard</a>
     </div>
</body>
</html>