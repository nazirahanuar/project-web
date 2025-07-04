<?php
include 'connect.php';

$pendingCount = 0;
$fireExtinguisherCount = 0;

$pendingQuery = $conn->query("SELECT COUNT(*) AS total FROM request");
if ($pendingQuery) {
  $row = $pendingQuery->fetch_assoc();
  $pendingCount = isset($row['total']) ? (int)$row['total'] : 0;
}

$fireExtinguisherQuery = $conn->query("SELECT COUNT(*) AS totalFire FROM fire_extinguisher");
if ($fireExtinguisherQuery) {
  $row = $fireExtinguisherQuery->fetch_assoc();
  $fireExtinguisherCount = isset($row['totalFire']) ? (int)$row['totalFire'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Home</title>
  <link rel="stylesheet" href="adminFormat.css" />
  <style>
    body {
      background: url("image/adminbg.jpg");
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }
  </style>
</head>
<body class="admin">

  <nav class="navbar">
    <div class="nav-left">
      <a href="adminHome.php" class="nav-item active">HOME</a>
      <a href="request_management.php" class="nav-item">REQUEST<br>MANAGEMENT</a>
      <a href="scheduling.php" class="nav-item">SCHEDULING</a>
    </div>

    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>

    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="information_management.php" class="nav-item">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>

  <div class="dashboard-container">
    <div class="header">
      <h2>🔥 WELCOME, ADMIN! 🔥</h2>
      <button class="logout-btn" onclick="handleLogOut()">LOG OUT</button>
    </div>

    <div class="summary-box">
      <h2>TODAY'S UPDATE</h2>
      <div class="stats">
        <div class="stat-card">
          <div class="stat-number" id="pendingRequests"><?= $pendingCount ?></div>
          <div class="stat-label">Pending<br>Requests</div>
        </div>

        <div class="stat-card">
          <div class="stat-number" id="totalFireExtinguishers"><?= $fireExtinguisherCount ?></div>
          <div class="stat-label">Total Fire<br>Extinguishers</div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function handleLogOut() {
      const confirmLogOut = confirm("Are you sure you want to log out?");
      if (confirmLogOut) {
        window.location.href = "logout.php";
      }
    }
  </script>
</body>
</html>
