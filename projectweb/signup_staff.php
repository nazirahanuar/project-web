<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staffID = trim($_POST['staffID']);
    $staffName = trim($_POST['staffName']);
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $tel = trim($_POST['tel']);
    $passwordRaw = trim($_POST['password']);

    // Validate required fields
    if (empty($staffID) || empty($staffName) || empty($email) || empty($gender) || empty($tel) || empty($passwordRaw)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href='signup_staff.php';</script>";
        exit();
    }

    // Check staff ID prefix
    if (strpos($staffID, "C2RY") !== 0) {
        echo "<script>alert('Staff ID must start with \"C2RY\".'); window.location.href='signup_staff.php';</script>";
        exit();
    }

    // Check if staff ID already exists
    $check = $conn->prepare("SELECT staffID FROM staff WHERE staffID = ?");
    $check->bind_param("s", $staffID);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Staff ID already exists.'); window.location.href='signup_staff.php';</script>";
        exit();
    }

    // Handle profile picture upload
    $profilePicPath = null;
    if (!empty($_FILES['profile']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES["profile"]["name"]);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["profile"]["tmp_name"], $targetPath)) {
            $profilePicPath = $targetPath;
        } else {
            echo "<script>alert('Failed to upload profile picture.'); window.location.href='signup_staff.php';</script>";
            exit();
        }
    }

    // Hash password
    $hashedPassword = password_hash($passwordRaw, PASSWORD_DEFAULT);

    // Insert staff into DB
    $stmt = $conn->prepare("INSERT INTO staff (staffID, staffName, Gender, NoTel, Email, profilePic, staffPassword) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $staffID, $staffName, $gender, $tel, $email, $profilePicPath, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Staff account created successfully!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href='signup_staff.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff Sign Up</title>
  <link rel="stylesheet" href="userFormat.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
  </nav>

  <div class="user-box">
    <h2>STAFF SIGN UP</h2>
    <form method="POST" action="" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group">
          <label>Staff ID</label>
          <input type="text" name="staffID" required>
        </div>
        <div class="form-group">
          <label>Staff Name</label>
          <input type="text" name="staffName" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>
        <div class="form-group">
          <label>Gender</label>
          <select name="gender" required>
            <option value="">Select</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>No. Tel</label>
          <input type="text" name="tel" required>
        </div>
        <div class="form-group">
          <label>Create Password</label>
          <input type="password" name="password" required>
        </div>
      </div>

      <div class="form-row single">
        <div class="form-group">
          <label>Add Profile Picture</label>
          <input type="file" name="profile" accept="image/*">
        </div>
      </div>

      <button type="submit">Sign Up</button>
      <p class="signup-text">Already have an account? <a href="login.php">Log In</a></p>
    </form>
  </div>
  <p class="side-note">
    *NOTE: Staff ID must start with <strong>"C2RY"</strong>.
  </p>
</body>
</html>
