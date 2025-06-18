<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST['adminID'];
    $password = password_hash($_POST['adminPassword'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admin (admin_id, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $admin_id, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Account created!'); window.location.href='login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
