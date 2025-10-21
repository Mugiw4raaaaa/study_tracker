<?php
$servername = "sqlXXX.infinityfree.com"; // check your hosting info
$username = "epiz_12345678";            // your InfinityFree username
$password = "your_db_password";         // your database password
$dbname = "epiz_12345678_yourdbname";   // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
