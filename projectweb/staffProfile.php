<?php
session_start();
include 'connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staffID = $_SESSION['userID'];

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE staffID = ?");
$stmt->bind_param("s", $staffID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $name = $row['staffName'];
    $gender = $row['Gender'];
    $tel = $row['noTel'];
    $email = $row['Email'];
    $pic = $row['profilePic'];
} else {
    echo "Staff not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff Profile</title>
  <link rel="stylesheet" href="staff.css" />
</head>
<body class="staff">
  <nav class="navbar">
    <div class="nav-left">
      <a href="staffHome.html" class="nav-item">HOME</a>
      <a href="staffSchedule.html" class="nav-item">SERVICE SCHEDULE</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.PNG" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="staffProfile.php" class="nav-item active">PROFILE</a>
      <button class="logout-btn" onclick="handleLogout()">LOG OUT</button>
    </div>
  </nav>

  <h2 class="profile-title">YOUR PROFILE</h2>
  <div class="profile-card">
    <div class="profile-header">
      <img src="<?= $pic ? $pic : 'image/default_profile.png' ?>" class="profile-pic" alt="Profile Picture" />
      <div>
        <p><strong>Staff ID:</strong> <?= $staffID ?></p>
        <p><strong>Staff Name:</strong> <?= htmlspecialchars($name) ?></p>
      </div>
    </div>

    <form action="editStaffProfile.php" method="get">
      <button type="submit" class="edit-btn">Edit Profile</button>
    </form>

    <hr>
    <div class="profile-details">
      <p><strong>Gender:</strong> <?= htmlspecialchars($gender) ?></p>
      <p><strong>No. Tel:</strong> <?= htmlspecialchars($tel) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
    </div>
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
