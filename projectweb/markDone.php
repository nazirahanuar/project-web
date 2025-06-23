<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];

    // Step 1: Get serialNo before deleting the order
    $serialStmt = $conn->prepare("SELECT serialNo FROM orders WHERE orderID = ?");
    $serialStmt->bind_param("s", $orderID);
    $serialStmt->execute();
    $serialStmt->bind_result($serialNo);
    $serialStmt->fetch();
    $serialStmt->close();

    // Step 2: Delete from schedule
    $deleteSchedule = $conn->prepare("DELETE FROM schedule WHERE orderID = ?");
    $deleteSchedule->bind_param("s", $orderID);
    $deleteSchedule->execute();
    $deleteSchedule->close();

    // Step 3: Delete from orders
    $deleteOrder = $conn->prepare("DELETE FROM orders WHERE orderID = ?");
    $deleteOrder->bind_param("s", $orderID);
    $deleteOrder->execute();
    $deleteOrder->close();

    // Step 4: Delete fire extinguisher if serialNo exists
    if (!empty($serialNo)) {
        $deleteFE = $conn->prepare("DELETE FROM fire_extinguisher WHERE serialNo = ?");
        $deleteFE->bind_param("s", $serialNo);
        $deleteFE->execute();
        $deleteFE->close();
    }
}

// Redirect back
header("Location: staffSchedule.php");
exit();
?>
