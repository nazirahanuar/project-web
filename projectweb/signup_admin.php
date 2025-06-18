<?php
include 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminID = trim($_POST['adminID']);
    $adminPasswordRaw = trim($_POST['adminPassword']);

    if (empty($adminID) || empty($adminPasswordRaw)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='signup_admin.php';</script>";
        exit();
    }

    if (strpos($adminID, "AD") !== 0) {
        echo "<script>alert('Admin ID must start with \"AD\".'); window.location.href='signup_admin.php';</script>";
        exit();
    }

    // Check if adminID already exists
    $check = $conn->prepare("SELECT adminID FROM admin WHERE adminID = ?");
    $check->bind_param("s", $adminID);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Admin ID already exists. Please choose another.'); window.location.href='signup_admin.php';</script>";
        exit();
    }

    // Hash password and insert into DB
    $adminPassword = password_hash($adminPasswordRaw, PASSWORD_DEFAULT);

    $sql = "INSERT INTO admin (adminID, adminPassword) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $adminID, $adminPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error creating account. Please try again.'); window.location.href='signup_admin.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Sign Up</title>
  <link rel="stylesheet" href="userFormat.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
  </nav>

  <div class="user-box">
    <h2>SIGN UP</h2>
    <form id="adminForm" method="POST" action="">
      <label for="adminID">Admin ID</label>
      <input type="text" name="adminID" id="adminID" required placeholder="e.g. AD12345" />

      <label for="adminPassword">Create Password</label>
      <input type="password" name="adminPassword" id="adminPassword" required placeholder="Min 6 characters" />

      <button type="submit">SIGN UP</button>
    </form>
    <p class="signup-text">
      Already have an account? <a href="login.php">Log In</a>
    </p>
    <p class="side-note">
      *NOTE: Admin ID must start with <strong>"AD"</strong>.
    </p>
  </div>
</body>
</html>
