<?php
session_start();
include 'connect.php';

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
  <title>Edit Staff Profile</title>
  <link rel="stylesheet" href="staff.css" />
</head>
<body class="staff">

  <nav class="staff-edit-navbar">
    <div class="staff-edit-nav-left">
      <a href="staffHome.php" class="nav-item">HOME</a>
      <a href="staffSchedule.php" class="nav-item">SERVICE SCHEDULE</a>
    </div>
    <div class="staff-edit-nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="staff-edit-nav-right">
      <a href="staffProfile.php" class="nav-item active">PROFILE</a>
    </div>
  </nav>

  <h2 class="title">EDIT PROFILE</h2>
  <div class="profile-card">
    <form action="updateStaffProfile.php" method="POST" enctype="multipart/form-data">
      <div class="profile-pic">
        <label for="profilePic">
          <?= $staff['profilePic'] ? '<img src="' . htmlspecialchars($staff['profilePic']) . '" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;" />' : 'Add<br>Picture' ?>
        </label>
        <input type="file" id="profilePic" name="profilePic" accept="image/*" hidden>
      </div>

      <div class="staff-id">Staff ID: <?= htmlspecialchars($staff['staffID']) ?></div>
      <input type="hidden" name="staffID" value="<?= htmlspecialchars($staff['staffID']) ?>">

      <label for="staffName">Name</label>
      <input type="text" name="staffName" value="<?= htmlspecialchars($staff['staffName']) ?>" required>

      <label for="Gender">Gender</label>
      <div class="gender-row">
        <label><input type="radio" name="Gender" value="Male" <?= $staff['Gender'] === 'Male' ? 'checked' : '' ?>> Male</label>
        <label><input type="radio" name="Gender" value="Female" <?= $staff['Gender'] === 'Female' ? 'checked' : '' ?>> Female</label>
      </div>

      <label for="NoTel">No. Tel</label>
      <input type="text" name="NoTel" value="<?= htmlspecialchars($staff['NoTel']) ?>" required>

      <label for="Email">Email</label>
      <input type="email" name="Email" value="<?= htmlspecialchars($staff['Email']) ?>" required>

      <div class="btn-row">
        <button type="submit">Save</button>
        <button type="button" onclick="window.location.href='staffProfile.php'">Cancel</button>
      </div>
    </form>
  </div>
</body>
</html>
