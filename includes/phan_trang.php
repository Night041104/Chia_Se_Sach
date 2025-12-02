<?php
    include('db_connect.php');
    Class Phan_trang
    {
        private $rowsPerPage;
        public function __construct($rowsPerPage)
        {
            $this->rowsPerPage = $rowsPerPage;
        }
        public function getRowsPerPage(){
            return $this->rowsPerPage;
        }
        public function getLimitPage(){
            if(!isset($_GET['page']))
            {
                $_GET['page']=1;
            }
            $offset = ($_GET['page']-1)*$this->rowsPerPage;
            return $offset.",".$this->rowsPerPage;
        }
        public function paging($sql_query)
{
    $numRows = mysqli_num_rows($sql_query);
    $maxPage = floor($numRows/$this->rowsPerPage) + 1;                
    if(!isset($_GET['page']))
    {
        $_GET['page']=1;
    }

    // CSS phân trang
    echo "<style>
    .pagination-wrapper {
        text-align: center;
        margin-top: 20px;
        font-family: Arial, sans-serif;
    }
    .pagination-wrapper a, .pagination-wrapper b {
        display: inline-block;
        margin: 0 4px;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
        transition: background-color 0.3s, color 0.3s;
    }
    .pagination-wrapper b {
        background-color: #ae1c55;
        color: white;
        font-weight: bold;
        border-color: #ae1c55;
    }
    .pagination-wrapper a:hover {
        background-color: #f5a9c1;
        color: white;
    }
    </style>";

    // tạo link tương ứng tới các trang
    echo "<div class='pagination-wrapper'>";
    if($_GET['page'] > 1)
    {
        echo "<a href=" .$_SERVER['PHP_SELF']."?page=1><<</a> ";
        echo "<a href=" .$_SERVER['PHP_SELF']."?page=".($_GET['page']-1)."><</a> ";           
    }           
    for ($i=1 ; $i<=$maxPage ; $i++)
    { 
        if ($i == $_GET['page'])
        { 
            echo "<b>$i</b> "; //trang hiện tại sẽ được bôi đậm
        }
        else
            echo "<a href=" .$_SERVER['PHP_SELF']. "?page=".$i.">$i</a> ";
    }
    if($_GET['page'] < $maxPage)    
    {
        echo "<a href=". $_SERVER['PHP_SELF']."?page=".($_GET['page']+1).">></a> ";
        echo "<a href=". $_SERVER['PHP_SELF']."?page=$maxPage>>></a> ";
    }
    echo "</div>";
}

    }
?>