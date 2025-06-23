<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];
    $staffID = $_SESSION['userID'];

    $stmt = $conn->prepare("DELETE FROM schedule WHERE orderID = ? AND staffID = ?");
    $stmt->bind_param("ss", $orderID, $staffID);

    if ($stmt->execute()) {
        header("Location: staffSchedule.php?msg=done");
        exit();
    } else {
        echo "Error deleting schedule.";
    }
} else {
    echo "Invalid request.";
}
?>
