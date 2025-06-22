<?php
include 'connect.php';

// Fetch data using correct POST names matching the form
$customerID    = $_POST['customerId'];
$serviceID     = $_POST['serviceId'];
$premiseID     = $_POST['premiseId'];
$preferredDate = $_POST['preferredDate'];
$quantity      = $_POST['quantity'];
$location      = $_POST['location'];
$notes         = $_POST['notes'];

// Basic validation
if (empty($customerID) || empty($serviceID) || empty($premiseID) || empty($preferredDate) || empty($location) || $quantity < 0) {
    echo "<script>alert('Please fill in all required fields correctly.'); window.history.back();</script>";
    exit;
}

// Optional: Check if serviceID and premiseID exist in their respective tables to prevent FK error
$checkService = $conn->prepare("SELECT serviceID FROM service WHERE serviceID = ?");
$checkService->bind_param("s", $serviceID);
$checkService->execute();
$serviceExists = $checkService->get_result()->num_rows > 0;

$checkPremise = $conn->prepare("SELECT premiseID FROM premise WHERE premiseID = ?");
$checkPremise->bind_param("s", $premiseID);
$checkPremise->execute();
$premiseExists = $checkPremise->get_result()->num_rows > 0;

if (!$serviceExists || !$premiseExists) {
    echo "<script>alert('Invalid Service Type or Premise Type selected.'); window.history.back();</script>";
    exit;
}

// Insert request
$sql = "INSERT INTO request (customerID, serviceID, premiseID, preferredDate, Quantity, Location, Additional_Notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssiss", $customerID, $serviceID, $premiseID, $preferredDate, $quantity, $location, $notes);

if ($stmt->execute()) {
    echo "<script>alert('Service request submitted successfully!'); window.location.href='requestService.html';</script>";
} else {
    echo "<script>alert('Error submitting request: " . addslashes($stmt->error) . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
