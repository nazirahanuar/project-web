<?php
$host = "localhost";
$user = "cent2ry";
$pass = "1234"; 
$db = "student_cent2ry";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
