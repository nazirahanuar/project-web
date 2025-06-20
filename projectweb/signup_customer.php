<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerID = trim($_POST['customerID']);
    $email = trim($_POST['email']);
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $customerName = $firstName . ' ' . $lastName;
    $gender = $_POST['gender'];
    $tel = trim($_POST['tel']);
    $passwordRaw = trim($_POST['password']);

    // Basic validations
    if (empty($customerID) || empty($email) || empty($customerName) || empty($gender) || empty($tel) || empty($passwordRaw)) {
        echo "<script>alert('Please fill in all required fields.'); window.location.href='signup_customer.php';</script>";
        exit();
    }

    // Check if customerID already exists
    $check = $conn->prepare("SELECT customerID FROM customer WHERE customerID = ?");
    $check->bind_param("s", $customerID);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Customer ID already exists. Please choose another.'); window.location.href='signup_customer.php';</script>";
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
            echo "<script>alert('Failed to upload profile picture.'); window.location.href='signup_customer.php';</script>";
            exit();
        }
    }

    $hashedPassword = password_hash($passwordRaw, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO customer (customerID, customerName, Gender, noTel, Email, profilePic, customerPassword) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $customerID, $customerName, $gender, $tel, $email, $profilePicPath, $hashedPassword);


    if ($stmt->execute()) {
        echo "<script>alert('Customer account created successfully!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.location.href='signup_customer.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Sign Up</title>
  <link rel="stylesheet" href="userFormat.css" />
</head>
<body>
  <nav class="navbar">
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
  </nav>

  <div class="user-box">
    <h2>CUSTOMER SIGN UP</h2>
    <form id="customerForm" method="POST" action="" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group">
          <label>Customer ID</label>
          <input type="text" name="customerID" required />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="firstName" required />
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="lastName" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Gender</label>
          <select name="gender" required>
            <option value="">Select</option>
            <option>Male</option>
            <option>Female</option>
          </select>
        </div>
        <div class="form-group">
          <label>No. Tel</label>
          <input type="tel" name="tel" required />
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Create Password</label>
          <input type="password" name="password" required />
        </div>
        <div class="form-group">
          <label>Profile Picture</label>
          <input type="file" name="profile" accept="image/*" />
        </div>
      </div>

      <button type="submit">SIGN UP</button>
      <p class="signup-text">Already have an account? <a href="login.php">Log In</a></p>
    </form>
  </div>
</body>
</html>
