<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staffID = $_SESSION['userID'];

// Main schedule list
$scheduleQuery = $conn->prepare("
    SELECT s.staffID, s.orderID, s.scheduleDate, s.Location, s.serviceID, s.premiseID, s.adminID
    FROM schedule s
    WHERE s.staffID = ?
");
$scheduleQuery->bind_param("s", $staffID);
$scheduleQuery->execute();
$schedules = $scheduleQuery->get_result();

// Order lookup
$orderDetails = null;
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $orderStmt = $conn->prepare("
        SELECT o.orderID, o.serialNo, o.customerID, o.Quantity, o.Additional_Notes,
               f.fireExtinguisherType
        FROM orders o
        JOIN fire_extinguisher f ON o.serialNo = f.serialNo
        WHERE o.orderID = ?
    ");
    $orderStmt->bind_param("s", $search);
    $orderStmt->execute();
    $orderDetails = $orderStmt->get_result()->fetch_assoc();
}

// Reference tables
$premiseRef = $conn->query("SELECT * FROM premise");
$serviceRef = $conn->query("SELECT * FROM service");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Service Schedule</title>
  <link rel="stylesheet" href="staff.css" />
</head>
<body class="staff-dark">

  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-left">
      <img src="image/logo.PNG" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <div class="nav-menu">
        <a href="staffHome.php" class="nav-item">HOME</a>
        <a href="staffSchedule.php" class="nav-item active">SERVICE SCHEDULE</a>
        <a href="staffProfile.php" class="nav-item">PROFILE</a>
      </div>
    </div>
  </nav>

  <!-- Title -->
  <div class="header">
    <h1>SERVICE SCHEDULE</h1>
    <p><em>View your schedule and click “DONE” as a sign that you’ve completed your task.</em></p>
  </div>

  <!-- Schedule Table -->
  <div class="schedule-table">
    <table border="1">
      <tr>
        <th>Staff ID</th>
        <th>Order ID</th>
        <th>Schedule Date</th>
        <th>Location</th>
        <th>Service ID</th>
        <th>Premise ID</th>
        <th>Admin ID</th>
        <th>Action</th>
      </tr>
      <?php while ($row = $schedules->fetch_assoc()): ?>
      <tr>
        <td><?= $row['staffID'] ?></td>
        <td><?= $row['orderID'] ?></td>
        <td><?= $row['scheduleDate'] ?></td>
        <td><?= $row['Location'] ?></td>
        <td><?= $row['serviceID'] ?></td>
        <td><?= $row['premiseID'] ?></td>
        <td><?= $row['adminID'] ?></td>
        <td><button class="done-btn" onclick="confirmDone('<?= $row['orderID'] ?>')">DONE</button></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <!-- Search Order Section -->
  <div class="search-section">
    <form method="GET">
      <label>Check customer's fire extinguisher(s) order by searching the Order ID.</label><br>
      <input type="text" name="search" placeholder="Search Order ID" value="<?= $_GET['search'] ?? '' ?>" />
      <button type="submit">🔍</button>
    </form>
  </div>

  <?php if ($orderDetails): ?>
  <div class="order-details-box">
    <p><strong>Order ID:</strong> <?= $orderDetails['orderID'] ?></p>
    <p><strong>Serial No.:</strong> <?= $orderDetails['serialNo'] ?></p>
    <p><strong>Customer ID:</strong> <?= $orderDetails['customerID'] ?></p>
    <p><strong>Quantity of Fire Extinguisher:</strong> <?= $orderDetails['Quantity'] ?></p>
    <p><strong>Additional Notes:</strong> <?= $orderDetails['Additional_Notes'] ?></p>
  </div>
  <?php endif; ?>

  <!-- Reference Tables -->
  <div class="reference-section">
    <h2>[ FOR REFERENCE ]</h2>
    <div class="reference-container">
      <div class="ref-box">
        <h3>PREMISE DETAILS</h3>
        <table border="1">
          <tr><th>Premise ID</th><th>Type</th></tr>
          <?php while ($p = $premiseRef->fetch_assoc()): ?>
          <tr><td><?= $p['premiseID'] ?></td><td><?= $p['premiseType'] ?></td></tr>
          <?php endwhile; ?>
        </table>
      </div>
      <div class="ref-box">
        <h3>SERVICE DETAILS</h3>
        <table border="1">
          <tr><th>Service ID</th><th>Type</th></tr>
          <?php while ($s = $serviceRef->fetch_assoc()): ?>
          <tr><td><?= $s['serviceID'] ?></td><td><?= $s['serviceType'] ?></td></tr>
          <?php endwhile; ?>
        </table>
      </div>
    </div>
  </div>

  <!-- DONE Confirmation Popup -->
  <div class="popup-overlay" id="popup">
    <div class="popup-box">
      <p>Are you done with this task?</p>
      <button class="popup-yes" onclick="markDone()">YES</button>
      <button class="popup-no" onclick="closePopup()">NO</button>
    </div>
  </div>

  <script>
    let selectedOrderID = "";

    function confirmDone(orderID) {
      selectedOrderID = orderID;
      document.getElementById("popup").style.display = "flex";
    }

    function closePopup() {
      document.getElementById("popup").style.display = "none";
      selectedOrderID = "";
    }

    function markDone() {
      window.location.href = "markDone.php?orderID=" + selectedOrderID;
    }
  </script>

</body>
</html>
