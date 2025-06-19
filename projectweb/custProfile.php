<?php
session_start();
include 'connect.php';

// Ensure the user is logged in and is a customer
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit();
}

$customerID = $_SESSION['userID'];

// Fetch customer details
$stmt = $conn->prepare("SELECT * FROM customer WHERE customerID = ?");
$stmt->bind_param("s", $customerID);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch most recent order (optional)
$orderResult = $conn->query("SELECT * FROM orders WHERE customerID = '$customerID' ORDER BY orderID DESC LIMIT 1");
$order = $orderResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Profile</title>
  <link rel="stylesheet" href="customer.css"/>
</head>
<body class="customer">

  <!-- NAVIGATION -->
  <nav class="navbar">
    <div class="nav-left">
      <a href="customerHome.html" class="nav-item">HOME</a>
      <a href="knowledgeHub.html" class="nav-item">KNOWLEDGE<br>HUB</a>
      <a href="requestService.html" class="nav-item">REQUEST<br>SERVICE</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="mySchedule.html" class="nav-item">MY SCHEDULE</a>
      <a href="custProfile.php" class="nav-item active">PROFILE</a>
    </div>
  </nav>

  <!-- PROFILE SECTION -->
  <div class="profile-section">
    <h2 class="profile-title">YOUR PROFILE</h2>
    <div class="profile-card">
      <img src="<?= htmlspecialchars($user['profilePic'] ?? 'uploads/default.png') ?>" class="profile-pic" alt="Profile Picture" />
      <p><strong>Customer ID:</strong> <?= htmlspecialchars($user['customerID']) ?></p>
      <a href="editProfile.php" class="edit-profile-btn">Edit Profile</a>
      <hr/>
      <div class="profile-details">
        <p><strong>First Name:</strong> <?= htmlspecialchars($user['firstName']) ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($user['lastName']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($user['Gender']) ?></p>
        <p><strong>No. Tel:</strong> <?= htmlspecialchars($user['NoTel']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
      </div>
    </div>
  </div>

  <!-- ORDER SECTION -->
  <div class="order-section">
    <h2 class="profile-title">YOUR ORDER</h2>
    <div class="order-box">
      <?php if ($order): ?>
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['orderID']) ?></p>
        <p><strong>Serial No.:</strong> <?= htmlspecialchars($order['serialNo'] ?? '-') ?></p>
        <p><strong>Customer ID:</strong> <?= htmlspecialchars($order['customerID']) ?></p>
        <p><strong>Quantity of Fire Extinguisher:</strong> <?= htmlspecialchars($order['quantity']) ?></p>
        <p><strong>Additional Notes:</strong> <?= htmlspecialchars($order['notes']) ?: '-' ?></p>
      <?php else: ?>
        <p>No recent orders found.</p>
      <?php endif; ?>
    </div>
    <p class="schedule-note">Ready to track your schedule? Go to <a href="mySchedule.html"><strong>MY SCHEDULE</strong></a></p>
  </div>

</body>
</html>
