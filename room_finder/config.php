<?php

 // Kết nối với database

$server = "localhost";
$username = "root";
$password  = "";
$databaseName = "roomfinder";


$connect = mysqli_connect($server, $username, $password, $databaseName);
mysqli_set_charset($connect, "utf8");

?>