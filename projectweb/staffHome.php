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
      <a href="staffHome.php" class="nav-item active">HOME</a>
      <a href="staffSchedule.html" class="nav-item">SERVICE SCHEDULE</a>
      <a href="staffProfile.php" class="nav-item">PROFILE</a>
    </div>
  </nav>

  <div class="logout-wrapper">
    <button class="logout-btn" onclick="handleLogout()">LOG OUT</button>
  </div>

  <div class="header">
    <h1>HAPPY WORKING,<br/>JUNGKOOK JEON!</h1>
    <p class="subtext">View your schedule today.</p>
    <a href="staffSchedule.html" class="cta-btn">GO TO SERVICE SCHEDULE</a>
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