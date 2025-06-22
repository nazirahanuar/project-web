<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$staffID = $_SESSION['userID'];
$query = $conn->prepare("SELECT * FROM staff WHERE staffID = ?");
$query->bind_param("s", $staffID);
$query->execute();
$result = $query->get_result();
$staff = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Profile</title>
  <link rel="stylesheet" href="staffProfile.css">
</head>
<body class="staff">

  <nav class="navbar">
    <div class="nav-left">
      <img src="image/logo.PNG" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="staffHome.php" class="nav-item">HOME</a>
      <a href="staffSchedule.html" class="nav-item">SERVICE SCHEDULE</a>
      <a href="staffProfile.php" class="nav-item active">PROFILE</a>
    </div>
  </nav>

  <section class="profile-section">
    <h2 class="profile-title">YOUR PROFILE</h2>

    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-pic">
            <?php if (!empty($staff['profilePic'])): ?>
            <img src="<?php echo htmlspecialchars($staff['profilePic']); ?>" alt="Profile Picture"
            style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;" />
        <?php else: ?>
    <div style="width: 80px; height: 80px; background: #ccc; border-radius: 50%;"></div>
  <?php endif; ?>
</div>

        <div class="profile-info">
          <p><strong>Staff ID: <?php echo $staff['staffID']; ?></strong></p>
          <p>Staff Name: <?php echo $staff['staffName']; ?></p>
        </div>
      </div>

      <div class="profile-button">
        <a href="editStaffProfile.php" class="edit-btn">Edit Profile</a>
      </div>

      <hr>

      <div class="profile-details">
        <p><strong>STAFF DETAILS:</strong></p>
        <p><strong>Gender:</strong> <?php echo $staff['Gender']; ?></p>
        <p><strong>No. Tel:</strong> <?php echo $staff['noTel']; ?></p>
        <p><strong>Email:</strong> <?php echo $staff['Email']; ?></p>
      </div>
    </div>
  </section>

</body>
</html>
