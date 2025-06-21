<?php
session_start();
include 'connect.php';

// Check if staff is logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staffID = $_SESSION['userID'];

$stmt = $conn->prepare("SELECT * FROM staff WHERE staffID = ?");
$stmt->bind_param("s", $staffID);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Staff Profile</title>
  <link rel="stylesheet" href="staff.css" />
</head>
<body class="staff">

  <!-- Navigation -->
  <nav class="staff-profile-navbar">
    <div class="staff-profile-nav-left">
      <a href="staffHome.php" class="nav-item">HOME</a>
      <a href="staffSchedule.php" class="nav-item">SERVICE SCHEDULE</a>
    </div>
    <div class="staff-profile-nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="staff-profile-nav-right">
      <a href="staffProfile.php" class="nav-item active">PROFILE</a>
      <div class="logout-wrapper">
        <button class="logout-btn" onclick="logout()">LOG OUT</button>
      </div>
    </div>
  </nav>

  <!-- Profile Section -->
  <div class="staff-profile-section">
    <h2 class="staff-profile-title">YOUR PROFILE</h2>
    <div class="staff-profile-card">
      <div class="staff-profile-header">
        <img src="<?= htmlspecialchars($staff['profilePic'] ?? 'uploads/default.png') ?>" class="profile-pic" alt="Profile Picture" />
        <div class="staff-profile-info">
          <p><strong>Staff ID:</strong> <?= htmlspecialchars($staff['staffID']) ?></p>
          <p><strong>Name:</strong> <?= htmlspecialchars($staff['staffName']) ?></p>
        </div>
      </div>

      <div class="profile-button">
        <a href="editStaffProfile.php" class="edit-btn">Edit Profile</a>
      </div>

      <div class="staff-profile-details">
        <p><strong>Gender:</strong> <?= htmlspecialchars($staff['Gender']) ?></p>
        <p><strong>No. Tel:</strong> <?= htmlspecialchars($staff['NoTel']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($staff['Email']) ?></p>
      </div>
    </div>
  </div>

  <script>
    function logout() {
      if (confirm("Are you sure you want to log out?")) {
        window.location.href = "logout.php";
      }
    }
  </script>
</body>
</html>
