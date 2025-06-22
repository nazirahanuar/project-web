<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$customerID = $_POST['customerID'];
$customerName = $_POST['customerName'];
$gender = $_POST['gender'];
$noTel = $_POST['NoTel'];
$email = $_POST['Email'];
$password = $_POST['password'];

// === Handle profile picture upload ===
$profilePicPath = null;
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = basename($_FILES['profilePic']['name']);
    $uniqueName = time() . '_' . $filename;
    $targetPath = $uploadDir . $uniqueName;

    if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetPath)) {
        $profilePicPath = $targetPath;
    }
}

// === Build update query ===
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE customer 
            SET customerName = ?, Gender = ?, noTel = ?, Email = ?, customerPassword = ?, 
                profilePic = IFNULL(?, profilePic)
            WHERE customerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $customerName, $gender, $noTel, $email, $hashedPassword, $profilePicPath, $customerID);
} else {
    $sql = "UPDATE customer 
            SET customerName = ?, Gender = ?, noTel = ?, Email = ?, 
                profilePic = IFNULL(?, profilePic)
            WHERE customerID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $customerName, $gender, $noTel, $email, $profilePicPath, $customerID);
}

// === Execute and redirect ===
if ($stmt->execute()) {
    header("Location: custProfile.php");
    exit();
} else {
    echo "Failed to update profile: " . $stmt->error;
}
?>
