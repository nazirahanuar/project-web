<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staffID = $_SESSION['userID'];

// Fetch assigned schedules
$scheduleQuery = $conn->prepare("
    SELECT s.scheduleDate, s.Location, s.orderID,
           o.customerID, o.Quantity, o.Additional_Notes,
           f.fireExtinguisherType, f.expiredDate, f.status,
           sv.serviceType, p.premiseType,
           a.adminID
    FROM schedule s
    JOIN orders o ON s.orderID = o.orderID
    JOIN fire_extinguisher f ON o.serialNo = f.serialNo
    JOIN service sv ON s.serviceID = sv.serviceID
    JOIN premise p ON s.premiseID = p.premiseID
    JOIN admin a ON s.adminID = a.adminID
    WHERE s.staffID = ?
");

$scheduleQuery->bind_param("s", $staffID);
$scheduleQuery->execute();
$schedules = $scheduleQuery->get_result();

// For reference table
$premiseRef = $conn->query("SELECT premiseID, premiseType FROM premise");
$serviceRef = $conn->query("SELECT serviceID, serviceType FROM service");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Service Schedule</title>
  <link rel="stylesheet" href="staff.css" />
  
</head>
<body class="staff-dark">

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

  <div class="header">
    <h1>SERVICE SCHEDULE</h1>
    <p>View your schedule and click “DONE” as a sign that you’ve completed your task.</p>
  </div>

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
          <td><?= $row['premiseLocation'] ?></td>
          <td><?= $row['serviceID'] ?></td>
          <td><?= $row['premiseID'] ?></td>
          <td><?= $row['adminID'] ?></td>
          <td>
            <button class="done-btn" onclick="confirmDone('<?= $row['scheduleID'] ?>')">DONE</button>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <!-- POPUP -->
  <div class="popup-overlay" id="popup">
    <div class="popup-box">
      <p>Are you done with this task?</p>
      <button class="popup-yes" onclick="markDone()">YES</button>
      <button class="popup-no" onclick="closePopup()">NO</button>
    </div>
  </div>

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

  <script>
    let selectedScheduleID = "";

    function confirmDone(scheduleID) {
      selectedScheduleID = scheduleID;
      document.getElementById("popup").style.display = "flex";
    }

    function closePopup() {
      selectedScheduleID = "";
      document.getElementById("popup").style.display = "none";
    }

    function markDone() {
      window.location.href = "markDone.php?scheduleID=" + selectedScheduleID;
    }
  </script>
</body>
</html>
