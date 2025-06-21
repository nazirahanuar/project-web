<?php
session_start();
include 'connect.php';

$staffID = $_SESSION['userID'] ?? '';

// Fetch staff info
$stmt = $conn->prepare("SELECT * FROM staff WHERE staffID = ?");
$stmt->bind_param("s", $staffID);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Profile</title>
  <link rel="stylesheet" href="editStaffProfile.css" />
</head>
<body>
  <nav class="navbar">
    <img src="image/logo.PNG" class="logo" alt="logo">
    <div class="nav-links">
      <a href="staffHome.php">HOME</a>
      <a href="staffSchedule.html">SERVICE SCHEDULE</a>
      <a href="staffProfile.php" class="active">PROFILE</a>
    </div>
  </nav>

  <h1 class="title">EDIT PROFILE</h1>

  <div class="profile-card">
    <form action="updateStaffProfile.php" method="POST" enctype="multipart/form-data">
      <div class="profile-pic">
        <label for="profilePic">Add<br>Profile<br>Picture</label>
        <input type="file" name="profilePic" id="profilePic" hidden />
      </div>

      <p class="staff-id"><em>Staff ID: <?php echo htmlspecialchars($data['staffID']); ?></em></p>

      <label>Staff Name:</label>
      <input type="text" name="staffName" value="<?php echo htmlspecialchars($data['staffName']); ?>" required>

      <label>Gender:</label>
      <div class="gender-row">
        <label><input type="radio" name="gender" value="Male" <?php if ($data['Gender'] === 'Male') echo 'checked'; ?>> Male</label>
        <label><input type="radio" name="gender" value="Female" <?php if ($data['Gender'] === 'Female') echo 'checked'; ?>> Female</label>
      </div>

      <label>No. Tel:</label>
      <input type="text" name="tel" value="<?php echo htmlspecialchars($data['noTel']); ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($data['Email']); ?>" required>

      <label>Change Password:</label>
      <input type="password" name="password" placeholder="Enter new password">

      <div class="btn-row">
        <button type="button" onclick="window.location.href='staffProfile.php'">CANCEL</button>
        <button type="submit">SAVE</button>
      </div>
    </form>
  </div>
</body>
</html>
