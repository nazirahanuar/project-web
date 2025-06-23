<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];

    // First: delete from schedule
    $deleteSchedule = $conn->prepare("DELETE FROM schedule WHERE orderID = ?");
    $deleteSchedule->bind_param("s", $orderID);
    $deleteSchedule->execute();
    $deleteSchedule->close();

    // Then: delete from orders
    $deleteOrder = $conn->prepare("DELETE FROM orders WHERE orderID = ?");
    $deleteOrder->bind_param("s", $orderID);
    $deleteOrder->execute();
    $deleteOrder->close();
}

// Redirect back
header("Location: staffSchedule.php");
exit();
?>
