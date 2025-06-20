<?php
include 'connect.php';


$customerID    = $_POST['customerId'];
$serviceID     = $_POST['serviceId'];
$premiseID     = $_POST['premiseId'];
$preferredDate = $_POST['preferredDate'];
$quantity      = $_POST['quantity'];
$location      = $_POST['location'];
$notes         = $_POST['notes'];

if (empty($customerID) || empty($serviceID) || empty($premiseID) || empty($preferredDate) || empty($location) || $quantity < 1) {
    echo "<script>alert('Please fill in all required fields correctly.'); window.history.back();</script>";
    exit;
}

$sql = "INSERT INTO request (customerID, serviceID, premiseID, preferredDate, Quantity, Location, Additional_Notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssiss", $customerID, $serviceID, $premiseID, $preferredDate, $quantity, $location, $notes);

if ($stmt->execute()) {
    echo "<script>alert('Service request submitted successfully!'); window.location.href='requestService.html';</script>";
} else {
    echo "Error submitting request: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
