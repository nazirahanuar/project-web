<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$query = $conn->prepare("SELECT staffName FROM staff WHERE staffID = ?");
$query->bind_param("s", $staffID);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$staffName = $row['staffName'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff Home</title>
  <link rel="stylesheet" href="staff.css" />
</head>
<body class="staff">

  <nav class="navbar">
    <div class="nav-left">
      <img src="image/logo.PNG" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <div class="nav-top-links">
        <a href="staffHome.php" class="nav-item active">HOME</a>
        <a href="staffSchedule.php" class="nav-item">SERVICE SCHEDULE</a>
        <a href="staffProfile.php" class="nav-item">PROFILE</a>
      </div>
      <button class="logout-btn" onclick="handleLogout()">LOG OUT</button>
    </div>
  </nav>

  <div class="header">
    <h1>HAPPY WORKING,<br><?php echo strtoupper($staffName); ?>!</h1>
    <p class="subtext">View your schedule today.</p>
    <a href="staffSchedule.php" class="cta-btn">GO TO SERVICE SCHEDULE</a>
  </div>

  <script>
    function handleLogout() {
      if (confirm("Are you sure you want to log out?")) {
        window.location.href = "logout.php";
      }
    }
  </script>
</body>
</html>
