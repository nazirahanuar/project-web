<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'customer') {
    echo "<script>alert('You must log in as customer first.'); window.location.href='login.php';</script>";
    exit;
}

$customerID = $_SESSION['userID'];

$premiseResult = $conn->query("SELECT * FROM premise");
$serviceResult = $conn->query("SELECT * FROM service");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Request Service</title>
  <link rel="stylesheet" href="customer.css">
</head>
<body class="customer">

<nav class="navbar">
  <div class="nav-left">
    <a href="customerHome.php" class="nav-item">HOME</a>
    <a href="knowledgeHub.html" class="nav-item">KNOWLEDGE HUB</a>
    <a href="requestService.php" class="nav-item active">REQUEST SERVICE</a>
  </div>
  <div class="nav-center">
    <img src="image/logo.png" class="logo" alt="Logo" />
  </div>
  <div class="nav-right">
    <a href="mySchedule.html" class="nav-item">MY SCHEDULE</a>
    <a href="custProfile.php" class="nav-item">PROFILE</a>
    <a href="logout.php" class="nav-item">LOGOUT</a>
  </div>
</nav>

<h1 class="sec-title">REQUEST SERVICE</h1>

<div class="request-form">
  <form action="submitRequest.php" method="POST">
    <input type="hidden" name="customerID" value="<?= htmlspecialchars($customerID) ?>">

    <div class="form-row">
      <div class="form-group">
        <label>Customer ID</label>
        <input type="text" value="<?= htmlspecialchars($customerID) ?>" disabled>
      </div>

      <div class="form-group">
        <label for="serviceId">Service Type</label>
        <select name="serviceID" required>
          <option value="">Select Service</option>
          <?php while ($row = $serviceResult->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['serviceID']) ?>"><?= htmlspecialchars($row['serviceType']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="premiseId">Premise Type</label>
        <select name="premiseID" required>
          <option value="">Select Premise</option>
          <?php while ($row = $premiseResult->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['premiseID']) ?>"><?= htmlspecialchars($row['premiseType']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="preferredDate">Preferred Date</label>
        <input type="date" name="preferredDate" required>
      </div>
    </div>

    <div class="form-group">
      <label for="quantity">Quantity</label>
      <input type="number" name="quantity" min="0" value="0">
    </div>

    <div class="form-group">
      <label for="location">Location</label>
      <input type="text" name="location" required>
    </div>

    <div class="form-group">
      <label for="notes">Additional Notes</label>
      <textarea name="notes"></textarea>
    </div>

    <button type="submit" class="submit-btn">SUBMIT</button>
  </form>
</div>

</body>
</html>
