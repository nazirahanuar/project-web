<?php
include 'connect.php';

if (isset($_POST['orderID'])) {
    $orderID = strtoupper(trim($_POST['orderID']));

    $stmt = $conn->prepare("SELECT * FROM schedule WHERE orderID = ?");
    $stmt->bind_param("s", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $schedule = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "data" => $schedule
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Order ID not found"
        ]);
    }
}
?>
