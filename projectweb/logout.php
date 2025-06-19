<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="3;url=login.php">
  <title>Logging Out...</title>
  <link rel="stylesheet" href="userFormat.css">

</head>
<body class="logout-page">
  <div class="logout-message">
    <h2>🔒 Logging Out...</h2>
    <p>You will be redirected to the login page shortly.</p>
  </div>
</body>
