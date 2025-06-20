<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['userID'];

$stmt = $conn->prepare("SELECT * FROM customer WHERE customerID = ?");
$stmt->bind_param("s", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$orderResult = $conn->query("SELECT * FROM orders WHERE customerID = '$customerID' ORDER BY orderID DESC LIMIT 1");
$order = $orderResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="custProfile.css">
</head>
<body>

<!-- === NAVIGATION BAR === -->
<nav class="navbar">
  <div class="nav-left">
    <a href="customerHome.php" class="nav-item">HOME</a>
    <a href="knowledgeHub.html" class="nav-item">KNOWLEDGE HUB</a>
    <a href="requestService.html" class="nav-item">REQUEST SERVICE</a>
  </div>
  <div class="nav-center">
    <img src="image/logo.png" class="logo" alt="Logo">
  </div>
  <div class="nav-right">
    <a href="mySchedule.html#schedule-section" class="nav-item">MY SCHEDULE</a>
    <a href="custProfile.php" class="nav-item active">PROFILE</a>
  </div>
</nav>

<main>
  <?php if (isset($_GET['edit']) && $_GET['edit'] === 'true'): ?>
    <!-- === EDIT PROFILE SECTION (Customer Profile 2) === -->
    <section class="profile-section">
      <h2 class="section-title">EDIT PROFILE</h2>
      <form class="profile-card" action="updateProfile.php" method="POST" enctype="multipart/form-data">
        <div class="edit-photo">
          <label for="profilePic">
            <img src="<?= htmlspecialchars($user['profilePic'] ?? 'uploads/default.png') ?>" class="circle-pic" alt="Edit Profile Picture">
          </label>
          <input type="file" name="profilePic" id="profilePic" accept="image/*" hidden>
        </div>

        <input type="hidden" name="customerID" value="<?= htmlspecialchars($user['customerID']) ?>">

        <label>Name:</label>
        <input type="text" name="customerName" value="<?= htmlspecialchars($user['customerName']) ?>" required>

        <label>Gender:</label>
        <div class="radio-group">
          <label><input type="radio" name="Gender" value="Male" <?= $user['Gender'] === 'Male' ? 'checked' : '' ?>> Male</label>
          <label><input type="radio" name="Gender" value="Female" <?= $user['Gender'] === 'Female' ? 'checked' : '' ?>> Female</label>
        </div>

        <label>No. Tel:</label>
        <input type="text" name="NoTel" value="<?= htmlspecialchars($user['noTel']) ?>" required>

        <label>Email:</label>
        <input type="email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>" required>

        <label>Change Password:</label>
        <input type="password" name="password" placeholder="Optional">

        <div class="button-row">
          <a href="custProfile.php" class="cancel-btn">CANCEL</a>
          <button type="submit" class="save-btn">SAVE</button>
        </div>
      </form>
    </section>

  <?php else: ?>
    <!-- === VIEW PROFILE SECTION (Customer Profile 1) === -->
    <section class="profile-section">
      <h2 class="section-title">YOUR PROFILE</h2>
      <div class="profile-card">
        <img src="<?= htmlspecialchars($user['profilePic'] ?? 'uploads/default.png') ?>" class="profile-pic" alt="Profile Picture">
        <p class="customer-id"><strong>Customer ID:</strong> <?= htmlspecialchars($user['customerID']) ?></p>
        <hr>
        <div class="profile-info">
          <p><strong>Name:</strong> <?= htmlspecialchars($user['customerName']) ?></p>
          <p><strong>Gender:</strong> <?= htmlspecialchars($user['Gender']) ?></p>
          <p><strong>No. Tel:</strong> <?= htmlspecialchars($user['noTel']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
        </div>
        <a href="custProfile.php?edit=true" class="edit-btn">Edit Profile</a>
      </div>
    </section>

    <!-- === ORDER SECTION === -->
    <section class="order-section">
      <h2 class="section-title">YOUR ORDER</h2>
      <div class="order-box">
        <?php if ($order): ?>
          <p><strong>Order ID:</strong> <?= htmlspecialchars($order['orderID']) ?></p>
          <p><strong>Serial No.:</strong> <?= htmlspecialchars($order['serialNo'] ?? '-') ?></p>
          <p><strong>Customer ID:</strong> <?= htmlspecialchars($user['customerID']) ?></p>
          <p><strong>Quantity of Fire Extinguisher:</strong> <?= htmlspecialchars($order['quantity']) ?></p>
          <p><strong>Additional Notes:</strong> <?= htmlspecialchars($order['notes'] ?? '-') ?></p>
        <?php else: ?>
          <p>No orders found.</p>
        <?php endif; ?>
      </div>
      <p class="schedule-hint">Ready to track your schedule? Go to <a href="mySchedule.html#schedule-section"><strong>MY SCHEDULE</strong></a></p>
    </section>
  <?php endif; ?>
</main>

</body>
</html>
