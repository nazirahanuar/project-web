<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffID = $_POST['staffID'];
    $orderID = $_POST['orderID'];

    $stmt = $conn->prepare("UPDATE schedule SET status = 'Done' WHERE staffID = ? AND orderID = ?");
    $stmt->bind_param("ss", $staffID, $orderID);

    if ($stmt->execute()) {
        echo "<script>alert('Task marked as done.'); window.location.href='staffSchedule.php';</script>";
    } else {
        echo "<script>alert('Failed to mark task as done.'); window.history.back();</script>";
    }
}
?>
