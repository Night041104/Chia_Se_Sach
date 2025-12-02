<?php
    $conn =  mysqli_connect('localhost','root','','chiasesach') OR die('Could not connected to MySQL:'.mysqli_connect_error());
    mysqli_set_charset($conn, 'UTF8');
?>