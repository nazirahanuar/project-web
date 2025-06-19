<?php
session_start();
include 'connect.php';

// Check if the logged-in user is a customer
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'customer') {
  header("Location: login.php");
  exit();
}

$customerID = $_SESSION['userID'];

// Get customer info
$stmt = $conn->prepare("SELECT * FROM customer WHERE customerID = ?");
$stmt->bind_param("s", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get latest order (if any)
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
  <!-- Navbar -->
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

  <?php if (isset($_GET['edit']) && $_GET['edit'] === 'true'): ?>
    <!-- Edit Profile View -->
    <div class="profile-section">
      <h2 class="profile-title">EDIT PROFILE</h2>
      <form action="updateProfile.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">
        <img src="<?= htmlspecialchars($user['profilePic'] ?? 'uploads/default.png') ?>" class="profile-pic" alt="Profile Picture" />
        <input type="file" name="profilePic" accept="image/*"><br><br>

        <input type="hidden" name="customerID" value="<?= htmlspecialchars($user['customerID']) ?>">

        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($user['firstName']) ?>" required>

        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($user['lastName']) ?>" required>

        <label for="Gender">Gender:</label>
        <select id="Gender" name="Gender" required>
          <option value="Male" <?= $user['Gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= $user['Gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
        </select>

        <label for="NoTel">No. Tel:</label>
        <input type="text" id="NoTel" name="NoTel" value="<?= htmlspecialchars($user['NoTel']) ?>" required>

        <label for="Email">Email:</label>
        <input type="email" id="Email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>" required>

        <button type="submit" class="save-profile-btn">Save Changes</button>
        <a href="custProfile.php" class="cancel-btn">Cancel</a>
      </form>
    </div>
  <?php else: ?>
    <!-- View Profile -->
    <div class="profile-section">
      <h2 class="profile-title">YOUR PROFILE</h2>
      <div class="profile-card">
        <img src="<?= htmlspecialchars($user['profilePic'] ?? 'uploads/default.png') ?>" class="profile-pic" alt="Profile Picture" />
        <p><strong>Customer ID:</strong> <?= htmlspecialchars($user['customerID']) ?></p>
        <a href="custProfile.php?edit=true" class="edit-profile-btn">Edit Profile</a>
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
  <?php endif; ?>

  <!-- Order Section -->
  <div class="order-section">
    <h2 class="profile-title">YOUR ORDER</h2>
    <div class="order-box">
      <?php if ($order): ?>
        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['orderID']) ?></p>
        <p><strong>Serial No.:</strong> <?= htmlspecialchars($order['serialNo'] ?? '-') ?></p>
        <p><strong>Customer ID:</strong> <?= htmlspecialchars($order['customerID']) ?></p>
        <p><strong>Quantity of Fire Extinguisher:</strong> <?= htmlspecialchars($order['quantity']) ?></p>
        <p><strong>Additional Notes:</strong> <?= htmlspecialchars($order['notes'] ?? '-') ?></p>
      <?php else: ?>
        <p>No orders found.</p>
      <?php endif; ?>
    </div>
    <p class="schedule-note">Ready to track your schedule? Go to <a href="mySchedule.html"><strong>MY SCHEDULE</strong></a></p>
  </div>
</body>
</html>
