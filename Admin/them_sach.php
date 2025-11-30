<?php
include ('../includes/admin_protection.php'); 
include ('../includes/db_connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sách</title>
</head>
<style>
    /* Style giữ nguyên như bạn yêu cầu */
    th{
        font-size: 30px;
        background-color: #ae1c55;
        color: white;
    }
    table {
        background-color: #fddedc;
    }
    td{
        padding: 5px;
    }
    /* Cập nhật class này */
    .checkbox-list {
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #ccc;
        padding: 10px; /* Tăng padding chút cho thoáng */
        background-color: white;

        /* --- [MỚI] CẤU HÌNH CHIA CỘT --- */
        display: grid;
        /* Dòng dưới nghĩa là: Chia làm 2 cột bằng nhau */
        grid-template-columns: 1fr 1fr; 
        
        /* Nếu muốn 3 cột thì dùng dòng này: */
        /* grid-template-columns: 1fr 1fr 1fr; */

        /* Khoảng cách giữa các ô (Hàng dọc - Hàng ngang) */
        gap: 5px 15px; 
    }

    /* Cập nhật style cho từng dòng label */
    .checkbox-list label {
        display: flex;       /* Để căn chỉnh checkbox và chữ thẳng hàng */
        align-items: center; /* Căn giữa theo chiều dọc */
        
        border-bottom: 1px dashed #eee;
        padding: 4px 0;
        cursor: pointer;
        font-size: 14px; /* Chỉnh cỡ chữ cho vừa mắt */
        
        /* Đảm bảo nội dung không bị vỡ nếu tên quá dài */
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .checkbox-list label:hover {
        background-color: #f9f9f9;
        color: #ae1c55; /* Đổi màu chữ khi di chuột vào cho đẹp */
    }
    
    /* Chỉnh lại input checkbox một chút cho đẹp */
    .checkbox-list input[type="checkbox"] {
        margin-right: 8px; /* Tạo khoảng cách giữa ô tích và tên */
        transform: scale(1.1); /* Phóng to ô tích lên xíu cho dễ bấm */
    }
</style>
<body>
    
    <form method="post" name="themSach" action="" enctype="multipart/form-data">
        <table align="center" width="700">
            <tr>
                <th colspan="2">THÊM SÁCH MỚI</th>
            </tr>
            <tr>
                <td><label>Mã sách:</label></td>
                <td><input type="text" name="masach" style="width:300px;" value="<?php if(isset($_POST['masach'])) echo $_POST['masach']?>"></td>
            </tr>
            <tr>
                <td><label>Tên sách:</label></td>
                <td><input type="text" name="tensach" style="width:300px;" value="<?php if(isset($_POST['tensach'])) echo $_POST['tensach']?>"></td>
            </tr>

            <tr>
                <td valign="top"><label>Tác giả:<br>(Chọn ít nhất 1)</label></td>
                <td>
                    <div class="checkbox-list">
                        <?php
                            $sql_tg = 'SELECT MaTG, TenTG FROM tacgia ORDER BY TenTG ASC';
                            $result_tg = mysqli_query($conn, $sql_tg);
                            if(mysqli_num_rows($result_tg) > 0) {
                                while($row_tg = mysqli_fetch_array($result_tg)) {
                                    $checked = '';
                                    // Giữ lại lựa chọn nếu form bị lỗi
                                    if(isset($_POST['tacgia']) && in_array($row_tg['MaTG'], $_POST['tacgia'])) $checked = 'checked';
                                    
                                    echo "<label><input type='checkbox' name='tacgia[]' value='{$row_tg['MaTG']}' $checked> {$row_tg['TenTG']}</label>";
                                }
                            }
                        ?>
                    </div>
                </td>
            </tr>
            
            <tr>
                <td valign="top"><label>Thể loại:<br>(Chọn ít nhất 1)</label></td>
                <td>
                    <div class="checkbox-list">
                        <?php
                            $sql_tl = 'SELECT MaTheLoai, TenTheLoai FROM theloai ORDER BY TenTheLoai ASC';
                            $result_tl = mysqli_query($conn, $sql_tl);
                            if(mysqli_num_rows($result_tl) > 0) {
                                while($row_tl = mysqli_fetch_array($result_tl)) {
                                    $checked = '';
                                    if(isset($_POST['theloai']) && in_array($row_tl['MaTheLoai'], $_POST['theloai'])) $checked = 'checked';
                                    
                                    echo "<label><input type='checkbox' name='theloai[]' value='{$row_tl['MaTheLoai']}' $checked> {$row_tl['TenTheLoai']}</label>";
                                }
                            }
                        ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td><label>Mô tả:</label></td>
                <td><textarea name="mota" rows="4" style="width:300px;"><?php if(isset($_POST['mota'])) echo $_POST['mota']?></textarea></td>
            </tr>
            
            <tr>
                <td><label>Tình trạng:</label></td>
                <td>
                    <select name="tinhtrang" style="padding: 5px; width:300px;">
                        <option value="Đang tiến hành">Đang tiến hành</option>
                        <option value="Đã hoàn thành">Đã hoàn thành</option>
                        <option value="Tạm ngưng">Tạm ngưng</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><label>Hình ảnh bìa:</label></td>
                <td><input type="file" name="hinh"></td>
            </tr>
            
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" name="them" value="Thêm mới" style="padding:5px 20px; font-weight:bold; cursor:pointer;">
                </td>
            </tr>
        </table>
    </form>

    <?php
        // --- XỬ LÝ PHP ---
        if(isset($_POST["them"]))
        {
            // 1. Kết nối CSDL
            if (!$conn) {
                $conn = mysqli_connect("localhost","root","","chiasesach") or die("Không kết nối được MySQL");
                mysqli_set_charset($conn, 'UTF8');
            }

            // 2. Lấy dữ liệu và Xử lý ký tự đặc biệt (SQL Injection)
            $masach = mysqli_real_escape_string($conn, $_POST["masach"]);
            $tensach = mysqli_real_escape_string($conn, $_POST["tensach"]);
            $mota = mysqli_real_escape_string($conn, $_POST["mota"]);
            $tinhtrang = $_POST["tinhtrang"];
            
            // Lấy mảng checkbox (Nếu không chọn thì là mảng rỗng)
            $tacgia_arr = isset($_POST["tacgia"]) ? $_POST["tacgia"] : []; 
            $theloai_arr = isset($_POST["theloai"]) ? $_POST["theloai"] : []; 

            $hinh_name = $_FILES["hinh"]["name"];
            
            // 3. KIỂM TRA RỖNG (Validation)
            // empty($tacgia_arr) sẽ trả về true nếu mảng rỗng => Bắt buộc phải chọn
            if(empty($masach) || empty($tensach) || empty($mota) || empty($tacgia_arr) || empty($theloai_arr) || empty($hinh_name))
            {
                echo "<p style='color:red; font-weight:bold;' align='center'>⚠️ Vui lòng nhập đầy đủ thông tin (bao gồm Tác giả và Thể loại)!</p>";
            }
            else
            {
                // 4. Kiểm tra mã trùng
                $sqlCheck = "SELECT MaSach FROM sach WHERE MaSach = '$masach'";
                $resultCheck = mysqli_query($conn, $sqlCheck);

                if(mysqli_num_rows($resultCheck) > 0) {
                    echo "<p style='color:red;' align='center'>❌ Mã sách '$masach' đã tồn tại. Vui lòng chọn mã khác.</p>";
                } else {
                    
                    // 5. Xử lý Upload Ảnh
                    $hinh_tmp = $_FILES['hinh']['tmp_name'];
                    $hinh_ext = strtolower(pathinfo($hinh_name, PATHINFO_EXTENSION));
                    $expensions_hinh = array("jpeg","jpg","png");
                    
                    if(!in_array($hinh_ext, $expensions_hinh)) {
                        echo "<p style='color:red;' align='center'>❌ Lỗi: Chỉ chấp nhận ảnh bìa JPG, JPEG hoặc PNG.</p>";
                    } else {
                        $uploadPath_hinh = "Hinh_sach/" . $hinh_name; 
                        
                        if(move_uploaded_file($hinh_tmp, $uploadPath_hinh)) {
                            
                            // 6. INSERT Bảng SACH
                            $sqlInsert = "INSERT INTO sach(MaSach, TenSach, MoTa, Hinh, TinhTrang)
                                          VALUES ('$masach','$tensach','$mota','$hinh_name','$tinhtrang')";
                            
                            if(mysqli_query($conn, $sqlInsert)) {
                                
                                // 7. INSERT Bảng TRUNG GIAN
                                
                                // Thêm Tác giả
                                foreach($tacgia_arr as $matg) {
                                    mysqli_query($conn, "INSERT INTO sach_tacgia(MaSach, MaTG) VALUES ('$masach', '$matg')");
                                }
                                
                                // Thêm Thể loại
                                foreach($theloai_arr as $matl) {
                                    mysqli_query($conn, "INSERT INTO sach_theloai(MaSach, MaTheLoai) VALUES ('$masach', '$matl')");
                                }

                                echo "<p style='color:green; font-weight:bold;' align='center'>✅ Thêm sách thành công!</p>";
                                echo "<p style='text-align:center;'><a href='them_chuong.php?masach=$masach' style='color:blue; font-weight:bold; font-size:18px;'>👉 [Thêm chương cho sách này ngay]</a></p>";
                                
                                 // 8. HIỂN THỊ LẠI SÁCH VỪA THÊM
                                //Lí do sử dụng group_concat + group by + distinct:
                                //-Nếu chúng ta sử dụng join bình thường thì sẽ tạo ra các dòng dữ liệu trùng lặp như sau:
                                // MaSach,TenSach,TenTG,TenTheLoai
                                // S001,Kính Vạn Hoa,Nguyễn Nhật Ánh,Truyện dài
                                // S001,Kính Vạn Hoa,Nguyễn Nhật Ánh,Văn học
                                // S001,Kính Vạn Hoa,Nguyễn Du,Truyện dài
                                // S001,Kính Vạn Hoa,Nguyễn Du,Văn học
                                //->Dẫn đến việc khi sử dụng fetch_array sẽ in ra tận 4 cuốn sách có cùng mã sách nhưng khác tác giả hoặc thể loại
                                //->Cần dùng đến group_concat + group by + distinct
                                //1.group by s.masach: ép tất cả các dòng có cùng mã sách về 1 dòng duy nhất, như ví dụ trên thì sẽ ép 4 dòng về làm 1
                                //2.group  concat: ra lệnh cho những cột bị gộp (tác giả và thể loại) thành 1 dòng duy nhất và ngăn cách bởi dấu phẩy (SEPARATOR ', ')
                                //ví dụ: MaSach,TenSach,TacGia,TheLoai
                                    //S001,Kính Vạn Hoa,"Nguyễn Nhật Ánh, Nguyễn Du","Truyện dài, Văn học"
                                //3.distinct: đây là 1 từ khóa quan trọng, vì ta sẽ join cùng 1 lúc 2 bảng n-n, dữ liệu sẽ bị lặp lại
                                //Lấy ví dụ dễ hiểu như ta có 4 dòng dữ liệu đã ví dụ phía trên thì Nguyễn Nhật Ánh lặp lại 2 lần, Văn học lặp lại 2 lần
                                //Nếu chúng ta chỉ sử dụng group_concat + group by thì cột tác giả sau khi gộp nó sẽ có dạng như sau: "Nguyễn Nhật Ánh, Nguyễn Nhật Ánh, Nguyễn Du, Nguyễn Du"
                                //Tương tự với cột thể loại cũng bị lặp như thế
                                //->Việc sử dụng distinct là để loại bỏ các dữ liệu bị trùng lặp lại, ví dụ tác giả thì chỉ lấy những tác giá khác nhau, mỗi tác giả chỉ xuất hiện đúng 1 lần, tương tự với thể loại
                                //Lưu ý: JOIN VÀ INNER JOIN GIỐNG NHAU, xài inner join hay join tùy vô việc muốn ghi dài hay ngắn
                                $sqlDisplay = "SELECT s.*, 
                                                GROUP_CONCAT(DISTINCT tg.TenTG SEPARATOR ', ') as TacGia, 
                                                GROUP_CONCAT(DISTINCT tl.TenTheLoai SEPARATOR ', ') as TheLoai
                                            FROM sach s
                                            LEFT JOIN sach_tacgia stg ON s.MaSach = stg.MaSach
                                            LEFT JOIN tacgia tg ON stg.MaTG = tg.MaTG
                                            LEFT JOIN sach_theloai stl ON s.MaSach = stl.MaSach
                                            LEFT JOIN theloai tl ON stl.MaTheLoai = tl.MaTheLoai
                                            WHERE s.MaSach = '$masach'
                                            GROUP BY s.MaSach";
                                            
                                $resultDisplay = mysqli_query($conn, $sqlDisplay);
                                if(mysqli_num_rows($resultDisplay) > 0) 
                                { 
                                    $row = mysqli_fetch_array($resultDisplay);
                                    echo "<br>";
                                    echo "<table width='700' align='center' border='1' cellpadding='5' style='border-collapse:collapse; background-color:white;'>";
                                    echo "<tr><th colspan='2' style='background-color:#fee0c1; padding:10px'><p align='center' style='font-size:20px; font-weight:bold; color:#f86500; margin:0;'>THÔNG TIN SÁCH VỪA THÊM</p></th></tr>";
                                    echo "<tr>";
                                    echo "<td width='30%' align='center'><img src='Hinh_sach/{$row['Hinh']}' width='150px' style='border:1px solid #ddd; padding:5px;'></td>";
                                    echo "<td width='70%' valign='top' style='padding:10px'>
                                            <p style='font-size:18px; color:#ae1c55;'><b>{$row['TenSach']}</b></p>
                                            <p><b>Mã sách:</b> {$row['MaSach']}</p>
                                            <p><b>Tác giả:</b> {$row['TacGia']}</p>
                                            <p><b>Thể loại:</b> {$row['TheLoai']}</p>
                                            <p><b>Tình trạng:</b> <span style='color:green; font-weight:bold;'>{$row['TinhTrang']}</span></p>
                                            <p><b>Mô tả:</b><br>{$row['MoTa']}</p>
                                          </td>";
                                    echo "</tr>";
                                    echo "</table>";
                                }

                            } else {
                                echo "<p style='color:red;'align='center'>❌ Lỗi khi thêm vào CSDL: " . mysqli_error($conn) . "</p>";
                                // Nếu lỗi insert CSDL thì xóa ảnh vừa upload để tránh rác
                                if(file_exists($uploadPath_hinh)) unlink($uploadPath_hinh);
                            }
                        } else {
                            echo "<p style='color:red;' align='center'>❌ Không thể upload file ảnh (Lỗi quyền thư mục hoặc file quá lớn).</p>";
                        }
                    }
                }
            }
        }
        
        if(isset($conn)) mysqli_close($conn);
    ?>
    
    <div style="text-align:center; margin:30px;">
        <a href="admin_index_sach.php" style="text-decoration:none; font-weight:bold; color:#555;">&laquo; Quay Về Danh Sách</a>
    </div>
</body>
</html>