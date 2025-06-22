<?php include 'connect.php'; ?>

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
      <a href="request_management.php" class="nav-item">REQUEST<br>MANAGEMENT</a>
      <a href="scheduling.php" class="nav-item">SCHEDULING</a>
    </div>
    <div class="nav-center">
      <img src="image/logo.png" class="logo" alt="Logo" />
    </div>
    <div class="nav-right">
      <a href="fireExtinguisher_information.php" class="nav-item">FIRE EXTINGUISHER<br>INFORMATION</a>
      <a href="information_management.php" class="nav-item active">INFORMATION<br>MANAGEMENT</a>
    </div>
  </nav>

<?php
// Add Premise
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_premise'])) {
  $pid = $_POST['premise_id'];
  $ptype = $_POST['premise_type'];

  $check = $conn->prepare("SELECT * FROM premise WHERE premiseID = ?");
  $check->bind_param("s", $pid);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO premise (premiseID, premiseType) VALUES (?, ?)");
    $stmt->bind_param("ss", $pid, $ptype);
    $stmt->execute();
  }
}

// Delete Premise
if (isset($_POST['delete_premise'])) {
  $deleteID = $_POST['delete_premise'];
  $stmt = $conn->prepare("DELETE FROM premise WHERE premiseID = ?");
  $stmt->bind_param("s", $deleteID);
  $stmt->execute();
}

// Add Service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
  $sid = $_POST['service_id'];
  $stype = $_POST['service_type'];

  $check = $conn->prepare("SELECT * FROM service WHERE serviceID = ?");
  $check->bind_param("s", $sid);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO service (serviceID, serviceType) VALUES (?, ?)");
    $stmt->bind_param("ss", $sid, $stype);
    $stmt->execute();
  }
}

// Delete Service
if (isset($_POST['delete_service'])) {
  $deleteID = $_POST['delete_service'];
  $stmt = $conn->prepare("DELETE FROM service WHERE serviceID = ?");
  $stmt->bind_param("s", $deleteID);
  $stmt->execute();
}
?>

<h2 class="title">VIEW AND CREATE PREMISE</h2>
<div class="container">
  <div class="create-premise">
    <h3 class="sub-title">PREMISE DETAILS</h3>
    <form method="POST">
      <label>Premise ID</label>
      <input type="text" name="premise_id" required />

      <label>Premise Type</label>
      <input type="text" name="premise_type" required />

      <button type="submit" name="add_premise">ADD</button>
    </form>

    <table class="premise-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Type</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $res = $conn->query("SELECT * FROM premise");
          while ($row = $res->fetch_assoc()) {
            echo "<tr>
              <td>{$row['premiseID']}</td>
              <td>{$row['premiseType']}</td>
              <td>
                <form method='POST' style='display:inline;'>
                  <input type='hidden' name='delete_premise' value='{$row['premiseID']}' />
                  <button type='submit' class='delete-btn'>Delete</button>
                </form>
              </td>
            </tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

<hr>
<h2 class="title">VIEW AND CREATE SERVICE</h2>
<div class="container">
  <div class="create-service">
    <h3 class="sub-title">SERVICE DETAILS</h3>
    <form method="POST">
      <label>Service ID</label>
      <input type="text" name="service_id" required />

      <label>Service Type</label>
      <input type="text" name="service_type" required />

      <button type="submit" name="add_service">ADD</button>
    </form>

    <table class="service-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Type</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $res = $conn->query("SELECT * FROM service");
          while ($row = $res->fetch_assoc()) {
            echo "<tr>
              <td>{$row['serviceID']}</td>
              <td>{$row['serviceType']}</td>
              <td>
                <form method='POST' style='display:inline;'>
                  <input type='hidden' name='delete_service' value='{$row['serviceID']}' />
                  <button type='submit' class='delete-btn'>Delete</button>
                </form>
              </td>
            </tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
