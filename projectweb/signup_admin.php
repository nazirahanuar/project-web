<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminID = $_POST['adminID'];
    $adminPassword = password_hash($_POST['adminPassword'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admin (adminID, adminPassword) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $adminID, $adminPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Account created!'); window.location.href='login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
