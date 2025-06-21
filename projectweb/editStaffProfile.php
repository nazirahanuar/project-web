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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Staff Profile</title>
  <link rel="stylesheet" href="staff.css" />
</head>
<body class="staff">
  <h2 class="profile-title">EDIT PROFILE</h2>
  <div class="profile-card">
    <form action="updateStaffProfile.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">
      <input type="hidden" name="staffID" value="<?= $staff['staffID'] ?>">

      <img src="<?= $staff['profilePic'] ? $staff['profilePic'] : 'image/default_profile.png' ?>" class="profile-pic" alt="Profile Picture" />
      <label>Change Profile Picture:</label>
      <input type="file" name="profilePic" accept="image/*"><br><br>

      <label for="staffName">Staff Name:</label>
      <input type="text" id="staffName" name="staffName" value="<?= htmlspecialchars($staff['staffName']) ?>" required>

      <label for="Gender">Gender:</label>
      <select id="Gender" name="Gender" required>
        <option value="Male" <?= $staff['Gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $staff['Gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
      </select>

      <label for="noTel">No. Tel:</label>
      <input type="text" id="noTel" name="noTel" value="<?= htmlspecialchars($staff['noTel']) ?>" required>

      <label for="Email">Email:</label>
      <input type="email" id="Email" name="Email" value="<?= htmlspecialchars($staff['Email']) ?>" required>

      <label for="password">New Password (leave blank to keep current):</label>
      <input type="password" id="password" name="password">

      <button type="submit" class="edit-btn">Save Changes</button>
      <a href="staffProfile.php" class="cancel-btn">Cancel</a>
    </form>
  </div>
</body>
</html>
