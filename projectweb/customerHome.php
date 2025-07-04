<?php
session_start();

// Redirect to login if not logged in or not a customer
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Home</title>
  <link rel="stylesheet" href="customer.css"/>
</head>
<style>
  body {
    background: url("image/customerbg-new.png");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }
</style>

<body class="customer">

    <nav class="navbar">
      <div class="nav-left">
        <a href="customerHome.php" class="nav-item active">HOME</a>
        <a href="knowledgeHub.html" class="nav-item ">KNOWLEDGE<br>HUB</a>
        <a href="requestService.php" class="nav-item ">REQUEST<br>SERVICE</a>
      </div>

      <div class="nav-center">
        <img src="image/logo.png" class="logo" alt="Logo" /></a>
      </div>

      <div class="nav-right">
        <a href="mySchedule.php" class="nav-item">MY SCHEDULE</a>
        <a href="custProfile.php" class="nav-item">PROFILE</a>

      </div>
    </nav>

    <div class="header">
      <p class="greeting">Hi, <?php echo htmlspecialchars($_SESSION['userID']); ?>.</p>
      <h1>WELCOME TO CENT2RY FIRE<br/>EXTINGUISHER SERVICES SYSTEM!</h1>
      <p class="subtext">
        Stay safe and compliant with our all-in-one Fire Extinguisher Services System. 
        Access expert fire safety tips, request services in seconds and track your service
        schedule—all from one easy-to-use platform.
      </p>
      <button class="logout-btn" onclick="handleLogOut()">LOG OUT</button>
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
