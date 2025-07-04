<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = trim($_POST['userID']);
    $password = trim($_POST['password']);

    if (empty($userID) || empty($password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='login.php';</script>";
        exit();
    }

    $roleTables = [
        'admin' => [
            'table' => 'admin',
            'id_col' => 'adminID',
            'pass_col' => 'adminPassword',
            'redirect' => 'adminHome.php'
        ],
        'staff' => [
            'table' => 'staff',
            'id_col' => 'staffID',
            'pass_col' => 'staffPassword',
            'redirect' => 'staffHome.php'
        ],
        'customer' => [
            'table' => 'customer',
            'id_col' => 'customerID',
            'pass_col' => 'customerPassword',
            'redirect' => 'customerHome.php'
        ]
    ];

    foreach ($roleTables as $role => $info) {
        $stmt = $conn->prepare("SELECT * FROM {$info['table']} WHERE {$info['id_col']} = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row[$info['pass_col']])) {
                $_SESSION['userID'] = $userID;
                $_SESSION['role'] = $role;
                header("Location: {$info['redirect']}");
                exit();
            }
        }
    }

    echo "<script>alert('Invalid ID or Password.'); window.location.href='login.php';</script>";
}
?>

<!-- login form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Log In</title>
  <link rel="stylesheet" href="userFormat.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
  </nav>

  <div class="user-box">
    <h2>LOG IN</h2>
    <form method="POST">
      <label for="userID">ID</label>
      <input type="text" id="userID" name="userID" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required />

      <button type="submit">LOG IN</button>
    </form>
    <p class="signup-text">Don't have an account? <a href="signup.html">Sign Up</a></p>
  </div>
</body>
</html>
