<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffID = $_POST['staffID'];
    $name = trim($_POST['staffName']);
    $gender = $_POST['Gender'];
    $tel = trim($_POST['noTel']);
    $email = trim($_POST['Email']);
    $newPass = $_POST['password'];

    // Get current picture
    $stmt = $conn->prepare("SELECT profilePic FROM staff WHERE staffID = ?");
    $stmt->bind_param("s", $staffID);
    $stmt->execute();
    $result = $stmt->get_result();
    $old = $result->fetch_assoc();
    $profilePic = $old['profilePic'];

    // Handle new picture upload
    if (!empty($_FILES['profilePic']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES["profilePic"]["name"]);
        $targetPath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $targetPath)) {
            $profilePic = $targetPath;
        }
    }

    // Handle password update
    if (!empty($newPass)) {
        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE staff SET staffName=?, Gender=?, noTel=?, Email=?, profilePic=?, staffPassword=? WHERE staffID=?");
        $stmt->bind_param("sssssss", $name, $gender, $tel, $email, $profilePic, $hashedPass, $staffID);
    } else {
        $stmt = $conn->prepare("UPDATE staff SET staffName=?, Gender=?, noTel=?, Email=?, profilePic=? WHERE staffID=?");
        $stmt->bind_param("ssssss", $name, $gender, $tel, $email, $profilePic, $staffID);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully'); window.location.href='staffProfile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile'); window.history.back();</script>";
    }
}
?>
