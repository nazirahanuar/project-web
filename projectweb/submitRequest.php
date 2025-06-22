<?php
include 'connect.php';
session_start();

try {
    // Fetch POST data
    $customerID    = $_POST['customerID'];
    $serviceID     = $_POST['serviceID'];
    $premiseID     = $_POST['premiseID'];
    $preferredDate = $_POST['preferredDate'];
    $quantity      = isset($_POST['quantity']) && $_POST['quantity'] !== '' ? (int)$_POST['quantity'] : 0;
    $location      = $_POST['location'];
    $notes         = $_POST['notes'];

    // Basic validation
    if (empty($customerID) || empty($serviceID) || empty($premiseID) || empty($preferredDate) || empty($location)) {
        echo "<script>alert('Please fill in all required fields correctly.'); window.history.back();</script>";
        exit;
    }

    // Optional: check if service and premise exist
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
    $stmt->execute();

    echo "<script>alert('Service request submitted successfully!'); window.location.href='requestService.php';</script>";
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo "<script>alert('You have already submitted a request for this service.'); window.history.back();</script>";
    } else {
        echo "<script>alert('An error occurred: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>
