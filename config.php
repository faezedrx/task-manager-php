<?php
$host = 'localhost';
$db = 'bamboos1_services_portf'; 
$user = 'root'; 
$pass = ''; 

// اتصال به پایگاه داده
$mysqli = new mysqli($host, $user, $pass, $db);

// بررسی اتصال
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// تنظیم مجموعه کاراکترها برای پایگاه داده
$mysqli->set_charset("utf8mb4");
?>
