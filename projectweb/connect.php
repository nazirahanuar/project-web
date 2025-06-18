<?php
$host = "localhost";
$user = "root";
$pass = "1234"; // your MySQL password
$db = "cent2ry";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
