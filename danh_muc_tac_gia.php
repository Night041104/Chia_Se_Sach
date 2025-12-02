<?php # Script danh_muc_tac_gia.php
include ('includes/db_connect.php');
$matg = '';
if (isset($_GET['matg'])) {
    $matg = $_GET['matg'];
}

$author_name = "Không rõ"; 
if (!empty($matg)) {
    $sql_title = "SELECT TenTG FROM tacgia WHERE MaTG = '$matg'";
    $result_title = mysqli_query($conn, $sql_title);
    if (mysqli_num_rows($result_title) > 0) {
        $row_title = mysqli_fetch_array($result_title);
        $author_name = $row_title['TenTG']; 
    }
}

$page_title = 'Tác giả: ' . $author_name; 
include ("includes/header.php"); 

?>

<h1 class="tentheloatacgia" align="center">Sách của tác giả: <?php echo $author_name; ?></h1>

<?php
if (empty($matg)) { 
    echo "<p align='center'>Vui lòng chọn một tác giả.</p>"; 
} else {
    $sql_motaTG = "SELECT MoTaTG from tacgia where MaTG = '$matg'";
    $result_mota = mysqli_query($conn, $sql_motaTG);
    $row = mysqli_fetch_array($result_mota);
    echo "<h4 class='motatacgia' style='text-align:justify;'>{$row['MoTaTG']}</h4>";
    $sql_books = "SELECT s.MaSach, s.TenSach, s.Hinh 
                  FROM sach s
                  INNER JOIN sach_tacgia stg ON s.MaSach = stg.MaSach
                  WHERE stg.MaTG = '$matg'
                  ORDER BY s.TenSach ASC";
                  
    $result_books = mysqli_query($conn, $sql_books);
    if (mysqli_num_rows($result_books) > 0) {
        
        echo "<div class='book-grid' align='center'>";
        while ($row = mysqli_fetch_array($result_books)) {
            $hinh_path = "Admin/Hinh_sach/" . $row['Hinh'];
            $chi_tiet_sach = "chi_tiet_sach.php?masach=" . $row['MaSach'];
            echo "<a href='{$chi_tiet_sach}'>";
            echo "<div class='book-cell'>";
            echo "<img src='{$hinh_path}' alt='" . $row['TenSach'] . "' />";
            echo "<div class='book-title'>" . $row['TenSach'] . "</div>";         
            echo "</div>"; 
            echo "</a>"; 
        }
        echo "</div>"; 

    } else {
        echo "<p align='center'>Không tìm thấy sách nào của tác giả này.</p>"; 
    }
}
mysqli_close($conn);
include ("includes/footer.html"); 
?>