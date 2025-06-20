<?php
include 'connect.php';

// --- Premise ---
$premiseError = $premiseSuccess = "";
if (isset($_POST['add_premise'])) {
  $pid = trim($_POST['premiseId']);
  $ptype = trim($_POST['premiseType']);
  if ($pid && $ptype) {
    $check = $conn->prepare("SELECT premiseID FROM premise WHERE premiseID = ?");
    $check->bind_param("s", $pid);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
      $premiseError = "Premise ID already exists.";
    } else {
      $stmt = $conn->prepare("INSERT INTO premise (premiseID, premiseType) VALUES (?, ?)");
      $stmt->bind_param("ss", $pid, $ptype);
      $stmt->execute();
      $premiseSuccess = "Premise added.";
    }
  } else {
    $premiseError = "Please fill in all premise fields.";
  }
}
if (isset($_POST['delete_premise'])) {
  $pid = $_POST['delete_premise'];
  $conn->query("DELETE FROM premise WHERE premiseID = '$pid'");
}

// --- Service ---
$serviceError = $serviceSuccess = "";
if (isset($_POST['add_service'])) {
  $sid = trim($_POST['serviceId']);
  $stype = trim($_POST['serviceType']);
  if ($sid && $stype) {
    $check = $conn->prepare("SELECT serviceID FROM service WHERE serviceID = ?");
    $check->bind_param("s", $sid);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
      $serviceError = "Service ID already exists.";
    } else {
      $stmt = $conn->prepare("INSERT INTO service (serviceID, serviceType) VALUES (?, ?)");
      $stmt->bind_param("ss", $sid, $stype);
      $stmt->execute();
      $serviceSuccess = "Service added.";
    }
  } else {
    $serviceError = "Please fill in all service fields.";
  }
}
if (isset($_POST['delete_service'])) {
  $sid = $_POST['delete_service'];
  $conn->query("DELETE FROM service WHERE serviceID = '$sid'");
}

$premises = $conn->query("SELECT * FROM premise");
$services = $conn->query("SELECT * FROM service");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Information Management</title>
  <link rel="stylesheet" href="adminFormat.css" />
</head>
<body class="admin">
  <nav class="navbar">
    <div class="nav-left">
      <a href="adminHome.html" class="nav-item">HOME</a>
      <a href="request_management.html" class="nav-item">REQUEST<br>MANAGEMENT</a>
      <a href="scheduling.html" class="nav-item">SCHEDULING</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="information_management.php" class="nav-item active">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>
<body>

  <div class="card-box premise-box">
    <h2>Premise Details</h2>
    <?php if ($premiseError) echo "<p class='error'>$premiseError</p>"; ?>
    <?php if ($premiseSuccess) echo "<p class='success'>$premiseSuccess</p>"; ?>
    <form method="POST">
      <input type="text" name="premiseId" placeholder="Premise ID">
      <input type="text" name="premiseType" placeholder="Type">
      <button name="add_premise">Add</button>
    </form>
    <table id="premiseTable">
      <tr><th>ID</th><th>Type</th><th>Action</th></tr>
      <?php while ($row = $premises->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['premiseID']) ?></td>
          <td><?= htmlspecialchars($row['premiseType']) ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Delete this premise?');" style="display:inline;">
              <input type="hidden" name="delete_premise" value="<?= htmlspecialchars($row['premiseID']) ?>">
              <button type="submit" class="delete-btn">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <hr>
  <div class="card-box service-box">
    <h2>Service Details</h2>
    <?php if ($serviceError) echo "<p class='error'>$serviceError</p>"; ?>
    <?php if ($serviceSuccess) echo "<p class='success'>$serviceSuccess</p>"; ?>
    <form method="POST">
      <input type="text" name="serviceId" placeholder="Service ID">
      <input type="text" name="serviceType" placeholder="Type">
      <button name="add_service">Add</button>
    </form>
    <table id="serviceTable">
      <tr><th>ID</th><th>Type</th><th>Action</th></tr>
      <?php while ($row = $services->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['serviceID']) ?></td>
          <td><?= htmlspecialchars($row['serviceType']) ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Delete this service?');" style="display:inline;">
              <input type="hidden" name="delete_service" value="<?= htmlspecialchars($row['serviceID']) ?>">
              <button type="submit" class="delete-btn">Delete</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

</body>
</html>
