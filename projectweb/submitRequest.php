<?php
include 'connect.php';


$customerId    = $_POST['customerId'];
$serviceId     = $_POST['serviceId'];
$premiseId     = $_POST['premiseId'];
$preferredDate = $_POST['preferredDate'];
$quantity      = $_POST['quantity'];
$location      = $_POST['location'];
$notes         = $_POST['notes'];

if (empty($customerId) || empty($serviceId) || empty($premiseId) || empty($preferredDate) || empty($location) || $quantity < 1) {
    echo "<script>alert('Please fill in all required fields correctly.'); window.history.back();</script>";
    exit;
}

$sql = "INSERT INTO service_requests (customer_id, service_id, premise_id, preferred_date, quantity, location, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssiss", $customerId, $serviceId, $premiseId, $preferredDate, $quantity, $location, $notes);

if ($stmt->execute()) {
    echo "<script>alert('Service request submitted successfully!'); window.location.href='requestService.html';</script>";
} else {
    echo "Error submitting request: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
